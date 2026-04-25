<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Services\TierService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    // GET /budgets
    public function index(Request $request): View
    {
        /** @var \App\Models\User $user */
        $user  = Auth::user();
        $month = (int) $request->input('month', now()->month);
        $year  = (int) $request->input('year', now()->year);

        $budgets = $user->budgets()
            ->with('category')
            ->where('month', $month)
            ->where('year', $year)
            ->get();

        // Hitung realisasi per budget dari transaksi expense
        $budgetData = $budgets->map(function ($budget) use ($user, $month, $year) {
            $spent = (int) round($user->transactions()
                ->where('type', 'expense')
                ->where('category_id', $budget->category_id)
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->sum('amount'));

            $percentage = $budget->limit_amount > 0
                ? round(($spent / $budget->limit_amount) * 100)
                : 0;

            return (object) [
                'id'           => $budget->id,
                'category'     => $budget->category,
                'limit_amount' => $budget->limit_amount,
                'spent'        => $spent,
                'remaining'    => $budget->limit_amount - $spent,
                'percentage'   => $percentage,
                'status'       => $percentage >= 100 ? 'over' : ($percentage >= 70 ? 'warning' : 'safe'),
            ];
        });

        $totalBudget = $budgetData->sum('limit_amount');
        $totalSpent  = $budgetData->sum('spent');

        $tierSummary = TierService::getSummary($user);

        // Ambil kategori expense yang belum ada budget di bulan ini
        $usedCategoryIds = $budgets->pluck('category_id');
        $availableCategories = $user->categories()
            ->where('type', 'expense')
            ->whereNotIn('id', $usedCategoryIds)
            ->orderBy('name')
            ->get();

        return view('user.budgets.index', compact(
            'budgetData',
            'totalBudget',
            'totalSpent',
            'month',
            'year',
            'tierSummary',
            'availableCategories'
        ));
    }

    // POST /budgets
    public function store(Request $request): RedirectResponse
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (! TierService::canAddBudget($user)) {
            $plan = TierService::getActivePlan($user);
            return back()->withErrors([
                'limit' => "Paket {$plan->name} hanya bisa memiliki maksimal {$plan->max_budgets} budget. Upgrade untuk menambah.",
            ]);
        }

        $data = $request->validate([
            'category_id'  => ['required', 'exists:categories,id'],
            'limit_amount' => ['required', 'numeric', 'min:1'],
            'month'        => ['required', 'integer', 'between:1,12'],
            'year'         => ['required', 'integer', 'min:2020'],
        ]);

        // Cek duplikat: satu kategori per bulan
        $exists = $user->budgets()
            ->where('category_id', $data['category_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if ($exists) {
            return back()->withErrors(['category_id' => 'Budget untuk kategori ini sudah ada di bulan yang sama.']);
        }

        $user->budgets()->create($data);

        return redirect()
            ->route('budgets.index', ['month' => $data['month'], 'year' => $data['year']])
            ->with('success', 'Budget berhasil ditambahkan.');
    }

    // PUT /budgets/{budget}
    public function update(Request $request, Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $data = $request->validate([
            'limit_amount' => ['required', 'numeric', 'min:1'],
        ]);

        $budget->update($data);

        return redirect()
            ->route('budgets.index', ['month' => $budget->month, 'year' => $budget->year])
            ->with('success', 'Budget berhasil diperbarui.');
    }

    // DELETE /budgets/{budget}
    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $month = $budget->month;
        $year  = $budget->year;

        $budget->delete();

        return redirect()
            ->route('budgets.index', ['month' => $month, 'year' => $year])
            ->with('success', 'Budget berhasil dihapus.');
    }
}
