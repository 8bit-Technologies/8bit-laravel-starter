<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Permissions\Edit;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the update permissions permission', function () {
    $user = User::factory()->create();
    $permission = Permission::create(['name' => 'view bookings']);

    $this->actingAs($user)
        ->get(route('admin.permissions.edit', $permission))
        ->assertForbidden();
});

it('allows an authorized user to rename an ordinary permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update permissions']));
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['permission' => $permission])
        ->set('name', 'view reservations')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    expect($permission->fresh()->name)->toBe('view reservations');
});

it('prevents editing a protected system permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update permissions']));

    $protected = Permission::where('name', 'update permissions')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.permissions.edit', $protected))
        ->assertForbidden();
});

it('prevents a super admin from editing a protected system permission through the ui', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $protected = Permission::create(['name' => 'delete users']);

    $this->actingAs($user)
        ->get(route('admin.permissions.edit', $protected))
        ->assertForbidden();
});

it('prevents renaming an ordinary permission into a protected permission name', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'update permissions']));
    Permission::create(['name' => 'delete users']);
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['permission' => $permission])
        ->set('name', 'delete users')
        ->call('save')
        ->assertHasErrors(['name']);

    expect($permission->fresh()->name)->toBe('view bookings');
});

it('allows a super admin to edit a normal permission without holding update permissions directly', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['permission' => $permission])
        ->set('name', 'view reservations')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    expect($permission->fresh()->name)->toBe('view reservations');
});

it('prevents a super admin from renaming an ordinary permission into a protected permission name', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    Permission::create(['name' => 'delete users']);
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Edit::class, ['permission' => $permission])
        ->set('name', 'delete users')
        ->call('save')
        ->assertHasErrors(['name']);

    expect($permission->fresh()->name)->toBe('view bookings');
});
