<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['transaction_id', 'file_path'])]
class TransactionAttachment extends Model
{
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }
}
