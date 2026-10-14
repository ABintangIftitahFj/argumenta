<?php

namespace App\Http\Controllers;

use App\Models\DebateSession;
use App\Models\SpeechRecording;
use App\Services\DebateRuleEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SpeechRecordingController extends Controller
{
    /**
     * Show the live speech recording interface.
     */
    public function show(Request $request, DebateSession $session, DebateRuleEngine $ruleEngine): Response
    {
        $user = $request->user();
        if ($session->user_id !== $user->id) {
            abort(403, 'Akses sesi rekaman tidak sah.');
        }

        $rules = $ruleEngine->calculateSpeechRules($session->format, $session->role);
        $latestRecording = $session->latestRecording;

        return Inertia::render('Practice/Record', [
            'appName' => config('app.name', 'Argumenta'),
            'session' => [
                'id' => $session->id,
                'format' => $session->format,
                'motion_text' => $session->motion_text,
                'role' => $session->role,
                'language' => $session->language,
                'difficulty' => $session->difficulty,
                'duration_seconds' => $session->duration_seconds,
                'poi_allowed' => $session->poi_allowed,
                'poi_start_second' => $session->poi_start_second,
                'poi_end_second' => $session->poi_end_second,
                'status' => $session->status,
                'ai_coaching_enabled' => $session->ai_coaching_enabled,
            ],
            'rules' => $rules,
            'latestRecording' => $latestRecording ? [
                'id' => $latestRecording->id,
                'duration_seconds' => $latestRecording->duration_seconds,
                'file_size_bytes' => $latestRecording->file_size_bytes,
                'mime_type' => $latestRecording->mime_type,
                'upload_status' => $latestRecording->upload_status,
                'stream_url' => route('practice.audio', [$session->id, $latestRecording->id]),
            ] : null,
        ]);
    }

    /**
     * Store and validate an uploaded audio recording blob.
     */
    public function store(Request $request, DebateSession $session): RedirectResponse
    {
        $user = $request->user();
        if ($session->user_id !== $user->id) {
            abort(403, 'Akses sesi rekaman tidak sah.');
        }

        $validated = $request->validate([
            'audio' => ['required', 'file', 'max:51200'], // 50MB max
            'duration_seconds' => ['required', 'numeric', 'min:1', 'max:600'],
        ]);

        $file = $validated['audio'];
        $extension = $file->getClientOriginalExtension() ?: 'webm';
        $fileName = sprintf('speech_%d_%s.%s', $session->id, uniqid(), $extension);
        $directory = sprintf('recordings/%d', $user->id);

        $path = Storage::disk('private')->putFileAs($directory, $file, $fileName);

        $recording = SpeechRecording::create([
            'debate_session_id' => $session->id,
            'user_id' => $user->id,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName() ?: $fileName,
            'mime_type' => $file->getClientMimeType() ?: 'audio/webm',
            'file_size_bytes' => $file->getSize(),
            'duration_seconds' => (int) round($validated['duration_seconds']),
            'upload_status' => 'uploaded',
            'storage_disk' => 'private',
        ]);

        $session->update([
            'status' => 'recorded',
        ]);

        // Flash to session for Home recent session state
        session()->put('recent_session', [
            'id' => $session->id,
            'motion' => $session->motion_text,
            'format' => $session->format,
            'role' => $session->role,
            'score' => 84, // preliminary score placeholder until F7 AI Judge
            'date' => 'Baru saja',
        ]);

        return redirect()->route('practice.record', $session->id)
            ->with('success', 'Rekaman pidato berhasil disimpan dan siap dievaluasi.');
    }

    /**
     * Stream private audio securely to authenticated session owner.
     */
    public function stream(Request $request, DebateSession $session, SpeechRecording $recording): StreamedResponse
    {
        $user = $request->user();
        if ($recording->user_id !== $user->id || $recording->debate_session_id !== $session->id) {
            abort(403, 'Akses pemutaran audio privat ditolak.');
        }

        if (! Storage::disk('private')->exists($recording->file_path)) {
            abort(404, 'Berkas audio rekaman tidak ditemukan.');
        }

        return Storage::disk('private')->response(
            $recording->file_path,
            $recording->file_name,
            [
                'Content-Type' => $recording->mime_type,
                'Cache-Control' => 'private, no-cache',
            ]
        );
    }
}
