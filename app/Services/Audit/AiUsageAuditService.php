<?php

namespace App\Services\Audit;

use App\Models\AiUsageLog;
use App\Models\User;

class AiUsageAuditService
{
    /**
     * Record an AI call audit log tracking tokens, cost, provider, model, and status.
     */
    public function log(
        User $user,
        string $feature,
        string $provider,
        string $model,
        int $inputTokens = 0,
        int $outputTokens = 0,
        float $costCents = 0.0,
        string $status = 'success',
        ?int $sessionId = null,
        ?string $errorMessage = null,
        int $durationMs = 0
    ): AiUsageLog {
        return AiUsageLog::create([
            'user_id' => $user->id,
            'debate_session_id' => $sessionId,
            'feature' => $feature,
            'provider' => $provider,
            'model' => $model,
            'input_tokens' => $inputTokens,
            'output_tokens' => $outputTokens,
            'duration_ms' => $durationMs,
            'cost_cents' => $costCents,
            'status' => $status,
            'error_message' => $errorMessage,
        ]);
    }
}
