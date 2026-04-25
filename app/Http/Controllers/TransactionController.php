<?php

namespace App\Http\Controllers;

use App\Models\AccountTransfer;
use App\Models\Transaction;
use App\Models\TransactionAttachment;
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

        $filter = $request->input('filter', 'all');
        $from   = $request->input('from');
        $to     = $request->input('to');

        $txQuery = $user->transactions()
            ->with(['category', 'account', 'attachments'])
            ->latest('transaction_date');

        if ($filter === 'income') {
            $txQuery->where('type', 'income');
        }
        if ($filter === 'expense') {
            $txQuery->where('type', 'expense');
        }
        if ($from) {
            $txQuery->whereDate('transaction_date', '>=', $from);
        }
        if ($to) {
            $txQuery->whereDate('transaction_date', '<=', $to);
        }

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

        $incomeThisMonth = (int) round($user->transactions()
            ->where('type', 'income')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount'));

        $expenseThisMonth = (int) round($user->transactions()
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->sum('amount'));

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

    // GET /transactions/{transaction}
    public function show(Transaction $transaction)
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->load(['category', 'account', 'attachments']);

        return response()->json([
            'id'               => $transaction->id,
            'type'             => $transaction->type,
            'amount'           => $transaction->amount,
            'note'             => $transaction->note,
            'transaction_date' => $transaction->transaction_date,
            'account'          => [
                'id'   => $transaction->account->id,
                'name' => $transaction->account->name,
            ],
            'category'         => [
                'id'   => $transaction->category->id,
                'name' => $transaction->category->name,
                'icon' => $transaction->category->icon,
            ],
            'attachments'      => $transaction->attachments->map(fn ($a) => [
                'id'        => $a->id,
                'file_path' => $a->file_path,
                'url'       => asset('storage/' . $a->file_path),
                'is_image'  => str_contains($a->file_path, '.jpg') || str_contains($a->file_path, '.jpeg') || str_contains($a->file_path, '.png'),
                'filename'  => basename($a->file_path),
            ]),
        ]);
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

    // PUT /transactions/{transaction}
    public function update(Request $request, Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
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
            'delete_attachments' => ['nullable', 'array'],
            'delete_attachments.*' => ['exists:transaction_attachments,id'],
        ]);
        /** @var \App\Models\User $user */
        $user = Auth::user();

        DB::transaction(function () use ($user, $data, $transaction, $request) {
            $oldAccount = $transaction->account;
            $newAccount = $user->accounts()->findOrFail($data['account_id']);

            // Rollback saldo lama
            if ($transaction->type === 'income') {
                $oldAccount->decrement('balance', $transaction->amount);
            } else {
                $oldAccount->increment('balance', $transaction->amount);
            }

            // Update transaksi
            $transaction->update($data);

            // Apply saldo baru
            if ($data['type'] === 'income') {
                $newAccount->increment('balance', $data['amount']);
            } else {
                $newAccount->decrement('balance', $data['amount']);
            }

            // Hapus attachment yang diminta
            if (! empty($data['delete_attachments'])) {
                $toDelete = TransactionAttachment::whereIn('id', $data['delete_attachments'])
                    ->where('transaction_id', $transaction->id)
                    ->get();

                foreach ($toDelete as $att) {
                    Storage::disk('public')->delete($att->file_path);
                    $att->delete();
                }
            }

            // Upload attachment baru
            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $file) {
                    $path = $file->store("attachments/{$user->id}", 'public');
                    $transaction->attachments()->create(['file_path' => $path]);
                }
            }
        });

        return back()->with('success', 'Transaksi berhasil diperbarui.');
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

            foreach ($transaction->attachments as $att) {
                Storage::disk('public')->delete($att->file_path);
            }

            $transaction->delete();
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
