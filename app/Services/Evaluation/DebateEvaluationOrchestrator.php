<?php

namespace App\Services\Evaluation;

use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\EvaluationIssue;
use App\Models\SpeechRecording;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use App\Services\AiJudge\AiJudgeInterface;
use App\Services\SpeechToText\SpeechToTextInterface;
use Illuminate\Support\Facades\DB;

class DebateEvaluationOrchestrator
{
    public function __construct(
        protected SpeechToTextInterface $sttService,
        protected AiJudgeInterface $aiJudgeService,
    ) {
    }

    /**
     * Run full speech processing pipeline: STT transcription + AI Judge evaluation.
     */
    public function process(DebateSession $session, ?SpeechRecording $recording = null): Evaluation
    {
        $recording = $recording ?: $session->latestRecording;

        if (! $recording) {
            throw new \RuntimeException('No speech recording available to evaluate.');
        }

        return DB::transaction(function () use ($session, $recording) {
            // 1. Transcribe speech audio
            $sttResult = $this->sttService->transcribe($recording);

            /** @var Transcript $transcript */
            $transcript = Transcript::updateOrCreate(
                ['speech_recording_id' => $recording->id],
                [
                    'debate_session_id' => $session->id,
                    'user_id' => $session->user_id,
                    'raw_text' => $sttResult->rawText,
                    'language' => $sttResult->language,
                    'confidence_score' => $sttResult->confidenceScore,
                    'words_count' => $sttResult->wordsCount,
                    'words_per_minute' => $sttResult->wordsPerMinute,
                    'has_uncertainty' => $sttResult->hasUncertainty,
                    'audio_quality_warning' => $sttResult->audioQualityWarning,
                    'asr_provider' => $sttResult->asrProvider,
                    'status' => 'completed',
                ]
            );

            // Clear old segments if any and re-insert
            $transcript->segments()->delete();
            foreach ($sttResult->segments as $segmentDto) {
                TranscriptSegment::create([
                    'transcript_id' => $transcript->id,
                    'segment_order' => $segmentDto->segmentOrder,
                    'start_time_seconds' => $segmentDto->startTimeSeconds,
                    'end_time_seconds' => $segmentDto->endTimeSeconds,
                    'text' => $segmentDto->text,
                    'confidence' => $segmentDto->confidence,
                    'is_uncertain' => $segmentDto->isUncertain,
                ]);
            }

            // 2. Run AI Judge evaluation
            $evaluationResult = $this->aiJudgeService->evaluate($session, $transcript);

            /** @var Evaluation $evaluation */
            $evaluation = Evaluation::updateOrCreate(
                ['debate_session_id' => $session->id],
                [
                    'transcript_id' => $transcript->id,
                    'user_id' => $session->user_id,
                    'debate_format' => $session->format,
                    'role' => $session->role,
                    'overall_score' => $evaluationResult->overallScore,
                    'matter_score' => $evaluationResult->matterScore,
                    'manner_score' => $evaluationResult->mannerScore,
                    'method_score' => $evaluationResult->methodScore,
                    'language_score' => $evaluationResult->languageScore,
                    'summary' => $evaluationResult->summary,
                    'strengths' => $evaluationResult->strengths,
                    'improvements' => $evaluationResult->improvements,
                    'next_drill_recommendation' => $evaluationResult->nextDrillRecommendation,
                    'model_version' => $evaluationResult->modelVersion,
                    'rubric_version' => $evaluationResult->rubricVersion,
                    'prompt_version' => $evaluationResult->promptVersion,
                    'status' => 'completed',
                ]
            );

            // Clear old issues if any and re-insert
            $evaluation->issues()->delete();
            foreach ($evaluationResult->issues as $issueDto) {
                EvaluationIssue::create([
                    'evaluation_id' => $evaluation->id,
                    'category' => $issueDto->category,
                    'title' => $issueDto->title,
                    'original_quote' => $issueDto->originalQuote,
                    'suggested_fix' => $issueDto->suggestedFix,
                    'explanation' => $issueDto->explanation,
                    'start_time_seconds' => $issueDto->startTimeSeconds,
                    'end_time_seconds' => $issueDto->endTimeSeconds,
                    'severity' => $issueDto->severity,
                ]);
            }

            // Mark session completed
            $session->update(['status' => 'completed']);

            return $evaluation->load(['transcript.segments', 'issues']);
        });
    }
}
