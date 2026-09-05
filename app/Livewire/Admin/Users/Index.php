<?php

namespace App\Livewire\Admin\Users;

use App\Livewire\Concerns\Notifies;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class Index extends Component
{
    use Notifies, WithPagination;

    public string $search = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteUser(int $userId): void
    {
        $this->authorize('delete users');

        $target = User::findOrFail($userId);

        // Hard invariants, checked unconditionally regardless of actor —
        // never expressed as Gate abilities, so never bypassed by the
        // Gate::before Super Admin shortcut. See PHASE-3-ROLES-PERMISSIONS.md §19.
        if ($target->is(auth()->user())) {
            $this->notifyError('You cannot delete your own account.');

            return;
        }

        if ($target->isSuperAdmin() && $target->isTheLastSuperAdmin()) {
            $this->notifyError('The last Super Admin cannot be deleted.');

            return;
        }

        if ($target->isSuperAdmin() && ! auth()->user()->isSuperAdmin()) {
            $this->notifyError('Only a Super Admin can delete another Super Admin.');

            return;
        }

        $target->delete();

        $this->notifySuccess('User deleted successfully.');
    }

    public function headers(): array
    {
        return [
            ['key' => 'name', 'label' => 'Name'],
            ['key' => 'email', 'label' => 'Email'],
            ['key' => 'roles', 'label' => 'Role(s)', 'sortable' => false],
            ['key' => 'verified', 'label' => 'Verified', 'sortable' => false],
            ['key' => 'created_at', 'label' => 'Created', 'format' => ['date', 'M d, Y']],
        ];
    }

    protected function users(): LengthAwarePaginator
    {
        return User::query()
            ->with('roles')
            ->when($this->search, fn ($query) => $query->where(
                fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%')
            ))
            ->orderBy('name')
            ->paginate(25);
    }

    public function render()
    {
        return view('livewire.admin.users.index', [
            'users' => $this->users(),
            'headers' => $this->headers(),
        ]);
    }
}
