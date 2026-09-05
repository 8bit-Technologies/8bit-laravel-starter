<?php

namespace App\Livewire\Admin\Roles;

use App\Enums\SystemRole;
use App\Livewire\Concerns\Notifies;
use App\Support\ProtectedPermissions;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class Edit extends Component
{
    use Notifies;

    public Role $role;

    public string $name = '';

    /** @var array<int, int> */
    public array $selectedPermissions = [];

    public function mount(Role $role): void
    {
        // The Super Admin role is immutable through the UI, for everyone,
        // including a Super Admin — see PHASE-3-ROLES-PERMISSIONS.md §6.1,
        // Rule 2. This is a hard invariant, not a permission check, so it is
        // never bypassed by the Gate::before Super Admin bypass.
        abort_if(SystemRole::isProtectedName($role->name), 403);

        $this->role = $role;
        $this->name = $role->name;
        $this->selectedPermissions = $role->permissions()->pluck('id')->all();
    }

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->ignore($this->role->id),
                function (string $attribute, mixed $value, Closure $fail) {
                    if (SystemRole::isProtectedName($value)) {
                        $fail('This name is reserved and cannot be used.');
                    }
                },
            ],
            'selectedPermissions' => ['array'],
            'selectedPermissions.*' => ['integer', 'exists:permissions,id'],
        ];
    }

    public function save(): void
    {
        $this->authorize('update roles');

        abort_if(SystemRole::isProtectedName($this->role->name), 403);

        $validated = $this->validate();

        $selectedNames = Permission::query()
            ->whereIn('id', $validated['selectedPermissions'])
            ->pluck('name')
            ->all();

        $includesProtected = collect($selectedNames)->contains(fn (string $name) => ProtectedPermissions::contains($name));

        if ($includesProtected && ! auth()->user()->isSuperAdmin()) {
            $this->notifyError('Only a Super Admin can assign protected system permissions.');

            return;
        }

        $this->role->update(['name' => $validated['name']]);
        $this->role->syncPermissions($selectedNames);

        $this->notifySuccess('Role updated successfully.');

        $this->redirectRoute('admin.roles.index', navigate: true);
    }

    /**
     * @return Collection<int, Permission>
     */
    protected function permissions(): Collection
    {
        return Permission::query()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.roles.edit', [
            'permissions' => $this->permissions(),
        ]);
    }
}
