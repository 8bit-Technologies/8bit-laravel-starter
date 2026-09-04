<div>
    <div class="mb-6 text-center">
        <div class="text-xl font-extrabold">Reset password</div>
        <div class="text-base-content/60 text-sm mt-1">Choose a new password for your account.</div>
    </div>

    <x-form wire:submit="resetPassword">
        <x-input label="Email" wire:model="email" type="email" autocomplete="username" required />

        <x-password label="New password" wire:model="password" autofocus autocomplete="new-password" required />

        <x-password label="Confirm new password" wire:model="password_confirmation" autocomplete="new-password" required />

        <x-slot:actions>
            <x-button label="Reset password" type="submit" class="btn-primary w-full" spinner="resetPassword" />
        </x-slot:actions>
    </x-form>
</div>
