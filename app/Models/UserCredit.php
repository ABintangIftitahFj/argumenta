<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserCredit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_tier',
        'total_credits',
        'used_credits',
        'monthly_credit_limit',
        'reset_at',
    ];

    protected $casts = [
        'total_credits' => 'integer',
        'used_credits' => 'integer',
        'monthly_credit_limit' => 'integer',
        'reset_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getAvailableCreditsAttribute(): int
    {
        return max(0, $this->total_credits - $this->used_credits);
    }

    public function hasCredits(int $required = 1): bool
    {
        return $this->available_credits >= $required;
    }
}
