<?php

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

it('renders the forgot password page', function () {
    $this->get(route('password.request'))->assertOk();
});

it('sends a password reset link to an existing user', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test(ForgotPassword::class)
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

it('resets the password with a valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    Livewire::test(ForgotPassword::class)
        ->set('email', $user->email)
        ->call('sendPasswordResetLink');

    Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use ($user) {
        Livewire::test(ResetPassword::class, ['token' => $notification->token])
            ->set('email', $user->email)
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('resetPassword')
            ->assertRedirect(route('login', absolute: false));

        expect($user->fresh()->password)->not->toBe($user->password);

        return true;
    });
});
