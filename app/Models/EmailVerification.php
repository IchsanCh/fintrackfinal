<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

#[Fillable(['user_id', 'token', 'is_used', 'expires_at'])]
class EmailVerification extends Model
{
    protected function casts(): array
    {
        return [
            'is_used'    => 'boolean',
            'expires_at' => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function isValid(string $token): bool
    {
        if ($this->is_used) {
            return false;
        }
        if ($this->isExpired()) {
            return false;
        }

        return hash_equals($this->token, $token);
    }
}
