<div>
    <x-8bit.page-header title="Create Role" subtitle="Define a new role and choose which permissions it grants." />

    <x-card class="mt-5" shadow>
        <x-form wire:submit="save">
            <x-input label="Name" wire:model="name" required autofocus />

            <div class="mt-4">
                <label class="text-sm font-medium">Permissions</label>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-x-4 gap-y-2 mt-2">
                    @foreach ($permissions as $permission)
                        @php($isProtected = \App\Support\ProtectedPermissions::contains($permission->name))

                        <x-checkbox
                            :label="$permission->name"
                            value="{{ $permission->id }}"
                            wire:model="selectedPermissions"
                            :hint="$isProtected ? 'System permission' : null"
                            :disabled="$isProtected && ! auth()->user()->isSuperAdmin()"
                            omit-error
                        />
                    @endforeach
                </div>
            </div>

            <x-slot:actions>
                <x-button label="Cancel" link="{{ route('admin.roles.index') }}" />
                <x-button label="Create Role" type="submit" class="btn-primary" spinner="save" />
            </x-slot:actions>
        </x-form>
    </x-card>
</div>
