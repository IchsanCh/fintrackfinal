<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'tier', 'name', 'price', 'duration_days',
    'max_accounts', 'max_saving_goals', 'max_budgets',
    'can_export', 'ai_rate_limit', 'has_priority_support', 'is_active',
])]
class Plan extends Model
{
    protected function casts(): array
    {
        return [
            'can_export'           => 'boolean',
            'has_priority_support' => 'boolean',
            'is_active'            => 'boolean',
            'price'                => 'integer',
        ];
    }

    // ── Relations ──────────────────────────────────────────
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /** Apakah limit tertentu unlimited (null) */
    public function isUnlimited(string $field): bool
    {
        return is_null($this->{$field});
    }

    /** Format harga: "Gratis" atau "Rp 49.000/bln" */
    public function formattedPrice(): string
    {
        if ($this->price === 0) {
            return 'Gratis';
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.') . '/bln';
    }

    // ── Scopes ─────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
