<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['account_id', 'category_id', 'amount', 'type', 'note', 'transaction_date'])]
class Transaction extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class)->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class)->withDefault();
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TransactionAttachment::class);
    }
}
