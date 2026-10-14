<?php

it('serves the application shell', function () {
    $this->get('/')
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Home')
            ->where('appName', config('app.name', 'Argumenta')));
});
