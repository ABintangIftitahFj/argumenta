<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Guest authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

// Authenticated user actions
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
});

// Main App Navigation Shell
Route::get('/', fn () => Inertia::render('Home', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('home');

Route::get('/learn', fn () => Inertia::render('Learn', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('learn');

Route::get('/practice', fn () => Inertia::render('Practice', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('practice');

Route::get('/coach', fn () => Inertia::render('Coach', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('coach');

Route::get('/progress', fn () => Inertia::render('Progress', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('progress');

Route::get('/profile', fn () => Inertia::render('Profile', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('profile');

