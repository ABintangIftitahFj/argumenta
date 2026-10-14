<?php

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;

uses(RefreshDatabase::class);

it('renders the registration screen', function () {
    $this->get('/register')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Register')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('allows new users to register and creates default profile', function () {
    $response = $this->post('/register', [
        'name' => 'Bintang Iftitah',
        'email' => 'bintang@argumenta.test',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $this->assertAuthenticated();
    $this->assertDatabaseHas('users', [
        'email' => 'bintang@argumenta.test',
    ]);
    $this->assertDatabaseHas('profiles', [
        'language' => 'id',
        'onboarding_completed' => false,
    ]);

    $response->assertRedirect(route('onboarding'));
});

it('validates registration input requirements', function () {
    $this->post('/register', [
        'name' => '',
        'email' => 'invalid-email',
        'password' => 'short',
        'password_confirmation' => 'mismatch',
    ])->assertSessionHasErrors(['name', 'email', 'password']);
});

it('renders the login screen', function () {
    $this->get('/login')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('authenticates user with valid credentials and redirects to onboarding if not completed', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'onboarding_completed' => false,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('onboarding'));
});

it('authenticates user with completed onboarding and redirects to home', function () {
    $user = User::factory()->create();
    Profile::create([
        'user_id' => $user->id,
        'onboarding_completed' => true,
    ]);

    $response = $this->post('/login', [
        'email' => $user->email,
        'password' => 'password',
    ]);

    $this->assertAuthenticatedAs($user);
    $response->assertRedirect(route('home'));
});

it('fails authentication with invalid password', function () {
    $user = User::factory()->create();

    $this->post('/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('allows authenticated users to log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect(route('login'));

    $this->assertGuest();
});
