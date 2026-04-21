<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'plan_id', 'status', 'started_at', 'expired_at'])]
class Subscription extends Model
{
    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expired_at' => 'datetime',
        ];
    }

    // ── Relations ──────────────────────────────────────────
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // ── Helpers ────────────────────────────────────────────

    /** Apakah subscription masih aktif */
    public function isActive(): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Free plan tidak punya expired_at
        if (is_null($this->expired_at)) {
            return true;
        }

        return $this->expired_at->isFuture();
    }

    /** Sisa hari subscription */
    public function daysRemaining(): ?int
    {
        if (is_null($this->expired_at)) {
            return null;
        }
        return max(0, now()->diffInDays($this->expired_at, false));
    }
}
