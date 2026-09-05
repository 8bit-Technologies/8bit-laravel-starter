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
class Create extends Component
{
    use Notifies;

    public string $name = '';

    /** @var array<int, int> */
    public array $selectedPermissions = [];

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name'),
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
        $this->authorize('create roles');

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

        $role = Role::create(['name' => $validated['name']]);
        $role->syncPermissions($selectedNames);

        $this->notifySuccess('Role created successfully.');

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
        return view('livewire.admin.roles.create', [
            'permissions' => $this->permissions(),
        ]);
    }
}
