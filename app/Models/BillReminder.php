<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'amount', 'due_date', 'repeat_days', 'is_active', 'last_paid_at'])]
class BillReminder extends Model
{
    protected function casts(): array
    {
        return [
            'due_date'     => 'date',
            'last_paid_at' => 'datetime',
            'is_active'    => 'boolean',
            'amount'       => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    /** Status: paid, overdue, due_today, upcoming */
    public function getStatusAttribute(): string
    {
        // Sudah dibayar bulan ini (atau sesuai cycle)
        if ($this->last_paid_at && $this->last_paid_at->gte($this->due_date)) {
            return 'paid';
        }

        if ($this->due_date->isToday()) return 'due_today';
        if ($this->due_date->isPast()) return 'overdue';
        return 'upcoming';
    }

    /** Sisa hari sampai due date */
    public function getDaysUntilDueAttribute(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->due_date, false);
    }

    /** Apakah recurring */
    public function isRecurring(): bool
    {
        return !is_null($this->repeat_days) && $this->repeat_days > 0;
    }
}