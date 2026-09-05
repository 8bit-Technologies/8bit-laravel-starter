<?php

namespace App\Livewire\Admin\Roles;

use App\Enums\SystemRole;
use App\Livewire\Concerns\Notifies;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class Index extends Component
{
    use Notifies, WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteRole(int $roleId): void
    {
        $this->authorize('delete roles');

        $role = Role::findOrFail($roleId);

        if (SystemRole::isProtectedName($role->name)) {
            $this->notifyError('The Super Admin role is protected and cannot be deleted.');

            return;
        }

        if ($role->users()->exists()) {
            $this->notifyError('This role is still assigned to one or more users and cannot be deleted.');

            return;
        }

        $role->delete();

        $this->notifySuccess('Role deleted successfully.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'permissions', 'label' => 'Permissions', 'sortable' => false],
        ];
    }

    protected function roles(): LengthAwarePaginator
    {
        return Role::query()
            ->with('permissions')
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(25);
    }

    public function render()
    {
        return view('livewire.admin.roles.index', [
            'roles' => $this->roles(),
            'headers' => $this->headers(),
        ]);
    }
}
