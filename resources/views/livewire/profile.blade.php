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
</div>
