<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Display the home dashboard.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $modules = LearningModule::where('is_published', true)
            ->orderBy('order')
            ->get();

        // Calculate progress state per module
        $userProgressMap = [];
        if ($user) {
            $userProgressMap = $user->learningProgress()
                ->pluck('status', 'learning_module_id')
                ->toArray();
        }

        $formattedModules = $modules->map(function (LearningModule $module, int $index) use ($userProgressMap) {
            $status = $userProgressMap[$module->id] ?? null;

            if (! $status) {
                // If first module and no progress, it is active/available; otherwise locked
                $status = ($index === 0) ? 'active' : 'locked';
            }

            return [
                'id' => $module->id,
                'title' => $module->title,
                'slug' => $module->slug,
                'stage' => 'Tahap '.$module->stage,
                'order' => $module->order,
                'description' => $module->description,
                'difficulty' => $module->difficulty,
                'xp_reward' => $module->xp_reward,
                'status' => $status,
            ];
        });

        // Determine recent speech session (empty state if none)
        $recentSession = null;
        if ($user && session()->has('recent_session')) {
            $recentSession = session('recent_session');
        }

        $profile = $user?->profile;

        return Inertia::render('Home', [
            'appName' => config('app.name', 'Argumenta'),
            'modules' => $formattedModules,
            'stats' => [
                'streak' => $profile?->streak_count ?? 0,
                'xp' => $profile?->total_xp ?? 0,
                'weeklyGoal' => $profile?->weekly_goal ?? 5,
                'completedThisWeek' => 0,
            ],
            'recentSession' => $recentSession,
        ]);
    }
}
