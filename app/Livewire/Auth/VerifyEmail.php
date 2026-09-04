<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class VerifyEmail extends Component
{
    public bool $resent = false;

    public function sendVerification(): void
    {
        if (Auth::user()->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

            return;
        }

        Auth::user()->sendEmailVerificationNotification();

        $this->resent = true;
    }

    public function logout(): void
    {
        Auth::guard('web')->logout();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.verify-email');
    }
}
