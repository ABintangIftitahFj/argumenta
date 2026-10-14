<?php

use App\Models\DebateSession;
use App\Models\SpeechRecording;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    Storage::fake('private');
});

it('renders the speech recording screen for the session owner', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'prepared',
    ]);

    $this->actingAs($user)
        ->get(route('practice.record', $session->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Practice/Record')
            ->where('session.id', $session->id)
            ->where('session.role', 'PM')
            ->where('rules.duration_seconds', 420));
});

it('forbids unauthorized users from accessing the recording screen of another debater', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'prepared',
    ]);

    $this->actingAs($intruder)
        ->get(route('practice.record', $session->id))
        ->assertForbidden();
});

it('uploads and stores speech recording securely in private storage', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'prepared',
    ]);

    $fakeAudio = UploadedFile::fake()->create('speech.webm', 2048, 'audio/webm');

    $response = $this->actingAs($user)->post(route('practice.record.store', $session->id), [
        'audio' => $fakeAudio,
        'duration_seconds' => 415,
    ]);

    $response->assertRedirect(route('practice.record', $session->id));

    $this->assertDatabaseHas('speech_recordings', [
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'duration_seconds' => 415,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $recording = SpeechRecording::where('debate_session_id', $session->id)->first();
    expect($recording)->not->toBeNull();
    Storage::disk('private')->assertExists($recording->file_path);

    expect($session->fresh()->status)->toBe('recorded');
});

it('validates audio upload file requirement and duration', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'prepared',
    ]);

    $this->actingAs($user)->post(route('practice.record.store', $session->id), [
        'duration_seconds' => 0,
    ])->assertSessionHasErrors(['audio', 'duration_seconds']);
});

it('allows session owner to stream their private audio recording', function () {
    $user = User::factory()->create();
    $session = DebateSession::create([
        'user_id' => $user->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'recorded',
    ]);

    $filePath = "recordings/{$user->id}/test_speech.webm";
    Storage::disk('private')->put($filePath, 'fake audio content stream');

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $user->id,
        'file_path' => $filePath,
        'file_name' => 'test_speech.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 25,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $response = $this->actingAs($user)
        ->get(route('practice.audio', [$session->id, $recording->id]));

    $response->assertOk();
});

it('forbids another user from streaming private audio recording', function () {
    $owner = User::factory()->create();
    $intruder = User::factory()->create();

    $session = DebateSession::create([
        'user_id' => $owner->id,
        'format' => 'BP',
        'motion_text' => 'Dewan ini akan mewajibkan transparansi AI',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'duration_seconds' => 420,
        'poi_allowed' => true,
        'poi_start_second' => 60,
        'poi_end_second' => 360,
        'status' => 'recorded',
    ]);

    $filePath = "recordings/{$owner->id}/test_speech.webm";
    Storage::disk('private')->put($filePath, 'fake audio content stream');

    $recording = SpeechRecording::create([
        'debate_session_id' => $session->id,
        'user_id' => $owner->id,
        'file_path' => $filePath,
        'file_name' => 'test_speech.webm',
        'mime_type' => 'audio/webm',
        'file_size_bytes' => 25,
        'duration_seconds' => 420,
        'upload_status' => 'uploaded',
        'storage_disk' => 'private',
    ]);

    $this->actingAs($intruder)
        ->get(route('practice.audio', [$session->id, $recording->id]))
        ->assertForbidden();
});
