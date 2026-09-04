<div>
    <div class="mb-6 text-center">
        <div class="text-xl font-extrabold">Forgot your password?</div>
        <div class="text-base-content/60 text-sm mt-1">
            Enter your email and we'll send you a password reset link.
        </div>
    </div>

    @if ($status)
        <div class="alert alert-success mb-4">{{ $status }}</div>
    @endif

    <x-form wire:submit="sendPasswordResetLink">
        <x-input label="Email" wire:model="email" type="email" autofocus autocomplete="username" required />

        <x-slot:actions>
            <x-button label="Email password reset link" type="submit" class="btn-primary w-full" spinner="sendPasswordResetLink" />
        </x-slot:actions>
    </x-form>

    <div class="text-center text-sm mt-6">
        <a href="{{ route('login') }}" wire:navigate class="link link-hover font-semibold">Back to login</a>
    </div>
</div>
