<?php

namespace App\Services\SpeechToText;

class TranscriptSegmentDto
{
    public function __construct(
        public readonly int $segmentOrder,
        public readonly float $startTimeSeconds,
        public readonly float $endTimeSeconds,
        public readonly string $text,
        public readonly float $confidence = 0.95,
        public readonly bool $isUncertain = false,
    ) {
    }

    public function toArray(): array
    {
        return [
            'segment_order' => $this->segmentOrder,
            'start_time_seconds' => $this->startTimeSeconds,
            'end_time_seconds' => $this->endTimeSeconds,
            'text' => $this->text,
            'confidence' => $this->confidence,
            'is_uncertain' => $this->isUncertain,
        ];
    }
}
