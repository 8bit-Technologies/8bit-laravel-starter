<?php

use App\Livewire\Profile;
use App\Models\User;
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
