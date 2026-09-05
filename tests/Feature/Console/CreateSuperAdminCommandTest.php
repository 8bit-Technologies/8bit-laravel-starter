<?php

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

it('creates a super admin with the provided details', function () {
    $this->artisan('8bit:create-super-admin')
        ->expectsQuestion('Name', 'Ada Lovelace')
        ->expectsQuestion('Email', 'ada@example.com')
        ->expectsQuestion('Password', 'correct-horse-battery-staple')
        ->expectsQuestion('Confirm password', 'correct-horse-battery-staple')
        ->assertExitCode(0);

    $user = User::where('email', 'ada@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('Ada Lovelace')
        ->and($user->isSuperAdmin())->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull()
        ->and(Hash::check('correct-horse-battery-staple', $user->password))->toBeTrue();
});

it('warns and requires confirmation before creating an additional super admin', function () {
    $existing = User::factory()->create();
    $existing->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->artisan('8bit:create-super-admin')
        ->expectsConfirmation('Create another Super Admin anyway?', 'no')
        ->assertExitCode(0);

    expect(User::count())->toBe(1);
});

it('creates another super admin when explicitly confirmed', function () {
    $existing = User::factory()->create();
    $existing->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->artisan('8bit:create-super-admin')
        ->expectsConfirmation('Create another Super Admin anyway?', 'yes')
        ->expectsQuestion('Name', 'Grace Hopper')
        ->expectsQuestion('Email', 'grace@example.com')
        ->expectsQuestion('Password', 'another-secure-password')
        ->expectsQuestion('Confirm password', 'another-secure-password')
        ->assertExitCode(0);

    expect(User::count())->toBe(2)
        ->and(User::where('email', 'grace@example.com')->first()?->isSuperAdmin())->toBeTrue();
});

it('creates another super admin without prompting for confirmation when --force is used', function () {
    $existing = User::factory()->create();
    $existing->assignRole(Role::create(['name' => SystemRole::SuperAdmin->value]));

    $this->artisan('8bit:create-super-admin --force')
        ->expectsQuestion('Name', 'Grace Hopper')
        ->expectsQuestion('Email', 'grace@example.com')
        ->expectsQuestion('Password', 'another-secure-password')
        ->expectsQuestion('Confirm password', 'another-secure-password')
        ->assertExitCode(0);

    expect(User::count())->toBe(2);
});
