<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('redirects guests away from onboarding to login', function () {
    $this->get('/onboarding')
        ->assertRedirect(route('login'));
});

it('renders the onboarding wizard for authenticated users', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'onboarding_completed' => false,
    ]);

    $this->actingAs($user)
        ->get('/onboarding')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Onboarding/Setup')
            ->has('initialPreferences'));
});

it('saves onboarding preferences and completes setup', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'onboarding_completed' => false,
    ]);

    $response = $this->actingAs($user)->post('/onboarding', [
        'language' => 'en',
        'learning_goal' => 'academic',
        'skill_level' => 'intermediate',
        'preferred_format' => 'AP',
        'weekly_goal' => 7,
    ]);

    $response->assertRedirect(route('home'));

    $this->assertDatabaseHas('profiles', [
        'user_id' => $user->id,
        'language' => 'en',
        'learning_goal' => 'academic',
        'skill_level' => 'intermediate',
        'preferred_format' => 'AP',
        'weekly_goal' => 7,
        'onboarding_completed' => true,
    ]);
});

it('validates onboarding input choices strictly', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/onboarding', [
        'language' => 'invalid-lang',
        'learning_goal' => '',
        'skill_level' => 'grandmaster',
        'preferred_format' => 'PARLIAMENT',
        'weekly_goal' => 999,
    ])->assertSessionHasErrors(['language', 'learning_goal', 'skill_level', 'preferred_format', 'weekly_goal']);
});
