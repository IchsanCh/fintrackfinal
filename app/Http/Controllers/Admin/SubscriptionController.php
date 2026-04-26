<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubscriptionController extends Controller
{
    // GET /admin/subscriptions
    public function index(Request $request): View
    {
        $query = Subscription::with(['user', 'plan'])->latest();

        // Search user
        if ($search = $request->input('search')) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter status
        if ($status = $request->input('status')) {
            if ($status === 'active') {
                $query->where('status', 'active')
                      ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>', now()));
            } elseif ($status === 'expired') {
                $query->where(
                    fn ($q) =>
                    $q->where('status', 'expired')
                      ->orWhere(fn ($q2) => $q2->where('status', 'active')->whereNotNull('expired_at')->where('expired_at', '<=', now()))
                );
            } elseif ($status === 'cancelled') {
                $query->where('status', 'cancelled');
            }
        }

        $subscriptions = $query->paginate(15)->withQueryString();

        $totalActive = Subscription::where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expired_at')->orWhere('expired_at', '>', now()))
            ->count();
        $totalExpired = Subscription::where(
            fn ($q) =>
            $q->where('status', 'expired')
              ->orWhere(fn ($q2) => $q2->where('status', 'active')->whereNotNull('expired_at')->where('expired_at', '<=', now()))
        )->count();
        $totalCancelled = Subscription::where('status', 'cancelled')->count();

        $plans = Plan::where('is_active', true)->orderByRaw("FIELD(tier, 'free', 'premium', 'sultan')")->get();

        return view('admin.subscriptions.index', compact(
            'subscriptions',
            'search',
            'status',
            'totalActive',
            'totalExpired',
            'totalCancelled',
            'plans'
        ));
    }

    // PATCH /admin/subscriptions/{subscription}/extend
    public function extend(Request $request, Subscription $subscription): RedirectResponse
    {
        $data = $request->validate([
            'days' => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $base = $subscription->expired_at && $subscription->expired_at->isFuture()
            ? $subscription->expired_at
            : now();

        $subscription->update([
            'expired_at' => $base->addDays($data['days']),
            'status'     => 'active',
        ]);

        return back()->with('success', "Subscription {$subscription->user->name} diperpanjang {$data['days']} hari.");
    }

    // PATCH /admin/subscriptions/{subscription}/cancel
    public function cancel(Subscription $subscription): RedirectResponse
    {
        $subscription->update(['status' => 'cancelled']);

        return back()->with('success', "Subscription {$subscription->user->name} dibatalkan.");
    }

    // POST /admin/subscriptions/assign
    public function assign(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'plan_id' => ['required', 'exists:plans,id'],
            'days'    => ['required', 'integer', 'min:1', 'max:365'],
        ]);

        $plan = Plan::findOrFail($data['plan_id']);

        // Deactivate existing active subscription
        Subscription::where('user_id', $data['user_id'])
            ->where('status', 'active')
            ->update(['status' => 'expired']);

        Subscription::create([
            'user_id'    => $data['user_id'],
            'plan_id'    => $data['plan_id'],
            'status'     => 'active',
            'started_at' => now(),
            'expired_at' => $plan->tier === 'free' ? null : now()->addDays($data['days']),
        ]);

        return back()->with('success', "Paket {$plan->name} berhasil diberikan.");
    }
}
