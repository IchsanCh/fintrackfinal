<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use App\Services\TierService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // GET /dashboard — user
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // ── Summary cards ──
        $stats = [
            'total_balance'      => (int) round($user->accounts()->sum('balance')),
            'account_count'      => $user->accounts()->count(),
            'income_this_month'  => (int) round($user->transactions()
                ->where('type', 'income')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount')),
            'expense_this_month' => (int) round($user->transactions()
                ->where('type', 'expense')
                ->whereMonth('transaction_date', now()->month)
                ->whereYear('transaction_date', now()->year)
                ->sum('amount')),
            'active_saving_goals' => $user->savingGoals()->where('status', 'active')->count(),
        ];

        $stats['net_this_month'] = $stats['income_this_month'] - $stats['expense_this_month'];

        // ── Line chart: trend income vs expense ──
        $trendRange = $request->input('trend', '6');
        $months = match($trendRange) {
            '3'    => 3,
            '12'   => 12,
            default => 6,
        };

        $monthlyTrend = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $m     = $date->month;
            $y     = $date->year;
            $label = $date->locale('id')->isoFormat('MMM YY');

            $income = (int) round($user->transactions()
                ->where('type', 'income')
                ->whereMonth('transaction_date', $m)
                ->whereYear('transaction_date', $y)
                ->sum('amount'));

            $expense = (int) round($user->transactions()
                ->where('type', 'expense')
                ->whereMonth('transaction_date', $m)
                ->whereYear('transaction_date', $y)
                ->sum('amount'));

            $monthlyTrend[] = [
                'label'   => $label,
                'income'  => $income,
                'expense' => $expense,
            ];
        }

        // ── Doughnut chart: expense per kategori bulan ini ──
        $expenseByCategory = $user->transactions()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->where('type', 'expense')
            ->whereMonth('transaction_date', now()->month)
            ->whereYear('transaction_date', now()->year)
            ->groupBy('category_id')
            ->with('category')
            ->get()
            ->map(fn ($t) => [
                'name'  => $t->category->name ?? '-',
                'total' => (int) round($t->total),
            ])
            ->sortByDesc('total')
            ->values();

        // ── Budget progress: top budgets ──
        $budgets = $user->budgets()
            ->with('category')
            ->where('month', now()->month)
            ->where('year', now()->year)
            ->get()
            ->map(function ($b) use ($user) {
                $spent = (int) round($user->transactions()
                    ->where('type', 'expense')
                    ->where('category_id', $b->category_id)
                    ->whereMonth('transaction_date', now()->month)
                    ->whereYear('transaction_date', now()->year)
                    ->sum('amount'));

                return (object) [
                    'category'     => $b->category,
                    'limit_amount' => $b->limit_amount,
                    'spent'        => $spent,
                    'percentage'   => $b->limit_amount > 0 ? (int) round(($spent / $b->limit_amount) * 100) : 0,
                ];
            })
            ->sortByDesc('percentage')
            ->take(4);

        // ── Saving goals aktif ──
        $savingGoals = $user->savingGoals()
            ->where('status', 'active')
            ->latest()
            ->take(4)
            ->get()
            ->map(function ($g) {
                $g->percentage = $g->target_amount > 0
                    ? (int) round(($g->current_amount / $g->target_amount) * 100)
                    : 0;
                return $g;
            });

        // ── Recent transactions ──
        $recentTransactions = $user->transactions()
            ->with(['category', 'account'])
            ->latest('transaction_date')
            ->take(5)
            ->get();

        // ── Tier info ──
        $tierSummary = TierService::getSummary($user);

        return view('user.dashboard', compact(
            'user',
            'stats',
            'monthlyTrend',
            'trendRange',
            'expenseByCategory',
            'budgets',
            'savingGoals',
            'recentTransactions',
            'tierSummary'
        ));
    }

    // GET /admin/dashboard — admin
    public function adminIndex(): View
    {
        $user = Auth::user();

        $stats = [
            'total_users' => User::where('role', 'user')->count(),
            'new_users_this_month' => User::where('role', 'user')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
            'active_users' => User::where('role', 'user')->where('status', 'active')->count(),
            'banned_users' => User::where('role', 'user')->where('status', 'banned')->count(),
            'total_transactions' => Transaction::count(),
        ];

        $recentUsers = User::where('role', 'user')->latest()->take(8)->get();

        return view('admin.dashboard', compact('user', 'stats', 'recentUsers'));
    }
}
