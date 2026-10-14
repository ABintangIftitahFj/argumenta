<?php

use App\Exceptions\QuotaExceededException;
use App\Models\AiUsageLog;
use App\Models\CreditTransaction;
use App\Models\DebateSession;
use App\Models\SpeechRecording;
use App\Models\User;
use App\Models\UserCredit;
use App\Services\AiJudge\AiJudgeInterface;
use App\Services\Evaluation\DebateEvaluationOrchestrator;
use App\Services\Quota\QuotaGuardService;
use App\Services\SpeechToText\SpeechToTextInterface;

test('it grants default trial credits and consumes them atomically', function () {
    $user = User::factory()->create();
    $quotaGuard = app(QuotaGuardService::class);

    expect($quotaGuard->hasAvailableCredits($user))->toBeTrue();

    $credit = $quotaGuard->consume($user, 1, 'session-123', 'Testing credit debit');

    expect($credit->available_credits)->toBe(4)
        ->and($credit->used_credits)->toBe(1);

    $this->assertDatabaseHas('credit_transactions', [
        'user_id' => $user->id,
        'amount' => -1,
        'balance_after' => 4,
        'type' => 'settle',
        'reference_id' => 'session-123',
    ]);
});

test('it stops AI calls and throws QuotaExceededException when credits are exhausted', function () {
    $user = User::factory()->create();
    $quotaGuard = app(QuotaGuardService::class);

    // Set credits to 0
    UserCredit::create([
        'user_id' => $user->id,
        'plan_tier' => 'free',
        'total_credits' => 5,
        'used_credits' => 5, // fully consumed
        'monthly_credit_limit' => 5,
    ]);

    expect($quotaGuard->hasAvailableCredits($user))->toBeFalse();

    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Autonomous Weapons',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/speech.webm',
        'file_name' => 'speech.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 100000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $orchestrator = app(DebateEvaluationOrchestrator::class);

    expect(fn () => $orchestrator->process($session))
        ->toThrow(QuotaExceededException::class);

    // Assert quota_exceeded audit log was persisted
    $this->assertDatabaseHas('ai_usage_logs', [
        'user_id' => $user->id,
        'debate_session_id' => $session->id,
        'feature' => 'speech_evaluation',
        'status' => 'quota_exceeded',
    ]);
});

test('it logs AI usage with user, feature, model, provider, tokens, and cost on successful evaluation', function () {
    $user = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Believes in Nuclear Energy Expansion',
        'role' => 'Leader of Opposition',
        'language' => 'en',
        'difficulty' => 'advanced',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/nuclear.webm',
        'file_name' => 'nuclear.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 250000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $orchestrator = app(DebateEvaluationOrchestrator::class);
    $orchestrator->process($session, $recording);

    $this->assertDatabaseHas('ai_usage_logs', [
        'user_id' => $user->id,
        'debate_session_id' => $session->id,
        'feature' => 'speech_evaluation',
        'provider' => 'gemini',
        'model' => 'gemini-1.5-pro',
        'status' => 'success',
    ]);

    $log = AiUsageLog::where('debate_session_id', $session->id)->first();
    expect($log->input_tokens)->toBeGreaterThan(0)
        ->and($log->output_tokens)->toBeGreaterThan(0)
        ->and($log->cost_cents)->toBeGreaterThan(0);
});

test('it refunds credit and logs provider_error on processing failure-path', function () {
    $user = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Private Jets',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/jets.webm',
        'file_name' => 'jets.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 150000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    // Mock AI judge to simulate provider outage
    $failingJudge = Mockery::mock(AiJudgeInterface::class);
    $failingJudge->shouldReceive('evaluate')
        ->andThrow(new RuntimeException('Gemini API 503 Service Unavailable'));

    $orchestrator = new DebateEvaluationOrchestrator(
        app(SpeechToTextInterface::class),
        $failingJudge,
        app(QuotaGuardService::class),
        app(\App\Services\Audit\AiUsageAuditService::class)
    );

    expect(fn () => $orchestrator->process($session, $recording))
        ->toThrow(RuntimeException::class, 'Gemini API 503 Service Unavailable');

    // Credit should be refunded back
    $credit = UserCredit::where('user_id', $user->id)->first();
    expect($credit->available_credits)->toBe(5);

    $this->assertDatabaseHas('credit_transactions', [
        'user_id' => $user->id,
        'type' => 'refund',
    ]);

    $this->assertDatabaseHas('ai_usage_logs', [
        'user_id' => $user->id,
        'status' => 'provider_error',
    ]);
});

test('it redirects user back with quota error when attempting evaluation with 0 credits', function () {
    $user = User::factory()->create();

    UserCredit::create([
        'user_id' => $user->id,
        'plan_tier' => 'free',
        'total_credits' => 5,
        'used_credits' => 5,
        'monthly_credit_limit' => 5,
    ]);

    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Impose Wealth Tax',
        'role' => 'Deputy Prime Minister',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'recording',
    ]);

    SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/wealth.webm',
        'file_name' => 'wealth.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 150000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $response = $this->actingAs($user)
        ->post(route('practice.process', $session->id));

    $response->assertRedirect(route('practice.record', $session->id))
        ->assertSessionHasErrors(['quota']);
});
