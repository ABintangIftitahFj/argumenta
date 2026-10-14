<?php

use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\SpeechRecording;
use App\Models\Transcript;
use App\Models\TranscriptSegment;
use App\Models\User;
use App\Services\AiJudge\AiJudgeInterface;
use App\Services\AiJudge\DebateAiJudgeService;
use App\Services\AiJudge\EvaluationIssueDto;
use App\Services\AiJudge\EvaluationResultDto;
use App\Services\Evaluation\DebateEvaluationOrchestrator;
use App\Services\SpeechToText\SimulatedSpeechToTextService;
use App\Services\SpeechToText\SpeechToTextInterface;
use App\Services\SpeechToText\TranscriptResultDto;
use App\Services\SpeechToText\TranscriptSegmentDto;
use Inertia\Testing\AssertableInertia as Assert;

test('it verifies typed STT provider output structure and uncertainty markers', function () {
    $service = new SimulatedSpeechToTextService();
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Single-Use Plastics',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/sample.webm',
        'file_name' => 'sample.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 102400,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $result = $service->transcribe($recording);

    expect($result)->toBeInstanceOf(TranscriptResultDto::class)
        ->and($result->rawText)->not->toBeEmpty()
        ->and($result->wordsCount)->toBeGreaterThan(0)
        ->and($result->wordsPerMinute)->toBeGreaterThan(0)
        ->and($result->hasUncertainty)->toBeTrue()
        ->and($result->segments)->not->toBeEmpty();

    $uncertainSegments = array_filter($result->segments, fn ($s) => $s->isUncertain);
    expect($uncertainSegments)->not->toBeEmpty();
});

test('it verifies typed AI Judge evaluation structure with exactly three strengths and three improvements', function () {
    $judge = new DebateAiJudgeService();
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Implement Universal Basic Income',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $transcript = new Transcript([
        'raw_text' => 'Thank you Madam Speaker. Today we propose universal basic income to eliminate extreme poverty.',
        'language' => 'en',
        'words_count' => 14,
        'words_per_minute' => 140,
        'confidence_score' => 0.95,
    ]);

    $evaluationResult = $judge->evaluate($session, $transcript);

    expect($evaluationResult)->toBeInstanceOf(EvaluationResultDto::class)
        ->and($evaluationResult->strengths)->toHaveCount(3)
        ->and($evaluationResult->improvements)->toHaveCount(3)
        ->and($evaluationResult->overallScore)->toBeGreaterThanOrEqual(0)
        ->and($evaluationResult->matterScore)->toBeGreaterThan(0)
        ->and($evaluationResult->mannerScore)->toBeGreaterThan(0)
        ->and($evaluationResult->methodScore)->toBeGreaterThan(0)
        ->and($evaluationResult->languageScore)->toBeGreaterThan(0)
        ->and($evaluationResult->nextDrillRecommendation)->not->toBeEmpty()
        ->and($evaluationResult->issues)->not->toBeEmpty();
});

test('it persists transcript and evaluation via orchestrator with database transaction', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'AP',
        'motion_text' => 'Dewan ini akan menerapkan pajak karbon progresif',
        'role' => 'Prime Minister',
        'language' => 'id',
        'difficulty' => 'advanced',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/speech.webm',
        'file_name' => 'speech.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 500000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $orchestrator = app(DebateEvaluationOrchestrator::class);
    $evaluation = $orchestrator->process($session, $recording);

    expect($evaluation)->toBeInstanceOf(Evaluation::class)
        ->and($evaluation->strengths)->toHaveCount(3)
        ->and($evaluation->improvements)->toHaveCount(3)
        ->and($session->fresh()->status)->toBe('completed');

    $this->assertDatabaseHas('transcripts', [
        'debate_session_id' => $session->id,
        'speech_recording_id' => $recording->id,
        'user_id' => $user->id,
    ]);

    $this->assertDatabaseHas('evaluations', [
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'debate_format' => 'AP',
    ]);

    $this->assertDatabaseHas('transcript_segments', [
        'transcript_id' => $evaluation->transcript_id,
        'segment_order' => 1,
    ]);
});

test('it processes evaluation request via POST endpoint and redirects to scorecard', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Regrets The Rise of FinTech',
        'role' => 'Deputy Prime Minister',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/fintech.webm',
        'file_name' => 'fintech.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 200000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $response = $this->actingAs($user)
        ->post(route('practice.process', $session->id));

    $response->assertRedirect(route('practice.evaluation', $session->id));

    $this->assertDatabaseHas('evaluations', [
        'debate_session_id' => $session->id,
    ]);
});

test('it renders evaluation scorecard view with three strengths, three improvements, and transcript segments', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Believes in AI Regulation',
        'role' => 'Leader of Opposition',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/ai.webm',
        'file_name' => 'ai.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 300000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    app(DebateEvaluationOrchestrator::class)->process($session, $recording);

    $response = $this->actingAs($user)
        ->get(route('practice.evaluation', $session->id));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Practice/Evaluation')
            ->has('session')
            ->has('evaluation')
            ->has('evaluation.strengths', 3)
            ->has('evaluation.improvements', 3)
            ->has('transcript')
            ->has('transcript.segments')
            ->where('transcript.has_uncertainty', true)
        );
});

test('it allows debater to submit transcript corrections and stores edited text', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Private Cars',
        'role' => 'Member of Government',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'completed',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/cars.webm',
        'file_name' => 'cars.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 300000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    app(DebateEvaluationOrchestrator::class)->process($session, $recording);

    $correctedText = 'Thank you, Madam Speaker. Today we firmly argue that banning private cars drastically curtails urban emissions and unlocks sustainable public transit.';

    $response = $this->actingAs($user)
        ->patch(route('practice.transcript.update', $session->id), [
            'edited_text' => $correctedText,
        ]);

    $response->assertRedirect(route('practice.evaluation', $session->id));

    $this->assertDatabaseHas('transcripts', [
        'debate_session_id' => $session->id,
        'user_edited_text' => $correctedText,
    ]);

    $transcript = Transcript::where('debate_session_id', $session->id)->first();
    expect($transcript->effective_text)->toBe($correctedText);
});

test('it forbids unauthorized users from viewing or modifying another debaters evaluation and transcript', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'Private Session Motion',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $owner->id,
        'file_path' => 'recordings/' . $owner->id . '/priv.webm',
        'file_name' => 'priv.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 100000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    app(DebateEvaluationOrchestrator::class)->process($session, $recording);

    // Stranger tries to view evaluation
    $this->actingAs($stranger)
        ->get(route('practice.evaluation', $session->id))
        ->assertForbidden();

    // Stranger tries to trigger process
    $this->actingAs($stranger)
        ->post(route('practice.process', $session->id))
        ->assertForbidden();

    // Stranger tries to update transcript
    $this->actingAs($stranger)
        ->patch(route('practice.transcript.update', $session->id), [
            'edited_text' => 'Malicious overwrite attempt',
        ])
        ->assertForbidden();
});
