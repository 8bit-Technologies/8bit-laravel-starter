<?php

namespace App\Livewire\Admin\Permissions;

use App\Livewire\Concerns\Notifies;
use App\Support\ProtectedPermissions;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.admin')]
class Index extends Component
{
    use Notifies, WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deletePermission(int $permissionId): void
    {
        $this->authorize('delete permissions');

        $permission = Permission::findOrFail($permissionId);

        if (ProtectedPermissions::contains($permission->name)) {
            $this->notifyError('This is a protected system permission and cannot be deleted.');

            return;
        }

        if ($permission->roles()->exists() || $permission->users()->exists()) {
            $this->notifyError('This permission is currently assigned and cannot be deleted.');

            return;
        }

        $permission->delete();

        $this->notifySuccess('Permission deleted successfully.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'id', 'label' => '#', 'class' => 'w-1'],
            ['key' => 'name', 'label' => 'Name'],
        ];
    }

    protected function permissions(): LengthAwarePaginator
    {
        return Permission::query()
            ->when($this->search, fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))
            ->orderBy('name')
            ->paginate(25);
    }

    public function render()
    {
        return view('livewire.admin.permissions.index', [
            'permissions' => $this->permissions(),
            'headers' => $this->headers(),
        ]);
    }
}
