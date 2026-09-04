<?php

namespace App\Livewire;

use App\Livewire\Concerns\Notifies;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Profile extends Component
{
    use Notifies;

    public string $name = '';

    public string $email = '';

    public function mount(): void
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
    }

    public function updateProfile(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->notifySuccess('Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
