<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'message', 'type', 'reference_id', 'is_read', 'reference_type'])]
class Notification extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withDefault();
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
