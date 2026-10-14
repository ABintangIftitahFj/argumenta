<?php

use App\Models\LearningModule;
use App\Models\Profile;
use App\Models\User;
use App\Models\UserLearningProgress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;


beforeEach(function () {
    $this->seed(\Database\Seeders\LearningModuleSeeder::class);
});

it('renders home page with learning modules and empty recent session state', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->has('modules', 5)
            ->where('modules.0.title', 'Dasar Klaim & Premis Argumen')
            ->where('modules.0.status', 'active')
            ->where('recentSession', null)
            ->has('stats'));
});

it('renders populated recent session state on home page when available', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'streak_count' => 7,
        'total_xp' => 750,
        'weekly_goal' => 5,
    ]);

    $recentSessionData = [
        'id' => 101,
        'motion' => 'Dewan ini akan mewajibkan kuota AI etis',
        'format' => 'BP',
        'role' => 'Prime Minister (PM)',
        'score' => 86,
        'date' => 'Hari ini',
    ];

    $this->actingAs($user)
        ->withSession(['recent_session' => $recentSessionData])
        ->get('/')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('recentSession.score', 86)
            ->where('recentSession.motion', 'Dewan ini akan mewajibkan kuota AI etis')
            ->where('stats.streak', 7)
            ->where('stats.xp', 750));
});

it('renders learn curriculum page with all seeded stages', function () {
    $this->get('/learn')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Learn')
            ->has('modules', 5)
            ->where('modules.0.slug', 'dasar-klaim-premis')
            ->where('modules.1.slug', 'mekanisme-rantai-kausalitas')
            ->where('modules.2.slug', 'rebuttal-direct-clash')
            ->where('modules.3.slug', 'comparative-weighing-metrics')
            ->where('modules.4.slug', 'role-fulfillment-bp-ap'));
});

it('reflects authenticated user module completion and score in learn curriculum', function () {
    $user = User::factory()->create();
    $module1 = LearningModule::where('slug', 'dasar-klaim-premis')->first();

    UserLearningProgress::create([
        'user_id' => $user->id,
        'learning_module_id' => $module1->id,
        'status' => 'completed',
        'score' => 95,
        'completed_at' => now(),
    ]);

    $this->actingAs($user)
        ->get('/learn')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Learn')
            ->where('modules.0.status', 'completed')
            ->where('modules.0.score', 95));
});
