<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OnboardingController extends Controller
{
    /**
     * Display the onboarding wizard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $profile = $user?->profile;

        return Inertia::render('Onboarding/Setup', [
            'appName' => config('app.name', 'Argumenta'),
            'initialPreferences' => [
                'language' => $profile?->language ?? 'id',
                'learning_goal' => $profile?->learning_goal ?? 'competition',
                'skill_level' => $profile?->skill_level ?? 'novice',
                'preferred_format' => $profile?->preferred_format ?? 'BP',
                'weekly_goal' => $profile?->weekly_goal ?? 5,
            ],
        ]);
    }

    /**
     * Store onboarding preferences and activate profile.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'language' => ['required', 'string', 'in:id,en'],
            'learning_goal' => ['required', 'string', 'max:50'],
            'skill_level' => ['required', 'string', 'in:novice,intermediate,advanced'],
            'preferred_format' => ['required', 'string', 'in:BP,AP'],
            'weekly_goal' => ['required', 'integer', 'min:1', 'max:14'],
        ]);

        $user = $request->user();

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                ...$validated,
                'onboarding_completed' => true,
            ]
        );

        return redirect()->route('home')->with('success', 'Selamat datang di Argumenta! Profil Anda siap.');
    }
}
