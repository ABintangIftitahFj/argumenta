<?php

use App\Models\DebateSession;
use App\Models\Motion;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->seed(\Database\Seeders\MotionSeeder::class);
});

it('renders the practice setup screen with motions catalog and roles', function () {
    $this->get('/practice')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Practice')
            ->has('motions')
            ->has('bpRoles')
            ->has('apRoles')
            ->has('defaults'));
});

it('allows authenticated users to configure a valid BP debate session', function () {
    $user = User::factory()->create();
    $motion = Motion::first();

    $response = $this->actingAs($user)->post('/practice', [
        'format' => 'BP',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'motion_id' => $motion->id,
        'ai_coaching_enabled' => true,
    ]);

    $session = DebateSession::where('user_id', $user->id)->first();
    expect($session)->not->toBeNull();
    expect($session->format)->toBe('BP');
    expect($session->role)->toBe('PM');
    expect($session->duration_seconds)->toBe(420);
    expect($session->poi_allowed)->toBeTrue();
    expect($session->status)->toBe('prepared');

    $response->assertRedirect(route('practice.prepare', $session->id));
});

it('allows configuring an AP Reply session with 4-minute duration and no POI', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/practice', [
        'format' => 'AP',
        'role' => 'GR',
        'language' => 'en',
        'difficulty' => 'intermediate',
        'custom_motion' => 'This House would prohibit targeted political advertising.',
    ]);

    $session = DebateSession::where('user_id', $user->id)->first();
    expect($session)->not->toBeNull();
    expect($session->format)->toBe('AP');
    expect($session->role)->toBe('GR');
    expect($session->duration_seconds)->toBe(240);
    expect($session->poi_allowed)->toBeFalse();
    expect($session->motion_text)->toBe('This House would prohibit targeted political advertising.');

    $response->assertRedirect(route('practice.prepare', $session->id));
});

it('rejects an invalid role for the selected format', function () {
    $user = User::factory()->create();
    $motion = Motion::first();

    // Member of Government (MG) is only valid in BP, not in AP
    $this->actingAs($user)->post('/practice', [
        'format' => 'AP',
        'role' => 'MG',
        'language' => 'id',
        'difficulty' => 'novice',
        'motion_id' => $motion->id,
    ])->assertSessionHasErrors('role');
});

it('requires either catalog motion or custom motion', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/practice', [
        'format' => 'BP',
        'role' => 'LO',
        'language' => 'id',
        'difficulty' => 'novice',
        'motion_id' => null,
        'custom_motion' => '',
    ])->assertSessionHasErrors('motion_id');
});

it('redirects guests away from practice session submission to login', function () {
    $motion = Motion::first();

    $this->post('/practice', [
        'format' => 'BP',
        'role' => 'PM',
        'language' => 'id',
        'difficulty' => 'novice',
        'motion_id' => $motion->id,
    ])->assertRedirect(route('login'));
});

it('renders the preparation brief page for session owner', function () {
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
        ->get(route('practice.prepare', $session->id))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Practice/Prepare')
            ->where('session.id', $session->id)
            ->where('session.role', 'PM')
            ->where('rules.duration_seconds', 420)
            ->has('rules.brief'));
});

it('forbids unauthorized users from accessing another debaters preparation session', function () {
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
        ->get(route('practice.prepare', $session->id))
        ->assertForbidden();
});
