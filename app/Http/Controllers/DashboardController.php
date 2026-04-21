<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // GET /dashboard — user
    public function index(): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $stats = [
            // Total saldo dari semua akun milik user
            'total_balance' => $user->accounts()->sum('balance'),

            // Jumlah akun
            'account_count' => $user->accounts()->count(),

            // Pemasukan bulan ini
            'income_this_month' => $user->transactions()
                ->where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),

            // Pengeluaran bulan ini
            'expense_this_month' => $user->transactions()
                ->where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount'),

            // Saving goals aktif
            'active_saving_goals' => $user->savingGoals()
                ->where('status', 'active')
                ->count(),
        ];

        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->take(8)
            ->get();

        return view('user.dashboard', compact('user', 'stats', 'recentTransactions'));
    }

    // GET /admin/dashboard — admin
    public function adminIndex(): View
    {
        $user = Auth::user();

        $stats = [
            // Total semua user (kecuali admin)
            'total_users' => User::where('role', 'user')->count(),

            // User baru bulan ini
            'new_users_this_month' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // User aktif
            'active_users' => User::where('role', 'user')
                ->where('status', 'active')
                ->count(),

            // User banned
            'banned_users' => User::where('role', 'user')
                ->where('status', 'banned')
                ->count(),

            // Total transaksi seluruh platform
            'total_transactions' => Transaction::count(),
        ];

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(8)
            ->get();

        return view('admin.dashboard', compact('user', 'stats', 'recentUsers'));
    }
}
