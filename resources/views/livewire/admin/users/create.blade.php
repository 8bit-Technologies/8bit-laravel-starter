<div>
    <x-8bit.page-header title="Create User" subtitle="Add a new user account and assign a role." />

    <x-card class="mt-5 max-w-xl" shadow>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required autofocus />

            <x-input label="Email" wire:model="email" type="email" required />

            <x-password label="Password" wire:model="password" required />

            <x-password label="Confirm Password" wire:model="password_confirmation" required />

            <x-select
                label="Role"
                wire:model="roleId"
                :options="$roles"
                placeholder="No role"
                placeholder-value=""
                hint="Leave unassigned for an ordinary member with no Admin Panel access."
            />

            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('admin.users.index') }}" />
                <x-button label="Create User" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
