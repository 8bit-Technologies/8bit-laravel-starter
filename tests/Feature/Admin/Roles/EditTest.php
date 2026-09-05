<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Roles\Edit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the update roles permission', function () {
    $user = User::factory()->create();
    $role = Role::create(['name' => 'Manager']);

    $this->actingAs($user)
        ->get(route('admin.roles.edit', $role))
        ->assertForbidden();
});

it('allows an authorized user to update an ordinary role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update roles']));
    $role = Role::create(['name' => 'Manager']);
    $reports = Permission::create(['name' => 'view reports']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['role' => $role])
        ->set('name', 'Senior Manager')
        ->set('selectedPermissions', [$reports->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.roles.index'));

    expect($role->fresh())
        ->name->toBe('Senior Manager')
        ->and($role->fresh()->permissions->pluck('name')->all())->toBe(['view reports']);
});

it('prevents renaming an ordinary role into super admin', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update roles']));
    $role = Role::create(['name' => 'Manager']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['role' => $role])
        ->set('name', 'Super Admin')
        ->call('save')
        ->assertHasErrors(['name']);

    expect($role->fresh()->name)->toBe('Manager');
});

it('prevents a normal admin from editing the super admin role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update roles']));
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);

    $this->actingAs($user)
        ->get(route('admin.roles.edit', $superAdminRole))
        ->assertForbidden();
});

it('prevents a super admin from editing the super admin role through the ui', function () {
    $user = User::factory()->create();
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);
    $user->assignRole($superAdminRole);

    $this->actingAs($user)
        ->get(route('admin.roles.edit', $superAdminRole))
        ->assertForbidden();
});

it('prevents a normal admin from adding a protected permission to an existing role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update roles']));
    $role = Role::create(['name' => 'Manager']);
    $viewUsers = Permission::create(['name' => 'view users']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['role' => $role])
        ->set('selectedPermissions', [$viewUsers->id])
        ->call('save');

    expect($role->fresh()->permissions)->toHaveCount(0);
});

it('allows a super admin to add a protected permission to an existing role', function () {
    $actor = User::factory()->create();
    $actor->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $role = Role::create(['name' => 'Manager']);
    $viewUsers = Permission::create(['name' => 'view users']);

    Livewire::actingAs($actor)
        ->test(Edit::class, ['role' => $role])
        ->set('selectedPermissions', [$viewUsers->id])
        ->call('save')
        ->assertHasNoErrors();

    expect($role->fresh()->permissions->pluck('name')->all())->toBe(['view users']);
});
