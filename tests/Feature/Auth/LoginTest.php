<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Livewire\Livewire;

it('renders the login page', function () {
    $this->get(route('login'))->assertOk();
});

it('allows a user to log in with valid credentials', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticatedAs($user);
});

it('fails to log in with invalid credentials', function () {
    $user = User::factory()->create();

    Livewire::test(Login::class)
        ->set('email', $user->email)
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors('email');

    $this->assertGuest();
});
