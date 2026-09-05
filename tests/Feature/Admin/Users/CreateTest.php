<?php

use App\Enums\SystemRole;
use App\Livewire\Admin\Users\Create;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('denies a user without the create users permission', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.users.create'))
        ->assertForbidden();
});

it('allows an authorized user to create a user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->set('name', 'Ada Lovelace')
        ->set('email', 'ada@example.com')
        ->set('password', 'correct-horse-battery-staple')
        ->set('password_confirmation', 'correct-horse-battery-staple')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.users.index'));

    $created = User::where('email', 'ada@example.com')->firstOrFail();

    expect($created->name)->toBe('Ada Lovelace')
        ->and(Hash::check('correct-horse-battery-staple', $created->password))->toBeTrue()
        ->and($created->email_verified_at)->not->toBeNull()
        ->and($created->roles)->toHaveCount(0);
});

it('validates required fields', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->set('password', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required', 'email' => 'email', 'password' => 'required']);
});

it('rejects a duplicate email address', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->set('name', 'Someone')
        ->set('email', 'taken@example.com')
        ->set('password', 'correct-horse-battery-staple')
        ->set('password_confirmation', 'correct-horse-battery-staple')
        ->call('save')
        ->assertHasErrors(['email' => 'unique']);
});

it('assigns a dynamically created role to the new user', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));
    $manager = Role::create(['name' => 'Manager']);

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->assertSee('Manager')
        ->set('name', 'New Manager')
        ->set('email', 'manager@example.com')
        ->set('password', 'correct-horse-battery-staple')
        ->set('password_confirmation', 'correct-horse-battery-staple')
        ->set('roleId', (string) $manager->id)
        ->call('save')
        ->assertHasNoErrors();

    $created = User::where('email', 'manager@example.com')->firstOrFail();

    expect($created->hasRole('Manager'))->toBeTrue();
});

it('does not offer the super admin role to a normal admin', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));
    Role::create(['name' => SystemRole::SuperAdmin->value]);

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->assertDontSee(SystemRole::SuperAdmin->value);
});

it('prevents a normal admin from creating a user with the super admin role', function () {
    $actor = User::factory()->create();
    $actor->givePermissionTo(Permission::create(['name' => 'create users']));
    $superAdminRole = Role::create(['name' => SystemRole::SuperAdmin->value]);

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->set('name', 'Hopeful Escalator')
        ->set('email', 'escalate@example.com')
        ->set('password', 'correct-horse-battery-staple')
        ->set('password_confirmation', 'correct-horse-battery-staple')
        ->set('roleId', (string) $superAdminRole->id)
        ->call('save');

    expect(User::where('email', 'escalate@example.com')->exists())->toBeFalse();
});

it('allows a super admin to create a user with the super admin role', function () {
    $actor = User::factory()->create();
    $actor->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));
    $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->firstOrFail();

    Livewire::actingAs($actor)
        ->test(Create::class)
        ->set('name', 'New Super Admin')
        ->set('email', 'newsuperadmin@example.com')
        ->set('password', 'correct-horse-battery-staple')
        ->set('password_confirmation', 'correct-horse-battery-staple')
        ->set('roleId', (string) $superAdminRole->id)
        ->call('save')
        ->assertHasNoErrors();

    $created = User::where('email', 'newsuperadmin@example.com')->firstOrFail();

    expect($created->isSuperAdmin())->toBeTrue();
});
