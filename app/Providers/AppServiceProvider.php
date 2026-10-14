<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Services\SpeechToText\SpeechToTextInterface::class,
            \App\Services\SpeechToText\SimulatedSpeechToTextService::class
        );

        $this->app->bind(
            \App\Services\AiJudge\AiJudgeInterface::class,
            \App\Services\AiJudge\DebateAiJudgeService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
