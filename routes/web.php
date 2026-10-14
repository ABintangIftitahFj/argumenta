<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
