<div>
    <x-8bit.page-header title="Profile" subtitle="Manage your account's name and email address." />

    <x-card class="mt-5 max-w-xl">
        <x-form wire:submit="updateProfile">
            <x-input label="Name" wire:model="name" autocomplete="name" required />

            <x-input label="Email" wire:model="email" type="email" autocomplete="username" required />

            <x-slot:actions>
                <x-button label="Save" type="submit" class="btn-primary" spinner="updateProfile" />
            </x-slot:actions>
        </x-form>
    </x-card>

    <div class="mt-10">
        <div class="text-lg font-bold">Password</div>
        <div class="text-base-content/60 text-sm">Update your account's password.</div>
    </div>

    <x-card class="mt-5 max-w-xl">
        <x-form wire:submit="updatePassword">
            <x-password label="Current Password" wire:model="current_password" autocomplete="current-password" required />

            <x-password label="New Password" wire:model="password" autocomplete="new-password" required />

            <x-password label="Confirm New Password" wire:model="password_confirmation" autocomplete="new-password" required />

            <x-slot:actions>
                <x-button label="Update Password" type="submit" class="btn-primary" spinner="updatePassword" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
