<?php

namespace App\Http\Controllers;

use App\Models\DebateSession;
use App\Services\Evaluation\DebateEvaluationOrchestrator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EvaluationController extends Controller
{
    /**
     * Trigger evaluation processing pipeline for the session.
     */
    public function process(
        Request $request,
        DebateSession $session,
        DebateEvaluationOrchestrator $orchestrator
    ): RedirectResponse {
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to debate session.');
        }

        $recording = $session->latestRecording;
        if (! $recording) {
            return redirect()->route('practice.record', $session->id)
                ->withErrors(['recording' => 'Silakan rekam pidato Anda terlebih dahulu sebelum evaluasi.']);
        }

        $orchestrator->process($session, $recording);

        return redirect()->route('practice.evaluation', $session->id)
            ->with('success', 'Pidato Anda telah berhasil dievaluasi oleh AI Judge!');
    }

    /**
     * Display evaluation scorecard and interactive transcript replay.
     */
    public function show(Request $request, DebateSession $session): Response|RedirectResponse
    {
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to debate session.');
        }

        $session->load([
            'motion',
            'latestRecording',
            'evaluation.issues',
            'latestTranscript.segments',
        ]);

        $evaluation = $session->evaluation;
        $transcript = $session->latestTranscript;

        if (! $evaluation || ! $transcript) {
            // If processing not complete yet, attempt to run orchestrator if recording exists
            if ($session->latestRecording) {
                return redirect()->route('practice.record', $session->id);
            }

            return redirect()->route('practice.index');
        }

        $audioUrl = $session->latestRecording
            ? route('practice.audio', ['session' => $session->id, 'recording' => $session->latestRecording->id])
            : null;

        return Inertia::render('Practice/Evaluation', [
            'session' => [
                'id' => $session->id,
                'format' => $session->format,
                'role' => $session->role,
                'motion_text' => $session->motion_text,
                'language' => $session->language,
                'difficulty' => $session->difficulty,
                'duration_seconds' => $session->duration_seconds,
                'status' => $session->status,
                'notes' => $session->notes,
            ],
            'evaluation' => [
                'id' => $evaluation->id,
                'overall_score' => (float) $evaluation->overall_score,
                'matter_score' => (float) $evaluation->matter_score,
                'manner_score' => (float) $evaluation->manner_score,
                'method_score' => (float) $evaluation->method_score,
                'language_score' => $evaluation->language_score !== null ? (float) $evaluation->language_score : null,
                'summary' => $evaluation->summary,
                'strengths' => $evaluation->strengths,
                'improvements' => $evaluation->improvements,
                'next_drill_recommendation' => $evaluation->next_drill_recommendation,
                'model_version' => $evaluation->model_version,
                'rubric_version' => $evaluation->rubric_version,
                'prompt_version' => $evaluation->prompt_version,
                'issues' => $evaluation->issues->map(fn ($issue) => [
                    'id' => $issue->id,
                    'category' => $issue->category,
                    'title' => $issue->title,
                    'original_quote' => $issue->original_quote,
                    'suggested_fix' => $issue->suggested_fix,
                    'explanation' => $issue->explanation,
                    'start_time_seconds' => $issue->start_time_seconds,
                    'end_time_seconds' => $issue->end_time_seconds,
                    'severity' => $issue->severity,
                ]),
            ],
            'transcript' => [
                'id' => $transcript->id,
                'raw_text' => $transcript->raw_text,
                'user_edited_text' => $transcript->user_edited_text,
                'effective_text' => $transcript->effective_text,
                'language' => $transcript->language,
                'confidence_score' => (float) $transcript->confidence_score,
                'words_count' => $transcript->words_count,
                'words_per_minute' => $transcript->words_per_minute,
                'has_uncertainty' => $transcript->has_uncertainty,
                'audio_quality_warning' => $transcript->audio_quality_warning,
                'asr_provider' => $transcript->asr_provider,
                'segments' => $transcript->segments->map(fn ($s) => [
                    'id' => $s->id,
                    'segment_order' => $s->segment_order,
                    'start_time_seconds' => (float) $s->start_time_seconds,
                    'end_time_seconds' => (float) $s->end_time_seconds,
                    'text' => $s->text,
                    'confidence' => (float) $s->confidence,
                    'is_uncertain' => (bool) $s->is_uncertain,
                ]),
            ],
            'audioUrl' => $audioUrl,
        ]);
    }

    /**
     * Save user corrections to speech transcript.
     */
    public function updateTranscript(Request $request, DebateSession $session): RedirectResponse
    {
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Unauthorized access to debate session.');
        }

        $validated = $request->validate([
            'edited_text' => ['required', 'string', 'min:5', 'max:20000'],
        ]);

        $transcript = $session->latestTranscript;
        if (! $transcript) {
            abort(404, 'Transcript not found for this debate session.');
        }

        $transcript->update([
            'user_edited_text' => $validated['edited_text'],
        ]);

        return redirect()->route('practice.evaluation', $session->id)
            ->with('success', 'Transkrip berhasil diperbarui dan disimpan!');
    }
}
