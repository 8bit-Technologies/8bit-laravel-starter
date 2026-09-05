<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Livewire\Livewire;

it('does not allow registration when the feature is disabled by default', function () {
    expect(config('features.registration_enabled'))->toBeFalse();

    $this->get(route('register'))->assertNotFound();
});

it('does not show a registration link on the login page by default', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('Sign up');
});

it('renders the registration page when the feature is enabled', function () {
    config(['features.registration_enabled' => true]);

    $this->get(route('register'))->assertOk();
});

it('shows a registration link on the login page when the feature is enabled', function () {
    config(['features.registration_enabled' => true]);

    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Sign up');
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
