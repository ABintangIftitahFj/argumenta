<?php

use Inertia\Testing\AssertableInertia as Assert;

it('renders home page with appName prop', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('renders learn curriculum page', function () {
    $this->get(route('learn'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Learn')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('renders practice speech setup page', function () {
    $this->get(route('practice'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Practice')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('renders coach kiko consultation page', function () {
    $this->get(route('coach'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Coach')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('renders progress and statistics page', function () {
    $this->get(route('progress'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Progress')
            ->where('appName', config('app.name', 'Argumenta')));
});

it('renders profile and preferences page', function () {
    $this->get(route('profile'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Profile')
            ->where('appName', config('app.name', 'Argumenta')));
});
