<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'title', 'amount', 'due_date', 'repeat_days', 'is_active', 'last_paid_at'])]
class BillReminder extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }
}
