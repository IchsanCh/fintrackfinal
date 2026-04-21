<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id', 'plan_id', 'order_id', 'amount',
    'status', 'snap_token', 'midtrans_payload', 'paid_at',
])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'midtrans_payload' => 'array',
            'paid_at'          => 'datetime',
            'amount'           => 'integer',
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
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
    public function isExpired(): bool
    {
        return $this->status === 'expired';
    }

    public function formattedAmount(): string
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }
}
