<?php

use App\Models\DebateSession;
use App\Models\Evaluation;
use App\Models\SpeechRecording;
use App\Models\Transcript;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

test('it verifies positive and negative authorization for speech audio streaming', function () {
    Storage::fake('private');

    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'This House Would Ban Private Healthcare',
        'role' => 'PM',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'completed',
    ]);

    $file = UploadedFile::fake()->create('private_audio.webm', 1000, 'audio/webm');
    $path = $file->storeAs("recordings/{$owner->id}", 'private_audio.webm', 'private');

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $owner->id,
        'file_path' => $path,
        'file_name' => 'private_audio.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 1024000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    // Positive: Owner is authorized to stream private audio
    $this->actingAs($owner)
        ->get(route('practice.audio', ['session' => $session->id, 'recording' => $recording->id]))
        ->assertOk()
        ->assertHeader('Content-Type', 'audio/webm');

    // Negative: Stranger is strictly forbidden from streaming owner audio
    $this->actingAs($stranger)
        ->get(route('practice.audio', ['session' => $session->id, 'recording' => $recording->id]))
        ->assertForbidden();

    // Negative: Unauthenticated guest redirected to login
    $this->post(route('logout'));
    $this->get(route('practice.audio', ['session' => $session->id, 'recording' => $recording->id]))
        ->assertRedirect(route('login'));
});

test('it verifies positive and negative authorization for debate preparation brief', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'This House Believes That Development Beats Conservation',
        'role' => 'DPM',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'preparing',
    ]);

    // Positive: Owner can view brief
    $this->actingAs($owner)
        ->get(route('practice.prepare', $session->id))
        ->assertOk();

    // Negative: Stranger cannot view brief
    $this->actingAs($stranger)
        ->get(route('practice.prepare', $session->id))
        ->assertForbidden();
});

test('it verifies positive and negative authorization for speech recording and evaluation', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'AP',
        'motion_text' => 'This House Would Ban Nuclear Energy',
        'role' => 'LO',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'duration_seconds' => 420,
        'status' => 'completed',
    ]);

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $owner->id,
        'file_path' => "recordings/{$owner->id}/rec.webm",
        'file_name' => 'rec.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 500000,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $transcript = Transcript::create([
        'debate_session_id' => $session->id,
        'speech_recording_id' => $recording->id,
        'user_id' => $owner->id,
        'raw_text' => 'Sample speech text',
        'language' => 'en',
        'confidence_score' => 0.95,
        'words_count' => 100,
        'words_per_minute' => 130,
        'status' => 'completed',
    ]);

    Evaluation::create([
        'debate_session_id' => $session->id,
        'transcript_id' => $transcript->id,
        'user_id' => $owner->id,
        'debate_format' => 'AP',
        'role' => 'LO',
        'overall_score' => 75.0,
        'matter_score' => 31.0,
        'manner_score' => 29.5,
        'method_score' => 14.5,
        'summary' => 'Good performance',
        'strengths' => ['Points', 'Clarity', 'Signposting'],
        'improvements' => ['Rebuttal', 'Timing', 'Weighing'],
        'next_drill_recommendation' => 'Modul 2 Drill',
        'status' => 'completed',
    ]);

    // Recording view authorization
    $this->actingAs($owner)->get(route('practice.record', $session->id))->assertOk();
    $this->actingAs($stranger)->get(route('practice.record', $session->id))->assertForbidden();

    // Evaluation view authorization
    $this->actingAs($owner)->get(route('practice.evaluation', $session->id))->assertOk();
    $this->actingAs($stranger)->get(route('practice.evaluation', $session->id))->assertForbidden();

    // Transcript update authorization
    $this->actingAs($stranger)
        ->patch(route('practice.transcript.update', $session->id), [
            'edited_text' => 'Unauthorized overwrite attempt',
        ])
        ->assertForbidden();

    $this->actingAs($owner)
        ->patch(route('practice.transcript.update', $session->id), [
            'edited_text' => 'Authorized owner correction',
        ])
        ->assertRedirect(route('practice.evaluation', $session->id));
});

test('it records structured security and error server logs when forbidden access occurs', function () {
    Log::spy();

    $owner = User::factory()->create();
    $stranger = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'Security Audit Motion',
        'role' => 'PM',
        'language' => 'en',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'status' => 'preparing',
    ]);

    $this->actingAs($stranger)
        ->get(route('practice.prepare', $session->id))
        ->assertForbidden();

    Log::shouldHaveReceived('warning')
        ->with('Unauthorized access attempt to debate session.', Mockery::on(function ($context) use ($stranger, $session) {
            return $context['user_id'] === $stranger->id
                && $context['session_id'] === $session->id;
        }));
});

test('it renders explicit Error page component with user-friendly error details', function () {
    $view = inertia('Error', [
        'status' => 403,
        'message' => 'Akses ditolak pada berkas audio privat.',
    ]);

    expect($view)->not->toBeNull();
});
