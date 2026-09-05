<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Permissions\Create;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the create permissions permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.permissions.create'))
        ->assertForbidden();
});

it('allows an authorized user to create a permission', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create permissions']));

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'view bookings')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    expect(Permission::where('name', 'view bookings')->exists())->toBeTrue();
});

it('allows a super admin to create a permission without holding create permissions directly', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'view bookings')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.permissions.index'));

    expect(Permission::where('name', 'view bookings')->exists())->toBeTrue();
});

it('rejects a duplicate permission name regardless of case or whitespace', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create permissions']));
    Permission::create(['name' => 'view bookings']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', '  VIEW BOOKINGS  ')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(Permission::where('name', 'view bookings')->count())->toBe(1);
});

it('rejects creating a permission that collides with a protected permission name', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'create permissions']));
    Permission::create(['name' => 'delete users']);

    Livewire::actingAs($user)
        ->test(Create::class)
        ->set('name', 'delete users')
        ->call('save')
        ->assertHasErrors(['name']);

    expect(Permission::where('name', 'delete users')->count())->toBe(1);
});
