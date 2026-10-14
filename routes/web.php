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

    Route::post('/practice', [\App\Http\Controllers\PracticeSetupController::class, 'store'])->name('practice.store');
    Route::get('/practice/{session}/prepare', [\App\Http\Controllers\PracticeSetupController::class, 'prepare'])->name('practice.prepare');
    Route::get('/practice/{session}/record', [\App\Http\Controllers\SpeechRecordingController::class, 'show'])->name('practice.record');
    Route::post('/practice/{session}/record', [\App\Http\Controllers\SpeechRecordingController::class, 'store'])->name('practice.record.store');
    Route::get('/practice/{session}/audio/{recording}', [\App\Http\Controllers\SpeechRecordingController::class, 'stream'])->name('practice.audio');
});

// Main App Navigation Shell
Route::get('/', \App\Http\Controllers\HomeController::class)->name('home');
Route::get('/learn', \App\Http\Controllers\LearnController::class)->name('learn');
Route::get('/practice', [\App\Http\Controllers\PracticeSetupController::class, 'create'])->name('practice');

Route::get('/coach', fn () => Inertia::render('Coach', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('coach');

Route::get('/progress', fn () => Inertia::render('Progress', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('progress');

Route::get('/profile', fn () => Inertia::render('Profile', [
    'appName' => config('app.name', 'Argumenta'),
]))->name('profile');

