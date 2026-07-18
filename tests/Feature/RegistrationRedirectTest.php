<?php

use function Pest\Laravel\post;
use function Pest\Laravel\get;
use function Pest\Laravel\assertAuthenticated;

it('simulates inertia registration', function () {
    $response = post('/register', [
        'name' => 'Inertia User 3',
        'email' => 'inertia_403_3@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ], [
        'X-Inertia' => 'true',
    ]);

    assertAuthenticated();

    $dashboardResponse = get('/dashboard', [
        'X-Inertia' => 'true',
    ]);

    dump($dashboardResponse->status());
});
