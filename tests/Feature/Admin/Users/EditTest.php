<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Users\Edit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the update users permission', function () {
    $actor = User::factory()->create();
    $target = User::factory()->create();

    $this->actingAs($actor)
        ->get(route('admin.users.edit', $target))
        ->assertForbidden();
});

it('allows an authorized user to update an ordinary user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $target = User::factory()->create(['name' => 'Old Name', 'email' => 'old@example.com']);

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('name', 'New Name')
        ->set('email', 'new@example.com')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    expect($target->fresh())
        ->name->toBe('New Name')
        ->email->toBe('new@example.com')
        ->email_verified_at->toBeNull();
});

it('preserves the existing password when none is supplied', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $target = User::factory()->create(['password' => 'original-password']);
    $originalHash = $target->password;

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('name', 'Still Old Name Updated')
        ->call('save')
        ->assertHasNoErrors();

    expect($target->fresh()->password)->toBe($originalHash);
});

it('updates the password when explicitly supplied', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-new-secure-password')
        ->call('save')
        ->assertHasNoErrors();

    expect(Hash::check('a-new-secure-password', $target->fresh()->password))->toBeTrue();
});

it('does not reset email verification when the email is left unchanged', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $target = User::factory()->create(['email' => 'stable@example.com']);
    $verifiedAt = $target->email_verified_at;

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('name', 'Renamed Only')
        ->call('save')
        ->assertHasNoErrors();

    expect($target->fresh()->email_verified_at)->not->toBeNull()
        ->and($target->fresh()->email_verified_at->eq($verifiedAt))->toBeTrue();
});

it('rejects a duplicate email address on update', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    User::factory()->create(['email' => 'taken@example.com']);
    $target = User::factory()->create(['email' => 'mine@example.com']);

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('email', 'taken@example.com')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

it('assigns a dynamically created role on update', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $manager = Role::create(['name' => 'Manager']);
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->assertSee('Manager')
        ->set('roleId', (string) $manager->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($target->fresh()->hasRole('Manager'))->toBeTrue();
});

it('prevents a normal admin from reaching a super admins edit page', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);
    $target = User::factory()->create();
    $target->assignRole($superAdminRole);

    $this->actingAs($actor)
        ->get(route('admin.users.edit', $target))
        ->assertForbidden();
});

it('allows a super admin to edit another super admins account', function () {
    $actor = User::factory()->create();
    $actor->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->firstOrFail();
    $target = User::factory()->create();
    $target->assignRole($superAdminRole);

    $this->actingAs($actor)
        ->get(route('admin.users.edit', $target))
        ->assertOk();
});

it('does not offer the super admin role to a normal admin editing a user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    Role::create(['name' => SystemRole::SuperAdmin->value]);
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->assertDontSee(SystemRole::SuperAdmin->value);
});

it('prevents a normal admin from promoting a user to super admin', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'update users']));
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('roleId', (string) $superAdminRole->id)
        ->call('save');

    expect($target->fresh()->isSuperAdmin())->toBeFalse();
});

it('allows a super admin to promote a user to super admin', function () {
    $actor = User::factory()->create();
    $actor->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->firstOrFail();
    $target = User::factory()->create();

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $target])
        ->set('roleId', (string) $superAdminRole->id)
        ->call('save')
        ->assertHasNoErrors();

    expect($target->fresh()->isSuperAdmin())->toBeTrue();
});

it('prevents removing the super admin role from the sole remaining super admin', function () {
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);

    $actor = User::factory()->create();
    $actor->assignRole($superAdminRole);

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $actor])
        ->set('roleId', '')
        ->call('save');

    expect($actor->fresh()->isSuperAdmin())->toBeTrue();
});

it('allows removing the super admin role when another super admin still exists', function () {
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);

    $actor = User::factory()->create();
    $actor->assignRole($superAdminRole);

    $other = User::factory()->create();
    $other->assignRole($superAdminRole);

    Livewire::actingAs($actor)
        ->test(Edit::class, ['user' => $other])
        ->set('roleId', '')
        ->call('save')
        ->assertHasNoErrors();

    expect($other->fresh()->isSuperAdmin())->toBeFalse();
});
