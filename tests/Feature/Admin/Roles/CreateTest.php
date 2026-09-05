<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Roles\Create;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the create roles permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view roles']));

    $this->actingAs($user)
        ->get(route('admin.roles.create'))
        ->assertForbidden();
});

it('allows an authorized user to create a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::create(['name' => 'view roles']),
        Permission::create(['name' => 'create roles']),
    ]);
    $reports = Permission::create(['name' => 'view reports']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'Manager')
        ->set('selectedPermissions', [$reports->id])
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.roles.index'));

    $role = Role::where('name', 'Manager')->firstOrFail();

    expect($role->permissions->pluck('name')->all())->toBe(['view reports']);
});

it('rejects creating a role named super admin regardless of case', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create roles']));

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'super admin')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(Role::where('name', 'super admin')->exists())->toBeFalse();
});

it('prevents a normal admin from assigning a protected permission to a new role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create roles']));
    $viewUsers = Permission::create(['name' => 'view users']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'Manager')
        ->set('selectedPermissions', [$viewUsers->id])
        ->call('save');

    expect(Role::where('name', 'Manager')->exists())->toBeFalse();
});

it('allows a super admin to assign a protected permission to a new role', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $viewUsers = Permission::create(['name' => 'view users']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'Manager')
        ->set('selectedPermissions', [$viewUsers->id])
        ->call('save')
        ->assertHasNoErrors();

    $role = Role::where('name', 'Manager')->firstOrFail();

    expect($role->permissions->pluck('name')->all())->toBe(['view users']);
});

it('makes a newly created application permission available in the role manager', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create roles']));
    $bookings = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->assertSee('view bookings');

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'Bookings Manager')
        ->set('selectedPermissions', [$bookings->id])
        ->call('save')
        ->assertHasNoErrors();

    $role = Role::where('name', 'Bookings Manager')->firstOrFail();

    expect($role->permissions->pluck('name')->all())->toBe(['view bookings']);
});
