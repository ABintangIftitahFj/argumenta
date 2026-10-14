<?php

namespace App\Services\Quota;

use App\Exceptions\QuotaExceededException;
use App\Models\CreditTransaction;
use App\Models\User;
use App\Models\UserCredit;
use Illuminate\Support\Facades\DB;

class QuotaGuardService
{
    /**
     * Retrieve or create default user credits record.
     */
    public function getOrCreateUserCredit(User $user): UserCredit
    {
        return UserCredit::firstOrCreate(
            ['user_id' => $user->id],
            [
                'plan_tier' => 'free',
                'total_credits' => 5,
                'used_credits' => 0,
                'monthly_credit_limit' => 5,
                'reset_at' => now()->addMonth(),
            ]
        );
    }

    /**
     * Check if user has sufficient credits without consuming.
     */
    public function hasAvailableCredits(User $user, int $required = 1): bool
    {
        $credit = $this->getOrCreateUserCredit($user);

        return $credit->hasCredits($required);
    }

    /**
     * Settle and consume credits atomically, creating an audit transaction.
     *
     * @throws QuotaExceededException
     */
    public function consume(User $user, int $amount = 1, string $referenceId = '', string $description = 'Konsumsi kuota evaluasi pidato AI'): UserCredit
    {
        return DB::transaction(function () use ($user, $amount, $referenceId, $description) {
            $credit = UserCredit::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $credit) {
                $credit = $this->getOrCreateUserCredit($user);
                // re-lock
                $credit = UserCredit::where('id', $credit->id)->lockForUpdate()->first();
            }

            if (! $credit->hasCredits($amount)) {
                throw new QuotaExceededException(
                    "Kuota AI Anda tidak mencukupi. Tersedia: {$credit->available_credits}, Dibutuhkan: {$amount}."
                );
            }

            $credit->increment('used_credits', $amount);
            $credit->refresh();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount,
                'balance_after' => $credit->available_credits,
                'type' => 'settle',
                'description' => $description,
                'reference_id' => $referenceId,
            ]);

            return $credit;
        });
    }

    /**
     * Refund consumed credits back to the user account atomically upon failure.
     */
    public function refund(User $user, int $amount = 1, string $referenceId = '', string $description = 'Pengembalian kuota karena kegagalan evaluasi'): UserCredit
    {
        return DB::transaction(function () use ($user, $amount, $referenceId, $description) {
            $credit = UserCredit::where('user_id', $user->id)->lockForUpdate()->first();

            if (! $credit) {
                $credit = $this->getOrCreateUserCredit($user);
                $credit = UserCredit::where('id', $credit->id)->lockForUpdate()->first();
            }

            $newUsed = max(0, $credit->used_credits - $amount);
            $credit->update(['used_credits' => $newUsed]);
            $credit->refresh();

            CreditTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'balance_after' => $credit->available_credits,
                'type' => 'refund',
                'description' => $description,
                'reference_id' => $referenceId,
            ]);

            return $credit;
        });
    }
}
