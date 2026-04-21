<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'tier'                 => 'free',
                'name'                 => 'Free',
                'price'                => 0,
                'duration_days'        => 30,
                'max_accounts'         => 2,
                'max_saving_goals'     => 2,
                'max_budgets'          => 3,
                'can_export'           => false,
                'ai_rate_limit'        => 5,      // 5x per bulan
                'has_priority_support' => false,
                'is_active'            => true,
            ],
            [
                'tier'                 => 'premium',
                'name'                 => 'Premium',
                'price'                => 49000,
                'duration_days'        => 30,
                'max_accounts'         => 10,
                'max_saving_goals'     => null,   // unlimited
                'max_budgets'          => null,   // unlimited
                'can_export'           => true,
                'ai_rate_limit'        => 50,     // 50x per bulan
                'has_priority_support' => false,
                'is_active'            => true,
            ],
            [
                'tier'                 => 'sultan',
                'name'                 => 'Sultan',
                'price'                => 99000,
                'duration_days'        => 30,
                'max_accounts'         => null,   // unlimited
                'max_saving_goals'     => null,   // unlimited
                'max_budgets'          => null,   // unlimited
                'can_export'           => true,
                'ai_rate_limit'        => null,   // unlimited
                'has_priority_support' => true,
                'is_active'            => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['tier' => $plan['tier']], $plan);
        }
    }
}
