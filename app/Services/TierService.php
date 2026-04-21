<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;

/**
 * TierService — Single source of truth untuk semua logic limit tier.
 *
 * Semua pengecekan limit fitur harus lewat service ini,
 * bukan langsung akses plan dari controller.
 */
class TierService
{
    /**
     * Ambil plan aktif user.
     * Kalau tidak ada subscription aktif, fallback ke plan Free.
     */
    public static function getActivePlan(User $user): Plan
    {
        $subscription = $user->activeSubscription;

        if ($subscription) {
            return $subscription->plan;
        }

        // Fallback ke Free plan
        return Plan::where('tier', 'free')->firstOrFail();
    }

    /**
     * Cek apakah user boleh menambah akun baru.
     */
    public static function canAddAccount(User $user): bool
    {
        $plan = self::getActivePlan($user);

        if (is_null($plan->max_accounts)) {
            return true;
        }

        return $user->accounts()->count() < $plan->max_accounts;
    }

    /**
     * Cek apakah user boleh menambah saving goal baru.
     */
    public static function canAddSavingGoal(User $user): bool
    {
        $plan = self::getActivePlan($user);

        if (is_null($plan->max_saving_goals)) {
            return true;
        }

        return $user->savingGoals()->count() < $plan->max_saving_goals;
    }

    /**
     * Cek apakah user boleh menambah budget baru.
     */
    public static function canAddBudget(User $user): bool
    {
        $plan = self::getActivePlan($user);

        if (is_null($plan->max_budgets)) {
            return true;
        }

        return $user->budgets()->count() < $plan->max_budgets;
    }

    /**
     * Cek apakah user boleh ekspor laporan.
     */
    public static function canExport(User $user): bool
    {
        return self::getActivePlan($user)->can_export;
    }

    /**
     * Cek apakah user masih dalam batas rate limit AI bulan ini.
     */
    public static function canUseAi(User $user): bool
    {
        $plan = self::getActivePlan($user);

        // Unlimited
        if (is_null($plan->ai_rate_limit)) {
            return true;
        }

        $usedThisMonth = $user->aiUsageLogs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return $usedThisMonth < $plan->ai_rate_limit;
    }

    /**
     * Ambil sisa kuota AI user bulan ini.
     * Return null jika unlimited.
     */
    public static function aiRemainingQuota(User $user): ?int
    {
        $plan = self::getActivePlan($user);

        if (is_null($plan->ai_rate_limit)) {
            return null;
        }

        $used = $user->aiUsageLogs()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        return max(0, $plan->ai_rate_limit - $used);
    }

    /**
     * Summary lengkap status tier user — untuk ditampilkan di UI.
     */
    public static function getSummary(User $user): array
    {
        $plan         = self::getActivePlan($user);
        $subscription = $user->activeSubscription;

        return [
            'plan'              => $plan,
            'tier'              => $plan->tier,
            'is_free'           => $plan->tier === 'free',
            'days_remaining'    => $subscription?->daysRemaining(),
            'can_add_account'   => self::canAddAccount($user),
            'can_add_saving'    => self::canAddSavingGoal($user),
            'can_add_budget'    => self::canAddBudget($user),
            'can_export'        => self::canExport($user),
            'can_use_ai'        => self::canUseAi($user),
            'ai_quota'          => self::aiRemainingQuota($user),
        ];
    }
}
