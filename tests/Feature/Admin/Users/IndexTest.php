<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Users\Index;
use App\Models\User;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('redirects a guest to the login page', function () {
    $this->get(route('admin.users.index'))->assertRedirect(route('login'));
});

it('redirects an unverified user to the email verification notice', function () {
    $user = User::factory()->unverified()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view users']));

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertRedirect(route('verification.notice'));
});

it('denies an authenticated user without the view users permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

it('allows a user with view users to access the index', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::create(['name' => 'view users']));
    $other = User::factory()->create(['name' => 'Jane Roe']);

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Jane Roe');
});

it('allows a super admin to access the index without the explicit permission', function () {
    $user = User::factory()->create();
    $user->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertOk();
});

it('displays the roles assigned to each user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'view users']));

    $manager = Role::create(['name' => 'Manager']);
    $target = User::factory()->create();
    $target->assignRole($manager);

    $this->actingAs($actor)
        ->get(route('admin.users.index'))
        ->assertOk()
        ->assertSee('Manager');
});

it('requires the delete users permission to delete a user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'view users']));
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $target->id)
        ->assertForbidden();

    expect(User::find($target->id))->not->toBeNull();
});

it('allows an authorized user to delete an ordinary user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo([
        Permission::create(['name' => 'view users']),
        Permission::create(['name' => 'delete users']),
    ]);
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $target->id);

    expect(User::find($target->id))->toBeNull();
});

it('prevents a user from deleting their own account', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'delete users']));

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $actor->id);

    expect(User::find($actor->id))->not->toBeNull();
});

it('prevents deleting the last remaining super admin', function () {
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);

    $actor = User::factory()->create();
    $actor->assignRole($superAdminRole);

    $target = User::factory()->create();
    $target->assignRole($superAdminRole);

    // Delete the actor's own Super Admin role assignment (not the actor's
    // account) so $target becomes the sole remaining Super Admin, while a
    // different, still-privileged actor attempts the deletion.
    $actor->removeRole($superAdminRole);
    $actor->givePermissionTo(Permission::create(['name' => 'delete users']));

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $target->id);

    expect(User::find($target->id))->not->toBeNull();
});

it('allows a super admin to delete another super admin when more than one exists', function () {
    $actor = User::factory()->create();
    $actor->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->firstOrFail();
    $target = User::factory()->create();
    $target->assignRole($superAdminRole);

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $target->id);

    expect(User::find($target->id))->toBeNull();
});

it('prevents a normal admin from deleting a super admin', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'delete users']));

    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);
    $target = User::factory()->create();
    $target->assignRole($superAdminRole);

    Livewire::actingAs($actor)
        ->test(Index::class)
        ->call('deleteUser', $target->id);

    expect(User::find($target->id))->not->toBeNull();
});
