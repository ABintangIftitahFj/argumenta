<?php

namespace App\Http\Controllers;

use App\Models\LearningModule;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LearnController extends Controller
{
    /**
     * Display the curriculum and learning path.
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $modules = LearningModule::where('is_published', true)
            ->orderBy('order')
            ->get();

        $userProgressMap = [];
        if ($user) {
            $userProgressMap = $user->learningProgress()
                ->get()
                ->keyBy('learning_module_id');
        }

        $formattedModules = $modules->map(function (LearningModule $module, int $index) use ($userProgressMap) {
            $progress = $userProgressMap[$module->id] ?? null;

            if ($progress) {
                $status = $progress->status;
                $score = $progress->score;
            } else {
                $status = ($index === 0) ? 'active' : 'locked';
                $score = null;
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
                'score' => $score,
            ];
        });

        return Inertia::render('Learn', [
            'appName' => config('app.name', 'Argumenta'),
            'modules' => $formattedModules,
        ]);
    }
}
