<?php

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

it('allows a super admin to access the admin dashboard', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

it('allows a super admin to pass an arbitrary gate ability', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    expect(Gate::forUser($user)->allows('this-ability-does-not-exist-anywhere'))->toBeTrue();
});

it('allows a super admin to pass an arbitrary permission check', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    expect($user->can('some permission nobody granted'))->toBeTrue();
});

it('does not treat an ordinary role as super admin', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => 'Manager']));

    expect($user->isSuperAdmin())->toBeFalse()
        ->and($user->can('access dashboard'))->toBeFalse();
});
