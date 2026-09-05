<?php

use App\Models\User;
use Spatie\Permission\Models\Permission;

it('hides roles and permissions navigation from a user without those permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'access dashboard']));

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertDontSee('Roles')
        ->assertDontSee('Permissions');
});

it('shows roles navigation only to a user with the view roles permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::create(['name' => 'access dashboard']),
        Permission::create(['name' => 'view roles']),
    ]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Roles')
        ->assertDontSee('Permissions');
});

it('shows permissions navigation only to a user with the view permissions permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::create(['name' => 'access dashboard']),
        Permission::create(['name' => 'view permissions']),
    ]);

    $this->actingAs($user)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Permissions')
        ->assertDontSee('Roles');
});
