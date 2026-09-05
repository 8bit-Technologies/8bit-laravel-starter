<div>
    <x-8bit.page-header title="Edit Permission" subtitle="Rename this permission." />

    <x-card class="mt-5 max-w-xl" shadow>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required autofocus />

            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('admin.permissions.index') }}" />
                <x-button label="Save Changes" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
