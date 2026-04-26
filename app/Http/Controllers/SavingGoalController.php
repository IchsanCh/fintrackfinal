<?php

namespace App\Http\Controllers;

use App\Models\SavingGoal;
use App\Models\SavingTransaction;
use App\Services\TierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SavingGoalController extends Controller
{
    // GET /saving-goals
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $goals = $user->savingGoals()
            ->withCount('savingTransactions')
            ->latest()
            ->get()
            ->map(function ($goal) {
                $goal->percentage = $goal->target_amount > 0
                    ? (int) round(($goal->current_amount / $goal->target_amount) * 100)
                    : 0;
                $goal->remaining = max(0, $goal->target_amount - $goal->current_amount);
                $goal->days_left = $goal->deadline
                    ? (int) max(0, now()->diffInDays($goal->deadline, false))
                    : null;
                return $goal;
            });

        $activeGoals    = $goals->where('status', 'active');
        $achievedGoals  = $goals->where('status', 'achieved');
        $completedGoals = $goals->where('status', 'completed');
        $cancelledGoals = $goals->where('status', 'cancelled');

        $totalSaved  = (int) round($goals->whereIn('status', ['active', 'achieved'])->sum('current_amount'));
        $totalTarget = (int) round($goals->whereIn('status', ['active', 'achieved'])->sum('target_amount'));

        $accounts    = $user->accounts()->orderBy('name')->get();
        $tierSummary = TierService::getSummary($user);

        return view('user.saving-goals.index', compact(
            'activeGoals',
            'achievedGoals',
            'completedGoals',
            'cancelledGoals',
            'totalSaved',
            'totalTarget',
            'accounts',
            'tierSummary'
        ));
    }

    // POST /saving-goals
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! TierService::canAddSavingGoal($user)) {
            $plan = TierService::getActivePlan($user);
            return back()->withErrors([
                'limit' => "Paket {$plan->name} hanya bisa memiliki maksimal {$plan->max_saving_goals} tabungan. Upgrade untuk menambah.",
            ]);
        }

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'deadline'      => ['nullable', 'date', 'after:today'],
        ]);

        $user->savingGoals()->create($data);

        return back()->with('success', 'Target tabungan berhasil dibuat.');
    }

    // PUT /saving-goals/{savingGoal}
    public function update(Request $request, SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:100'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'deadline'      => ['nullable', 'date'],
        ]);

        $savingGoal->update($data);

        // Cek otomatis apakah sudah tercapai
        if ($savingGoal->current_amount >= $savingGoal->target_amount && $savingGoal->status === 'active') {
            $savingGoal->update(['status' => 'achieved']);
        }

        return back()->with('success', 'Target tabungan berhasil diperbarui.');
    }

    // POST /saving-goals/{savingGoal}/deposit
    public function deposit(Request $request, SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }
        if ($savingGoal->status !== 'active') {
            return back()->withErrors(['status' => 'Tabungan ini sudah tidak aktif.']);
        }

        $data = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount'     => ['required', 'numeric', 'min:1'],
            'note'       => ['nullable', 'string', 'max:255'],
        ]);

        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $account = $user->accounts()->findOrFail($data['account_id']);

        if ($account->balance < $data['amount']) {
            return back()->withErrors(['amount' => 'Saldo akun tidak mencukupi.']);
        }

        DB::transaction(function () use ($savingGoal, $data, $account) {
            // Kurangi saldo akun
            $account->decrement('balance', $data['amount']);

            // Tambah current_amount
            $savingGoal->increment('current_amount', $data['amount']);

            // Simpan saving transaction
            SavingTransaction::create([
                'saving_goal_id' => $savingGoal->id,
                'account_id'     => $data['account_id'],
                'amount'         => $data['amount'],
                'type'           => 'deposit',
                'note'           => $data['note'] ?? null,
            ]);

            // Cek apakah sudah tercapai
            $savingGoal->refresh();
            if ($savingGoal->current_amount >= $savingGoal->target_amount) {
                $savingGoal->update(['status' => 'achieved']);
            }
        });

        $msg = $savingGoal->status === 'achieved'
            ? "Selamat! Target tabungan \"{$savingGoal->title}\" tercapai! 🎉"
            : 'Deposit berhasil ditambahkan.';

        return back()->with('success', $msg);
    }

    // POST /saving-goals/{savingGoal}/withdraw
    public function withdraw(Request $request, SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }
        if ($savingGoal->status === 'cancelled') {
            return back()->withErrors(['status' => 'Tabungan ini sudah dibatalkan.']);
        }

        $data = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
            'amount'     => ['required', 'numeric', 'min:1'],
            'note'       => ['nullable', 'string', 'max:255'],
        ]);

        if ($savingGoal->current_amount < $data['amount']) {
            return back()->withErrors(['amount' => 'Saldo tabungan tidak mencukupi.']);
        }
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $account = $user->accounts()->findOrFail($data['account_id']);

        DB::transaction(function () use ($savingGoal, $data, $account) {
            $account->increment('balance', $data['amount']);
            $savingGoal->decrement('current_amount', $data['amount']);

            SavingTransaction::create([
                'saving_goal_id' => $savingGoal->id,
                'account_id'     => $data['account_id'],
                'amount'         => $data['amount'],
                'type'           => 'withdraw',
                'note'           => $data['note'] ?? null,
            ]);

            // Kalau saldo turun di bawah target, kembalikan ke active
            $savingGoal->refresh();
            if ($savingGoal->current_amount < $savingGoal->target_amount && $savingGoal->status === 'achieved') {
                $savingGoal->update(['status' => 'active']);
            }
        });

        return back()->with('success', 'Penarikan berhasil.');
    }

    // POST /saving-goals/{savingGoal}/cashout — Selesai & cairkan semua dana
    public function cashout(Request $request, SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }
        if ($savingGoal->status !== 'achieved') {
            return back()->withErrors(['status' => 'Hanya tabungan yang sudah tercapai yang bisa dicairkan.']);
        }
        if ($savingGoal->current_amount <= 0) {
            return back()->withErrors(['amount' => 'Tidak ada dana untuk dicairkan.']);
        }

        $data = $request->validate([
            'account_id' => ['required', 'exists:accounts,id'],
        ]);
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $account = $user->accounts()->findOrFail($data['account_id']);
        $amount  = $savingGoal->current_amount;

        DB::transaction(function () use ($savingGoal, $data, $account, $amount) {
            $account->increment('balance', $amount);

            SavingTransaction::create([
                'saving_goal_id' => $savingGoal->id,
                'account_id'     => $data['account_id'],
                'amount'         => $amount,
                'type'           => 'withdraw',
                'note'           => 'Pencairan dana — target tercapai',
            ]);

            $savingGoal->update([
                'current_amount' => 0,
                'status'         => 'completed',
            ]);
        });

        return back()->with('success', "Dana Rp " . number_format($amount, 0, ',', '.') . " dari \"{$savingGoal->title}\" berhasil dicairkan! 🎉");
    }

    // PATCH /saving-goals/{savingGoal}/cancel
    public function cancel(SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $savingGoal->update(['status' => 'cancelled']);

        return back()->with('success', 'Target tabungan dibatalkan.');
    }

    // DELETE /saving-goals/{savingGoal}
    public function destroy(SavingGoal $savingGoal): RedirectResponse
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }

        if ($savingGoal->current_amount > 0) {
            return back()->withErrors(['delete' => 'Tabungan masih memiliki saldo. Tarik semua dana terlebih dahulu.']);
        }

        $savingGoal->delete();

        return back()->with('success', 'Target tabungan berhasil dihapus.');
    }

    // GET /saving-goals/{savingGoal}/history (JSON)
    public function history(SavingGoal $savingGoal)
    {
        if ($savingGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $transactions = $savingGoal->savingTransactions()
            ->with('account')
            ->latest()
            ->get()
            ->map(fn ($t) => [
                'id'         => $t->id,
                'type'       => $t->type,
                'amount'     => $t->amount,
                'note'       => $t->note,
                'account'    => $t->account->name ?? '-',
                'created_at' => $t->created_at->locale('id')->isoFormat('D MMM YYYY · HH:mm'),
            ]);

        return response()->json([
            'goal'         => [
                'title'          => $savingGoal->title,
                'target_amount'  => $savingGoal->target_amount,
                'current_amount' => $savingGoal->current_amount,
                'status'         => $savingGoal->status,
            ],
            'transactions' => $transactions,
        ]);
    }
}
