<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanController extends Controller
{
    // GET /admin/plans
    public function index(): View
    {
        $plans = Plan::withCount('subscriptions')->orderByRaw("FIELD(tier, 'free', 'premium', 'sultan')")->get();

        return view('admin.plans.index', compact('plans'));
    }

    // GET /admin/plans/create
    public function create(): View
    {
        return view('admin.plans.form', ['plan' => null]);
    }

    // POST /admin/plans
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatePlan($request);
        $data['is_active'] = $request->has('is_active');

        Plan::create($data);

        return redirect()->route('admin.plans.index')->with('success', 'Paket berhasil dibuat.');
    }

    // GET /admin/plans/{plan}/edit
    public function edit(Plan $plan): View
    {
        return view('admin.plans.form', compact('plan'));
    }

    // PUT /admin/plans/{plan}
    public function update(Request $request, Plan $plan): RedirectResponse
    {
        $data = $this->validatePlan($request, $plan->id);
        $data['is_active'] = $request->has('is_active');

        $plan->update($data);

        return redirect()->route('admin.plans.index')->with('success', 'Paket berhasil diperbarui.');
    }

    // PATCH /admin/plans/{plan}/toggle
    public function toggle(Plan $plan): RedirectResponse
    {
        $plan->update(['is_active' => !$plan->is_active]);
        $status = $plan->is_active ? 'diaktifkan' : 'dinonaktifkan';

        return back()->with('success', "Paket {$plan->name} berhasil {$status}.");
    }

    // DELETE /admin/plans/{plan}
    public function destroy(Plan $plan): RedirectResponse
    {
        if ($plan->subscriptions()->exists()) {
            return back()->withErrors(['delete' => 'Paket tidak bisa dihapus karena masih memiliki subscriber.']);
        }

        $plan->delete();

        return back()->with('success', 'Paket berhasil dihapus.');
    }

    private function validatePlan(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'tier'                 => ['required', 'in:free,premium,sultan', 'unique:plans,tier' . ($ignoreId ? ",{$ignoreId}" : '')],
            'name'                 => ['required', 'string', 'max:50'],
            'price'                => ['required', 'numeric', 'min:0'],
            'duration_days'        => ['required', 'integer', 'min:1'],
            'max_accounts'         => ['nullable', 'integer', 'min:1'],
            'max_saving_goals'     => ['nullable', 'integer', 'min:1'],
            'max_budgets'          => ['nullable', 'integer', 'min:1'],
            'can_export'           => ['nullable'],
            'ai_rate_limit'        => ['nullable', 'integer', 'min:1'],
            'has_priority_support' => ['nullable'],
        ]);
    }
}
