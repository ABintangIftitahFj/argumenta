<?php

namespace App\Http\Controllers;

use App\Services\Analytics\ProgressAnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgressController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, ProgressAnalyticsService $analyticsService): Response
    {
        $user = $request->user();

        if ($user) {
            $analytics = $analyticsService->getUserAnalytics($user);
        } else {
            $analytics = [
                'stats' => [
                    'total_speeches' => 0,
                    'total_practice_seconds' => 0,
                    'formatted_practice_time' => '0 jam 0 menit',
                    'average_score' => 0.0,
                    'matter_average' => 0.0,
                    'manner_average' => 0.0,
                    'method_average' => 0.0,
                    'streak' => 0,
                    'xp_points' => 0,
                ],
                'history' => [],
                'skill_trends' => [],
                'error_frequency' => [],
                'next_recommendation' => 'Masuk atau daftar untuk melacak riwayat pidato dan kemajuan debat Anda.',
            ];
        }

        return Inertia::render('Progress', [
            'appName' => config('app.name', 'Argumenta'),
            'stats' => $analytics['stats'],
            'history' => $analytics['history'],
            'skillTrends' => $analytics['skill_trends'],
            'errorFrequency' => $analytics['error_frequency'],
            'nextRecommendation' => $analytics['next_recommendation'],
        ]);
    }
}
