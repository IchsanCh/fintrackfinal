<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'saving_goal_id', 'account_id', 'amount', 'type', 'note'])]
class SavingTransaction extends Model
{
    public function savingGoal(): BelongsTo
    {
        return $this->belongsTo(SavingGoal::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}
