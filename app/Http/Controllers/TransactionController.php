<?php

namespace App\Http\Controllers;

use App\Models\AccountTransfer;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class TransactionController extends Controller
{
    // GET /transactions
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ── Filters ──
        $filter = $request->input('filter', 'all');
        $from   = $request->input('from');
        $to     = $request->input('to');

        // ── Transactions (income + expense) ──
        $txQuery = $user->transactions()
            ->with(['category', 'account', 'attachments'])
            ->latest('transaction_date');

        if ($filter === 'income') {
            $txQuery->where('type', 'income');
        } elseif ($filter === 'expense') {
            $txQuery->where('type', 'expense');
        }

        if ($from) {
            $txQuery->whereDate('transaction_date', '>=', $from);
        }
        if ($to) {
            $txQuery->whereDate('transaction_date', '<=', $to);
        }

        // ── Transfers ──
        $accountIds = $user->accounts()->pluck('id');
        $tfQuery = AccountTransfer::whereIn('from_account_id', $accountIds)
            ->with(['fromAccount', 'toAccount'])
            ->latest('transfer_date');

        if ($from) {
            $tfQuery->whereDate('transfer_date', '>=', $from);
        }
        if ($to) {
            $tfQuery->whereDate('transfer_date', '<=', $to);
        }

        // ── Merge & paginate ──
        if ($filter === 'transfer') {
            $transactions = collect();
            $transfers    = $tfQuery->paginate(15)->withQueryString();
        } elseif ($filter === 'all') {
            $transactions = $txQuery->paginate(15)->withQueryString();
            $transfers    = $tfQuery->get();
        } else {
            $transactions = $txQuery->paginate(15)->withQueryString();
            $transfers    = collect();
        }

        // ── Summary bulan ini ──
        $incomeThisMonth = $user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        $expenseThisMonth = $user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount');

        // ── Data untuk form ──
        $accounts   = $user->accounts()->orderBy('name')->get();
        $categories = $user->categories()->orderBy('name')->get();

        return view('user.transactions.index', compact(
            'transactions',
            'transfers',
            'accounts',
            'categories',
            'filter',
            'from',
            'to',
            'incomeThisMonth',
            'expenseThisMonth'
        ));
    }

    // POST /transactions
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $type = $request->input('transaction_type');

        if ($type === 'transfer') {
            return $this->storeTransfer($request, $user);
        }

        $data = $request->validate([
            'account_id'       => ['required', 'exists:accounts,id'],
            'category_id'      => ['required', 'exists:categories,id'],
            'amount'           => ['required', 'numeric', 'min:1'],
            'type'             => ['required', 'in:income,expense'],
            'note'             => ['nullable', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'attachments'      => ['nullable', 'array', 'max:5'],
            'attachments.*'    => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        $account = $user->accounts()->findOrFail($data['account_id']);

        if ($data['type'] === 'expense' && $account->balance < $data['amount']) {
            return back()->withErrors(['amount' => 'Saldo akun tidak mencukupi.'])->withInput();
        }

        DB::transaction(function () use ($user, $data, $account, $request) {
            $transaction = $user->transactions()->create($data);

            // Upload attachments
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store("attachments/{$user->id}", 'public');
                    $transaction->attachments()->create(['file_path' => $path]);
                }
            }

            if ($data['type'] === 'income') {
                $account->increment('balance', $data['amount']);
            } else {
                $account->decrement('balance', $data['amount']);
            }
        });

        return back()->with('success', 'Transaksi berhasil ditambahkan.');
    }

    private function storeTransfer(Request $request, $user): RedirectResponse
    {
        $data = $request->validate([
            'from_account_id' => ['required', 'exists:accounts,id'],
            'to_account_id'   => ['required', 'exists:accounts,id', 'different:from_account_id'],
            'amount'          => ['required', 'numeric', 'min:1'],
            'note'            => ['nullable', 'string', 'max:255'],
            'transfer_date'   => ['required', 'date'],
        ]);

        $fromAccount = $user->accounts()->findOrFail($data['from_account_id']);
        $toAccount   = $user->accounts()->findOrFail($data['to_account_id']);

        if ($fromAccount->balance < $data['amount']) {
            return back()->withErrors(['amount' => 'Saldo akun asal tidak mencukupi.'])->withInput();
        }

        DB::transaction(function () use ($data, $fromAccount, $toAccount) {
            AccountTransfer::create($data);
            $fromAccount->decrement('balance', $data['amount']);
            $toAccount->increment('balance', $data['amount']);
        });

        return back()->with('success', 'Transfer berhasil dilakukan.');
    }

    // DELETE /transactions/{transaction}
    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;

            if ($transaction->type === 'income') {
                $account->decrement('balance', $transaction->amount);
            } else {
                $account->increment('balance', $transaction->amount);
            }

            // Hapus file attachments dari storage
            foreach ($transaction->attachments as $att) {
                Storage::disk('public')->delete($att->file_path);
            }

            $transaction->delete(); // cascadeOnDelete handles DB records
        });

        return back()->with('success', 'Transaksi berhasil dihapus.');
    }

    // DELETE /transfers/{transfer}
    public function destroyTransfer(AccountTransfer $transfer): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user       = Auth::user();
        $accountIds = $user->accounts()->pluck('id');

        if (! $accountIds->contains($transfer->from_account_id)) {
            abort(403);
        }

        DB::transaction(function () use ($transfer) {
            $transfer->fromAccount->increment('balance', $transfer->amount);
            $transfer->toAccount->decrement('balance', $transfer->amount);
            $transfer->delete();
        });

        return back()->with('success', 'Transfer berhasil dihapus.');
    }
}
