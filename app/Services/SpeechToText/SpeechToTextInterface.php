<?php

namespace App\Services\SpeechToText;

use App\Models\SpeechRecording;

interface SpeechToTextInterface
{
    /**
     * Transcribe the given speech recording into typed transcript data.
     */
    public function transcribe(SpeechRecording $recording): TranscriptResultDto;
}
