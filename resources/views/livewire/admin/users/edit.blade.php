<div>
    <x-8bit.page-header title="Edit User" subtitle="Update this user's account details and role." />

    <x-card class="mt-5 max-w-xl" shadow>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required autofocus />

            <x-input label="Email" wire:model="email" type="email" required />

            <x-password label="New Password" wire:model="password" hint="Leave blank to keep the current password." />

            <x-password label="Confirm New Password" wire:model="password_confirmation" />

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
                <x-button label="Save Changes" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
