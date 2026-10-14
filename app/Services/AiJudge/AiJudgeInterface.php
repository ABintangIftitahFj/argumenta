<?php

namespace App\Services\AiJudge;

use App\Models\DebateSession;
use App\Models\Transcript;

interface AiJudgeInterface
{
    /**
     * Evaluate a debate session speech transcript according to format rules and rubrics.
     */
    public function evaluate(DebateSession $session, Transcript $transcript): EvaluationResultDto;
}
