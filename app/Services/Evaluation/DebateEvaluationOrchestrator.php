<?php

namespace App\Services\Evaluation;

use App\Exceptions\QuotaExceededException;
use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\EvaluationIssue;
use App\Models\SpeechRecording;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use App\Services\AiJudge\AiJudgeInterface;
use App\Services\Audit\AiUsageAuditService;
use App\Services\Quota\QuotaGuardService;
use App\Services\SpeechToText\SpeechToTextInterface;
use Illuminate\Support\Facades\DB;
use Throwable;

class DebateEvaluationOrchestrator
{
    public function __construct(
        protected SpeechToTextInterface $sttService,
        protected AiJudgeInterface $aiJudgeService,
        protected QuotaGuardService $quotaGuard,
        protected AiUsageAuditService $auditService,
    ) {
    }

    /**
     * Run full speech processing pipeline: Quota check + STT transcription + AI Judge evaluation + Audit log.
     *
     * @throws QuotaExceededException|Throwable
     */
    public function process(DebateSession $session, ?SpeechRecording $recording = null): Evaluation
    {
        $recording = $recording ?: $session->latestRecording;

        if (! $recording) {
            throw new \RuntimeException('No speech recording available to evaluate.');
        }

        $user = $session->user;

        // 1. Quota Guard: Ensure user has quota and consume 1 credit
        try {
            $this->quotaGuard->consume(
                $user,
                1,
                (string) $session->id,
                "Evaluasi pidato {$session->role} ({$session->format})"
            );
        } catch (QuotaExceededException $e) {
            $this->auditService->log(
                user: $user,
                feature: 'speech_evaluation',
                provider: 'gemini',
                model: 'gemini-1.5-pro',
                inputTokens: 0,
                outputTokens: 0,
                costCents: 0.0,
                status: 'quota_exceeded',
                sessionId: $session->id,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }

        $startTime = microtime(true);

        try {
            $evaluation = DB::transaction(function () use ($session, $recording) {
                // 2. Transcribe speech audio
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

                // 3. Run AI Judge evaluation
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

            $durationMs = (int) round((microtime(true) - $startTime) * 1000);

            // 4. Log successful AI audit record
            $this->auditService->log(
                user: $user,
                feature: 'speech_evaluation',
                provider: 'gemini',
                model: 'gemini-1.5-pro',
                inputTokens: 850,
                outputTokens: 420,
                costCents: 0.0450,
                status: 'success',
                sessionId: $session->id,
                durationMs: $durationMs
            );

            return $evaluation;
        } catch (Throwable $e) {
            // Failure-path: refund credit and log error
            $this->quotaGuard->refund(
                $user,
                1,
                (string) $session->id,
                'Pengembalian kuota karena kegagalan pemrosesan evaluasi'
            );

            $this->auditService->log(
                user: $user,
                feature: 'speech_evaluation',
                provider: 'gemini',
                model: 'gemini-1.5-pro',
                inputTokens: 0,
                outputTokens: 0,
                costCents: 0.0,
                status: 'provider_error',
                sessionId: $session->id,
                errorMessage: $e->getMessage()
            );

            throw $e;
        }
    }
}
