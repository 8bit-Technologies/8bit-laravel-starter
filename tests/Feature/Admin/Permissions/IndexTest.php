<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Permissions\Index;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the view permissions permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertForbidden();
});

it('allows an authorized user to view permissions', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view permissions']));
    Permission::create(['name' => 'view bookings']);

    $this->actingAs($user)
        ->get(route('admin.permissions.index'))
        ->assertOk()
        ->assertSee('view bookings');
});

it('requires the delete permissions permission to delete a permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view permissions']));
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $permission->id)
        ->assertForbidden();

    expect(Permission::find($permission->id))->not->toBeNull();
});

it('allows an authorized user to delete an ordinary permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::create(['name' => 'view permissions']),
        Permission::create(['name' => 'delete permissions']),
    ]);
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::find($permission->id))->toBeNull();
});

it('prevents deleting a protected system permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'delete permissions']));

    $protected = Permission::where('name', 'delete permissions')->firstOrFail();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $protected->id);

    expect(Permission::find($protected->id))->not->toBeNull();
});

it('allows a super admin to delete an ordinary permission without holding delete permissions directly', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $permission = Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::find($permission->id))->toBeNull();
});

it('prevents a super admin from deleting a protected system permission', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $protected = Permission::where('name', 'delete permissions')->first()
        ?? Permission::create(['name' => 'delete permissions']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $protected->id);

    expect(Permission::find($protected->id))->not->toBeNull();
});

it('prevents deleting a permission that is currently assigned to a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'delete permissions']));

    $permission = Permission::create(['name' => 'view bookings']);
    Role::create(['name' => 'Manager'])->givePermissionTo($permission);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deletePermission', $permission->id);

    expect(Permission::find($permission->id))->not->toBeNull();
});
