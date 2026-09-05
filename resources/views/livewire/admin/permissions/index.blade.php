<div>
    <x-8bit.page-header title="Permissions" subtitle="Manage the permissions available to roles.">
        @can('create permissions')
            <x-slot:actions>
                <x-button label="Create Permission" icon="o-plus" link="{{ route('admin.permissions.create') }}" class="btn-primary" responsive />
            </x-slot:actions>
        @endcan
    </x-8bit.page-header>

    <x-card class="mt-5" shadow>
        <x-input placeholder="Search permissions..." wire:model.live.debounce.400ms="search" icon="o-magnifying-glass" clearable class="max-w-sm" />

        <x-table :headers="$headers" :rows="$permissions" with-pagination show-empty-text empty-text="No permissions yet.">
            @scope('actions', $permission)
                <div class="flex justify-end gap-1">
                    @can('update permissions')
                        @if (! \App\Support\ProtectedPermissions::contains($permission->name))
                            <x-button icon="o-pencil-square" link="{{ route('admin.permissions.edit', $permission) }}" class="btn-ghost btn-sm" tooltip="Edit" />
                        @endif
                    @endcan

                    @can('delete permissions')
                        @if (! \App\Support\ProtectedPermissions::contains($permission->name))
                            <x-button
                                icon="o-trash"
                                wire:click="deletePermission({{ $permission->id }})"
                                wire:confirm="Delete the {{ $permission->name }} permission? This action cannot be undone."
                                spinner
                                class="btn-ghost btn-sm text-error"
                                tooltip="Delete"
                            />
                        @endif
                    @endcan
                </div>
            @endscope
        </x-table>
    </x-card>
</div>
