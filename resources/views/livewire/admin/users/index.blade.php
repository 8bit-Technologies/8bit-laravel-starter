<div>
    <x-8bit.page-header title="Users" subtitle="Manage user accounts and role assignments.">
        @can('create users')
            <x-slot:actions>
                <x-button label="Create User" icon="o-plus" link="{{ route('admin.users.create') }}" class="btn-primary" responsive />
            </x-slot:actions>
        @endcan
    </x-8bit.page-header>

    <x-card class="mt-5" shadow>
        <x-input placeholder="Search users..." wire:model.live.debounce.400ms="search" icon="o-magnifying-glass" clearable class="max-w-sm" />

        <x-table :headers="$headers" :rows="$users" with-pagination show-empty-text empty-text="No users yet.">
            @scope('cell_roles', $user)
                <div class="flex flex-wrap gap-1 py-1">
                    @forelse ($user->roles as $role)
                        <x-badge :value="$role->name" class="badge-sm badge-ghost whitespace-nowrap shrink-0" />
                    @empty
                        <span class="text-base-content/50 text-sm">None</span>
                    @endforelse
                </div>
            @endscope

            @scope('cell_verified', $user)
                @if ($user->email_verified_at)
                    <x-badge value="Verified" class="badge-sm badge-success badge-outline" />
                @else
                    <x-badge value="Unverified" class="badge-sm badge-ghost" />
                @endif
            @endscope

            @scope('actions', $user)
                <div class="flex justify-end gap-1">
                    @can('update', $user)
                        <x-button icon="o-pencil-square" link="{{ route('admin.users.edit', $user) }}" class="btn-ghost btn-sm" tooltip="Edit" />
                    @endcan

                    @can('delete users')
                        @if (! $user->is(auth()->user()) && ! ($user->isSuperAdmin() && $user->isTheLastSuperAdmin()))
                            <x-button
                                icon="o-trash"
                                wire:click="deleteUser({{ $user->id }})"
                                wire:confirm="Delete {{ $user->name }}? This action cannot be undone."
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
