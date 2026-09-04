<?php

use App\Models\User;

it('redirects a guest to the login page', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

it('allows an authenticated verified user to access the admin dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard');
});

it('redirects an unverified user to the email verification notice', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('verification.notice'));
});
