<div>
    <div class="mb-6 text-center">
        <div class="text-xl font-extrabold">Create an account</div>
        <div class="text-base-content/60 text-sm mt-1">Get started in less than a minute.</div>
    </div>

    <x-form wire:submit="register">
        <x-input label="Name" wire:model="name" autofocus autocomplete="name" required />

        <x-input label="Email" wire:model="email" type="email" autocomplete="username" required />

        <x-password label="Password" wire:model="password" autocomplete="new-password" required />

        <x-password label="Confirm password" wire:model="password_confirmation" autocomplete="new-password" required />

        <x-slot:actions>
            <x-button label="Create account" type="submit" class="btn-primary w-full" spinner="register" />
        </x-slot:actions>
    </x-form>

    <div class="text-center text-sm mt-6">
        Already have an account?
        <a href="{{ route('login') }}" wire:navigate class="link link-hover font-semibold">Log in</a>
    </div>
</div>
