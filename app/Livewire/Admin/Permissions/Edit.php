<?php

namespace App\Livewire\Admin\Permissions;

use App\Livewire\Concerns\Notifies;
use App\Support\ProtectedPermissions;
use Closure;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.admin')]
class Edit extends Component
{
    use Notifies;

    public Permission $permission;

    public string $name = '';

    public function mount(Permission $permission): void
    {
        // Protected/system permissions can never be renamed through the UI,
        // by anyone, including a Super Admin — see
        // PHASE-3-ROLES-PERMISSIONS.md §6.4/§13.3. This is a hard invariant,
        // not a permission check, so it is never bypassed by the
        // Gate::before Super Admin bypass.
        abort_if(ProtectedPermissions::contains($permission->name), 403);

        $this->permission = $permission;
        $this->name = $permission->name;
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) {
                    $normalized = strtolower(trim($value));

                    $exists = Permission::query()
                        ->whereKeyNot($this->permission->id)
                        ->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])
                        ->exists();

                    if ($exists) {
                        $fail('A permission with this name already exists.');
                    }
                },
            ],
        ];
    }

    public function save(): void
    {
        $this->authorize('update permissions');

        abort_if(ProtectedPermissions::contains($this->permission->name), 403);

        $validated = $this->validate();

        $this->permission->update(['name' => trim($validated['name'])]);

        $this->notifySuccess('Permission updated successfully.');

        $this->redirectRoute('admin.permissions.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.permissions.edit');
    }
}
