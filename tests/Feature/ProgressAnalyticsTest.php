<?php

use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\EvaluationIssue;
use App\Models\Profile;
use App\Models\SpeechRecording;
use App\Models\Transcript;
use App\Models\User;
use App\Services\Analytics\ProgressAnalyticsService;
use Inertia\Testing\AssertableInertia as Assert;

test('it aggregates debate history, skill trends, error frequency, and recommendation correctly', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'learning_goal' => 'win_tournaments',
        'preferred_format' => 'BP',
        'language' => 'en',
        'skill_level' => 'intermediate',
        'onboarding_completed' => true,
        'streak_count' => 3,
        'total_xp' => 350,
    ]);
    $user->refresh();

    // Create 2 completed sessions for this user
    $session1 = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Private Healthcare',
        'role' => 'Prime Minister',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'completed',
    ]);

    $recording1 = SpeechRecording::create([
        'debate_session_id' => $session1->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/health.webm',
        'file_name' => 'health.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 400000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $transcript1 = Transcript::create([
        'debate_session_id' => $session1->id,
        'speech_recording_id' => $recording1->id,
        'user_id' => $user->id,
        'raw_text' => 'Speech 1 on healthcare',
        'language' => 'en',
        'confidence_score' => 0.95,
        'words_count' => 120,
        'words_per_minute' => 140,
        'status' => 'completed',
    ]);

    $evaluation1 = Evaluation::create([
        'debate_session_id' => $session1->id,
        'transcript_id' => $transcript1->id,
        'user_id' => $user->id,
        'debate_format' => 'BP',
        'role' => 'Prime Minister',
        'overall_score' => 74.0,
        'matter_score' => 31.0,
        'manner_score' => 29.0,
        'method_score' => 14.0,
        'language_score' => 80.0,
        'summary' => 'Solid first speech',
        'strengths' => ['Clear premise', 'Good pacing', 'Structured points'],
        'improvements' => ['Deepen AREL mechanism', 'Compare trade-offs', 'Address counter-arguments'],
        'next_drill_recommendation' => 'Modul 2: AREL Mechanism Drill',
        'status' => 'completed',
    ]);

    EvaluationIssue::create([
        'evaluation_id' => $evaluation1->id,
        'category' => 'arel',
        'title' => 'Missing link in reasoning',
        'original_quote' => 'healthcare is a right',
        'suggested_fix' => 'Explain why private market fails',
        'explanation' => 'Unsubstantiated link',
        'severity' => 'moderate',
    ]);

    // Second session
    $session2 = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'AP',
        'motion_text' => 'This House Would Carbon Tax Heavy Industries',
        'role' => 'Leader of Opposition',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 300,
        'status' => 'completed',
    ]);

    $recording2 = SpeechRecording::create([
        'debate_session_id' => $session2->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/carbon.webm',
        'file_name' => 'carbon.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 300000,
        'duration_seconds' => 300,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $transcript2 = Transcript::create([
        'debate_session_id' => $session2->id,
        'speech_recording_id' => $recording2->id,
        'user_id' => $user->id,
        'raw_text' => 'Speech 2 on carbon tax',
        'language' => 'en',
        'confidence_score' => 0.96,
        'words_count' => 140,
        'words_per_minute' => 145,
        'status' => 'completed',
    ]);

    $evaluation2 = Evaluation::create([
        'debate_session_id' => $session2->id,
        'transcript_id' => $transcript2->id,
        'user_id' => $user->id,
        'debate_format' => 'AP',
        'role' => 'Leader of Opposition',
        'overall_score' => 76.0,
        'matter_score' => 32.0,
        'manner_score' => 29.5,
        'method_score' => 14.5,
        'language_score' => 82.0,
        'summary' => 'Improved refutation',
        'strengths' => ['Strong clash', 'Effective voice modulation', 'Concise summary'],
        'improvements' => ['Deepen AREL mechanism', 'Better time management', 'Address principle point'],
        'next_drill_recommendation' => 'Modul 4: Comparative Weighing & Delta Analysis',
        'status' => 'completed',
    ]);

    EvaluationIssue::create([
        'evaluation_id' => $evaluation2->id,
        'category' => 'arel',
        'title' => 'Missing link in economic impact',
        'original_quote' => 'capital flight will ruin economy',
        'suggested_fix' => 'Provide comparative evidence',
        'explanation' => 'Unsubstantiated link',
        'severity' => 'moderate',
    ]);

    EvaluationIssue::create([
        'evaluation_id' => $evaluation2->id,
        'category' => 'manner',
        'title' => 'Rapid tempo in rebuttal',
        'original_quote' => null,
        'suggested_fix' => 'Pause between distinct clashes',
        'explanation' => 'Fast speech rate without breath',
        'severity' => 'minor',
    ]);

    $service = new ProgressAnalyticsService();
    $analytics = $service->getUserAnalytics($user);

    expect($analytics['stats']['total_speeches'])->toBe(2)
        ->and($analytics['stats']['total_practice_seconds'])->toBe(720)
        ->and($analytics['stats']['average_score'])->toBe(75.0)
        ->and($analytics['stats']['matter_average'])->toBe(31.5)
        ->and($analytics['stats']['streak'])->toBe(3)
        ->and($analytics['stats']['xp_points'])->toBe(350);

    expect($analytics['history'])->toHaveCount(2)
        ->and($analytics['history'][0]['session_id'])->toBe($session2->id);

    expect($analytics['skill_trends'])->toHaveCount(2)
        ->and($analytics['skill_trends'][0]['overall_score'])->toBe(74.0)
        ->and($analytics['skill_trends'][1]['overall_score'])->toBe(76.0);

    expect($analytics['error_frequency'])->not->toBeEmpty();
    $arelError = collect($analytics['error_frequency'])->firstWhere('category', 'arel');
    expect($arelError['count'])->toBe(2);

    expect($analytics['next_recommendation'])->toBe('Modul 4: Comparative Weighing & Delta Analysis');
});

test('it renders the progress page with aggregated stats, history, trends, and recommendation for authenticated debater', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Factory Farming',
        'role' => 'Deputy Prime Minister',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'completed',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => 'recordings/' . $user->id . '/farm.webm',
        'file_name' => 'farm.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 300000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $transcript = Transcript::create([
        'debate_session_id' => $session->id,
        'speech_recording_id' => $recording->id,
        'user_id' => $user->id,
        'raw_text' => 'Speech on farming',
        'language' => 'en',
        'confidence_score' => 0.95,
        'words_count' => 150,
        'words_per_minute' => 140,
        'status' => 'completed',
    ]);

    Evaluation::create([
        'debate_session_id' => $session->id,
        'transcript_id' => $transcript->id,
        'user_id' => $user->id,
        'debate_format' => 'BP',
        'role' => 'Deputy Prime Minister',
        'overall_score' => 77.0,
        'matter_score' => 32.5,
        'manner_score' => 30.0,
        'method_score' => 14.5,
        'summary' => 'Great performance',
        'strengths' => ['Depth of argumentation', 'Pacing', 'Clash prioritization'],
        'improvements' => ['Clarify stakeholder harms', 'Strengthen rebuttal', 'Conclude on time'],
        'next_drill_recommendation' => 'Modul 5: Advanced BP Whip Strategy',
        'status' => 'completed',
    ]);

    $response = $this->actingAs($user)->get(route('progress'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Progress')
            ->has('stats')
            ->where('stats.total_speeches', 1)
            ->where('stats.average_score', fn ($score) => (float) $score === 77.0)
            ->has('history', 1)
            ->has('skillTrends', 1)
            ->where('nextRecommendation', 'Modul 5: Advanced BP Whip Strategy')
        );
});

test('it renders empty analytics state gracefully for new debater with no completed sessions', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('progress'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Progress')
            ->has('stats')
            ->where('stats.total_speeches', 0)
            ->where('stats.average_score', 0)
            ->has('history', 0)
            ->has('skillTrends', 0)
            ->has('errorFrequency', 0)
        );
});

test('it renders progress page for guests without breaking', function () {
    $response = $this->get(route('progress'));

    $response->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Progress')
            ->where('appName', config('app.name', 'Argumenta'))
            ->where('stats.total_speeches', 0)
        );
});
