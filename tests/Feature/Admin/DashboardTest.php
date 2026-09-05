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

it('redirects an unverified user to the email verification notice', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertRedirect(route('verification.notice'));
});
