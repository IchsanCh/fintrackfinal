<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['admin_id', 'title', 'content', 'is_active'])]
class Announcement extends Model
{
    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id')->withDefault();
    }
}
