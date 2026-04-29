<?php

namespace App\Http\Controllers;

use App\Models\BillReminder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class BillReminderController extends Controller
{
    // GET /bill-reminders
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user   = Auth::user();
        $filter = $request->input('filter', 'all');

        $query = $user->billReminders()->where('is_active', true)->orderBy('due_date');

        $bills = $query->get()->map(function ($bill) {
            $bill->computed_status = $bill->status;
            return $bill;
        });

        if ($filter !== 'all') {
            $bills = $bills->where('computed_status', $filter)->values();
        }

        $accounts  = $user->accounts()->orderBy('name')->get();
        $overdue   = $bills->where('computed_status', 'overdue')->count();
        $dueToday  = $bills->where('computed_status', 'due_today')->count();
        $upcoming  = $bills->where('computed_status', 'upcoming')->count();
        $paid      = $bills->where('computed_status', 'paid')->count();

        return view('user.bill-reminders.index', compact(
            'bills',
            'accounts',
            'filter',
            'overdue',
            'dueToday',
            'upcoming',
            'paid'
        ));
    }

    // POST /bill-reminders
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'       => ['required', 'string', 'max:100'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'due_date'    => ['required', 'date'],
            'repeat_days' => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($data['repeat_days'])) {
            $data['repeat_days'] = null;
        }
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->billReminders()->create($data);
        // Alternatif: Auth::user()->billReminders()->create($data);

        return back()->with('success', 'Tagihan berhasil ditambahkan.');
    }

    // PUT /bill-reminders/{billReminder}
    public function update(Request $request, BillReminder $billReminder): RedirectResponse
    {
        if ($billReminder->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:100'],
            'amount'      => ['required', 'numeric', 'min:1'],
            'due_date'    => ['required', 'date'],
            'repeat_days' => ['nullable', 'integer', 'min:0'],
        ]);

        if (empty($data['repeat_days'])) {
            $data['repeat_days'] = null;
        }

        $billReminder->update($data);

        return back()->with('success', 'Tagihan berhasil diperbarui.');
    }

    // POST /bill-reminders/{billReminder}/pay
    public function pay(Request $request, BillReminder $billReminder): RedirectResponse
    {
        if ($billReminder->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
        ]);
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $account = $user->accounts()->findOrFail($data['account_id']);

        if ($account->balance < $billReminder->amount) {
            return back()->withErrors(['amount' => 'Saldo akun tidak mencukupi.']);
        }

        DB::transaction(function () use ($billReminder, $account, $user) {
            // Kurangi saldo
            $account->decrement('balance', $billReminder->amount);

            // Catat sebagai transaksi expense
            $user->transactions()->create([
                'account_id'       => $account->id,
                'category_id'      => $user->categories()->where('name', 'Tagihan')->first()?->id
                                      ?? $user->categories()->where('type', 'expense')->first()?->id,
                'amount'           => $billReminder->amount,
                'type'             => 'expense',
                'note'             => "Pembayaran: {$billReminder->title}",
                'transaction_date' => now()->toDateString(),
            ]);

            // Update last_paid_at
            $billReminder->update(['last_paid_at' => now()]);

            // Kalau recurring, geser due_date ke cycle berikutnya
            if ($billReminder->isRecurring()) {
                $billReminder->update([
                    'due_date' => $billReminder->due_date->addDays($billReminder->repeat_days),
                ]);
            }
        });

        return back()->with('success', "Tagihan \"{$billReminder->title}\" berhasil dibayar.");
    }

    // DELETE /bill-reminders/{billReminder}
    public function destroy(BillReminder $billReminder): RedirectResponse
    {
        if ($billReminder->user_id !== Auth::id()) {
            abort(403);
        }

        $billReminder->delete();

        return back()->with('success', 'Tagihan berhasil dihapus.');
    }
}
