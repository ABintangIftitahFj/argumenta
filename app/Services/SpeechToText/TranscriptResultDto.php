<?php

namespace App\Services\SpeechToText;

class TranscriptResultDto
{
    /**
     * @param array<TranscriptSegmentDto> $segments
     */
    public function __construct(
        public readonly string $rawText,
        public readonly string $language,
        public readonly float $confidenceScore,
        public readonly int $wordsCount,
        public readonly int $wordsPerMinute,
        public readonly bool $hasUncertainty,
        public readonly ?string $audioQualityWarning = null,
        public readonly string $asrProvider = 'mock_stt',
        public readonly array $segments = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'raw_text' => $this->rawText,
            'language' => $this->language,
            'confidence_score' => $this->confidenceScore,
            'words_count' => $this->wordsCount,
            'words_per_minute' => $this->wordsPerMinute,
            'has_uncertainty' => $this->hasUncertainty,
            'audio_quality_warning' => $this->audioQualityWarning,
            'asr_provider' => $this->asrProvider,
            'segments' => array_map(fn (TranscriptSegmentDto $segment) => $segment->toArray(), $this->segments),
        ];
    }
}
