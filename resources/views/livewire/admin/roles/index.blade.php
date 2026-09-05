<div>
    <x-8bit.page-header title="Roles" subtitle="Manage roles and their assigned permissions.">
        @can('create roles')
            <x-slot:actions>
                <x-button label="Create Role" icon="o-plus" link="{{ route('admin.roles.create') }}" class="btn-primary" responsive />
            </x-slot:actions>
        @endcan
    </x-8bit.page-header>

    <x-card class="mt-5" shadow>
        <x-input placeholder="Search roles..." wire:model.live.debounce.400ms="search" icon="o-magnifying-glass" clearable class="max-w-sm" />

        <x-table :headers="$headers" :rows="$roles" with-pagination show-empty-text empty-text="No roles yet.">
            @scope('cell_permissions', $role)
                <div class="flex flex-wrap gap-1 py-1 min-w-[180px] max-w-full">
                    @forelse ($role->permissions as $permission)
                        <x-badge :value="$permission->name" class="badge-sm badge-ghost whitespace-nowrap shrink-0" />
                    @empty
                        <span class="text-base-content/50 text-sm">None</span>
                    @endforelse
                </div>
            @endscope

            @scope('actions', $role)
                <div class="flex justify-end gap-1">
                    @can('update roles')
                        @if (! \App\Enums\SystemRole::isProtectedName($role->name))
                            <x-button icon="o-pencil-square" link="{{ route('admin.roles.edit', $role) }}" class="btn-ghost btn-sm" tooltip="Edit" />
                        @endif
                    @endcan

                    @can('delete roles')
                        @if (! \App\Enums\SystemRole::isProtectedName($role->name))
                            <x-button
                                icon="o-trash"
                                wire:click="deleteRole({{ $role->id }})"
                                wire:confirm="Delete the {{ $role->name }} role? This action cannot be undone."
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
