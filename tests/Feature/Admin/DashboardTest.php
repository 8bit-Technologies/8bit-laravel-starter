<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

it('redirects a guest to the login page', function () {
    $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
});

it('denies an authenticated verified user without the access dashboard permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

it('allows an authenticated verified user with the access dashboard permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'access dashboard']));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Dashboard');
});

it('allows an unverified authorized user to access the admin dashboard when email verification is disabled', function () {
    expect(config('features.email_verification_enabled'))->toBeFalse();

    $user = User::factory()->unverified()->create();
    $user->givePermissionTo(Permission::create(['name' => 'access dashboard']));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

it('redirects an unverified authorized user to the email verification notice when email verification is enabled', function () {
    config(['features.email_verification_enabled' => true]);

    $user = User::factory()->unverified()->create();
    $user->givePermissionTo(Permission::create(['name' => 'access dashboard']));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('verification.notice'));
});
