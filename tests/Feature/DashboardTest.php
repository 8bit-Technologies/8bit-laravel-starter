<?php

use App\Models\User;

it('redirects a guest to the login page', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('allows an authenticated verified user to access the dashboard', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertSee('Dashboard');
});

it('does not require the access dashboard permission', function () {
    $user = User::factory()->create();

    expect($user->can('access dashboard'))->toBeFalse();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('allows an unverified user to access the dashboard when email verification is disabled', function () {
    expect(config('features.email_verification_enabled'))->toBeFalse();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

it('redirects an unverified user to the email verification notice when email verification is enabled', function () {
    config(['features.email_verification_enabled' => true]);

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));
});
