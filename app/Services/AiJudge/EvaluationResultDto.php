<?php

namespace App\Services\AiJudge;

class EvaluationResultDto
{
    /**
     * @param array<string> $strengths exactly 3 strengths
     * @param array<string> $improvements exactly 3 improvements
     * @param array<EvaluationIssueDto> $issues
     */
    public function __construct(
        public readonly float $overallScore,
        public readonly float $matterScore,
        public readonly float $mannerScore,
        public readonly float $methodScore,
        public readonly ?float $languageScore,
        public readonly string $summary,
        public readonly array $strengths,
        public readonly array $improvements,
        public readonly string $nextDrillRecommendation,
        public readonly string $modelVersion = 'gemini-1.5-pro',
        public readonly string $rubricVersion = 'wudc-2024',
        public readonly string $promptVersion = 'v1.0',
        public readonly array $issues = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'overall_score' => $this->overallScore,
            'matter_score' => $this->matterScore,
            'manner_score' => $this->mannerScore,
            'method_score' => $this->methodScore,
            'language_score' => $this->languageScore,
            'summary' => $this->summary,
            'strengths' => $this->strengths,
            'improvements' => $this->improvements,
            'next_drill_recommendation' => $this->nextDrillRecommendation,
            'model_version' => $this->modelVersion,
            'rubric_version' => $this->rubricVersion,
            'prompt_version' => $this->promptVersion,
            'issues' => array_map(fn (EvaluationIssueDto $issue) => $issue->toArray(), $this->issues),
        ];
    }
}
