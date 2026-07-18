<?php

use App\Models\User;

it('allows admin users to access the admin panel', function () {
    $admin = User::factory()->create(['is_admin' => true]);

    $this->actingAs($admin)
        ->get('/admin')
        ->assertSuccessful();
});

it('denies access to non-admin users', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();
});

it('redirects unauthenticated users to login', function () {
    $this->get('/admin')
        ->assertRedirect('/admin/login');
});
