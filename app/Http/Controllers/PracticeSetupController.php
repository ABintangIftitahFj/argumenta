<?php

namespace App\Http\Controllers;

use App\Models\DebateSession;
use App\Models\Motion;
use App\Services\DebateRuleEngine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PracticeSetupController extends Controller
{
    /**
     * Show the practice setup screen.
     */
    public function create(Request $request, DebateRuleEngine $ruleEngine): Response
    {
        $user = $request->user();
        $profile = $user?->profile;

        $motions = Motion::orderByDesc('is_featured')
            ->orderBy('id')
            ->get();

        return Inertia::render('Practice', [
            'appName' => config('app.name', 'Argumenta'),
            'motions' => $motions,
            'bpRoles' => $ruleEngine->getRoles('BP'),
            'apRoles' => $ruleEngine->getRoles('AP'),
            'defaults' => [
                'format' => $profile?->preferred_format ?? 'BP',
                'language' => $profile?->language ?? 'id',
                'difficulty' => $profile?->skill_level ?? 'novice',
            ],
        ]);
    }

    /**
     * Store a new debate practice session.
     */
    public function store(Request $request, DebateRuleEngine $ruleEngine): RedirectResponse
    {
        $user = $request->user();
        if (! $user) {
            return redirect()->route('login');
        }

        $validated = $request->validate([
            'format' => ['required', 'string', 'in:BP,AP'],
            'role' => ['required', 'string'],
            'language' => ['required', 'string', 'in:id,en'],
            'difficulty' => ['required', 'string', 'in:novice,intermediate,advanced'],
            'motion_id' => ['nullable', 'exists:motions,id'],
            'custom_motion' => ['nullable', 'string', 'max:500'],
            'ai_coaching_enabled' => ['boolean'],
        ]);

        if (! $ruleEngine->isValidRole($validated['format'], $validated['role'])) {
            throw ValidationException::withMessages([
                'role' => "Posisi {$validated['role']} tidak valid untuk format {$validated['format']}.",
            ]);
        }

        // Determine motion text
        $motionText = null;
        if (! empty($validated['motion_id'])) {
            $motion = Motion::find($validated['motion_id']);
            $motionText = $motion?->title;
        } elseif (! empty($validated['custom_motion'])) {
            $motionText = trim($validated['custom_motion']);
        }

        if (empty($motionText)) {
            throw ValidationException::withMessages([
                'motion_id' => 'Silakan pilih mosi dari katalog atau masukkan mosi kustom Anda.',
            ]);
        }

        $rules = $ruleEngine->calculateSpeechRules($validated['format'], $validated['role']);

        $session = DebateSession::create([
            'user_id' => $user->id,
            'format' => $validated['format'],
            'motion_id' => $validated['motion_id'] ?? null,
            'motion_text' => $motionText,
            'role' => $validated['role'],
            'language' => $validated['language'],
            'difficulty' => $validated['difficulty'],
            'duration_seconds' => $rules['duration_seconds'],
            'poi_allowed' => $rules['poi_allowed'],
            'poi_start_second' => $rules['poi_start_second'],
            'poi_end_second' => $rules['poi_end_second'],
            'status' => 'prepared',
            'ai_coaching_enabled' => $request->boolean('ai_coaching_enabled', true),
        ]);

        return redirect()->route('practice.prepare', $session->id);
    }

    /**
     * Show the preparation brief and timer before recording.
     */
    public function prepare(Request $request, DebateSession $session, DebateRuleEngine $ruleEngine): Response
    {
        $user = $request->user();
        if ($user && $session->user_id !== $user->id) {
            abort(403, 'Akses sesi latihan tidak sah.');
        }

        $rules = $ruleEngine->calculateSpeechRules($session->format, $session->role);

        return Inertia::render('Practice/Prepare', [
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
                'ai_coaching_enabled' => $session->ai_coaching_enabled,
            ],
            'rules' => $rules,
        ]);
    }
}
