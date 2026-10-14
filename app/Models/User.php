<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the profile associated with the user.
     */
    public function profile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Profile::class);
    }

    /**
     * Get the learning progress entries for the user.
     */
    public function learningProgress(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserLearningProgress::class);
    }

    /**
     * Get the debate sessions created by the user.
     */
    public function debateSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(DebateSession::class);
    }

    /**
     * Get the user's credit balance.
     */
    public function credit(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(UserCredit::class);
    }

    /**
     * Get credit debit/grant audit transactions for the user.
     */
    public function creditTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(CreditTransaction::class);
    }

    /**
     * Get AI usage and cost audit logs for the user.
     */
    public function aiUsageLogs(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(AiUsageLog::class);
    }
}
