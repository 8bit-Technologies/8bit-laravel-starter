<?php

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('redirects a guest to the login page', function () {
    $this->get(route('profile'))->assertRedirect(route('login'));
});

it('allows an authenticated user to access their profile', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('profile'))
        ->assertOk()
        ->assertSee($user->name);
});

it('validates profile updates', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', '')
        ->set('email', 'not-an-email')
        ->call('updateProfile')
        ->assertHasErrors(['name' => 'required', 'email' => 'email']);
});

it('allows an authenticated user to update their profile', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('name', 'Updated Name')
        ->set('email', 'updated@example.com')
        ->call('updateProfile')
        ->assertHasNoErrors();

    expect($user->fresh())
        ->name->toBe('Updated Name')
        ->email->toBe('updated@example.com')
        ->email_verified_at->toBeNull();
});

it('allows an authenticated user to change their password', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-new-secure-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Hash::check('a-new-secure-password', $user->fresh()->password))->toBeTrue();
});

it('rejects a password change with an incorrect current password', function () {
    $user = User::factory()->create();
    $originalHash = $user->password;

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'wrong-current-password')
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-new-secure-password')
        ->call('updatePassword')
        ->assertHasErrors(['current_password']);

    expect($user->fresh()->password)->toBe($originalHash);
});

it('rejects a password change when the confirmation does not match', function () {
    $user = User::factory()->create();
    $originalHash = $user->password;

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-different-password')
        ->call('updatePassword')
        ->assertHasErrors(['password' => 'confirmed']);

    expect($user->fresh()->password)->toBe($originalHash);
});

it('rejects a password change that does not meet the password policy', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('updatePassword')
        ->assertHasErrors(['password']);
});

it('no longer accepts the old password after a successful change', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-new-secure-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Auth::attempt(['email' => $user->email, 'password' => 'password']))->toBeFalse();
});

it('accepts the new password for login after a successful change', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Profile::class)
        ->set('current_password', 'password')
        ->set('password', 'a-new-secure-password')
        ->set('password_confirmation', 'a-new-secure-password')
        ->call('updatePassword')
        ->assertHasNoErrors();

    expect(Auth::attempt(['email' => $user->email, 'password' => 'a-new-secure-password']))->toBeTrue();
});
