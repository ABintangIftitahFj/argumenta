<?php

namespace App\Services\AiJudge;

class EvaluationIssueDto
{
    public function __construct(
        public readonly string $category,
        public readonly string $title,
        public readonly ?string $originalQuote,
        public readonly ?string $suggestedFix,
        public readonly string $explanation,
        public readonly ?float $startTimeSeconds = null,
        public readonly ?float $endTimeSeconds = null,
        public readonly string $severity = 'moderate',
    ) {
    }

    public function toArray(): array
    {
        return [
            'category' => $this->category,
            'title' => $this->title,
            'original_quote' => $this->originalQuote,
            'suggested_fix' => $this->suggestedFix,
            'explanation' => $this->explanation,
            'start_time_seconds' => $this->startTimeSeconds,
            'end_time_seconds' => $this->endTimeSeconds,
            'severity' => $this->severity,
        ];
    }
}
