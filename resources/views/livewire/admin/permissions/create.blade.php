<div>
    <x-8bit.page-header title="Create Permission" subtitle="Add a new permission that can be assigned to roles." />

    <x-card class="mt-5 max-w-xl" shadow>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" placeholder="e.g. view bookings" hint="Use the {verb} {resource} convention, for example: view bookings" required autofocus />

            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('admin.permissions.index') }}" />
                <x-button label="Create Permission" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
