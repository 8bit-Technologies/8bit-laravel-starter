<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

it('renders the registration page', function () {
    $this->get(route('register'))->assertOk();
});

it('allows a new user to register', function () {
    Event::fake();

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'test@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    expect(User::where('email', 'test@example.com')->exists())->toBeTrue();

    Event::assertDispatched(Registered::class);
});

it('requires a valid unique email to register', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'Test User')
        ->set('email', 'taken@example.com')
        ->set('password', 'password')
        ->set('password_confirmation', 'password')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);

    $this->assertGuest();
});
