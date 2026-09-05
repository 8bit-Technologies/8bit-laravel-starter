<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Roles\Index;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the view roles permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertForbidden();
});

it('allows an authorized user to view roles', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view roles']));

    $role = Role::create(['name' => 'Manager']);
    $role->givePermissionTo(Permission::create(['name' => 'view reports']));

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertOk()
        ->assertSee('Manager')
        ->assertSee('view reports');
});

it('allows a super admin to view roles without the explicit permission', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->actingAs($user)
        ->get(route('admin.roles.index'))
        ->assertOk();
});

it('requires the delete roles permission to delete a role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view roles']));
    $role = Role::create(['name' => 'Manager']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteRole', $role->id)
        ->assertForbidden();

    expect(Role::find($role->id))->not->toBeNull();
});

it('allows an authorized user to delete an ordinary role', function () {
    $user = User::factory()->create();
    $user->givePermissionTo([
        Permission::create(['name' => 'view roles']),
        Permission::create(['name' => 'delete roles']),
    ]);
    $role = Role::create(['name' => 'Manager']);

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteRole', $role->id);

    expect(Role::find($role->id))->toBeNull();
});

it('prevents deleting the super admin role', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $user->givePermissionTo(Permission::create(['name' => 'delete roles']));

    $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->firstOrFail();

    Livewire::actingAs($user)
        ->test(Index::class)
        ->call('deleteRole', $superAdminRole->id);

    expect(Role::find($superAdminRole->id))->not->toBeNull();
});

it('prevents deleting a role that is still assigned to users', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo([
        Permission::create(['name' => 'view roles']),
        Permission::create(['name' => 'delete roles']),
    ]);

    $role = Role::create(['name' => 'Manager']);
    $assignedUser = User::factory()->create();
    $assignedUser->assignRole($role);

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteRole', $role->id);

    expect(Role::find($role->id))->not->toBeNull();
});
