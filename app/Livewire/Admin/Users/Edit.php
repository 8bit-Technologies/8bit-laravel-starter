<?php

namespace App\Livewire\Admin\Users;

use App\Enums\SystemRole;
use App\Livewire\Concerns\Notifies;
use App\Models\User;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class Edit extends Component
{
    use Notifies;

    public User $targetUser;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $roleId = '';

    public function mount(User $user): void
    {
        // Resource-specific authorization the route's plain "update users"
        // ability check cannot express — a normal Admin may never reach a
        // Super Admin's edit page. See PHASE-3-ROLES-PERMISSIONS.md §8/§21.
        $this->authorize('update', $user);

        $this->targetUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;

        $role = $user->roles->first();
        $this->roleId = $role ? (string) $role->getKey() : '';
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($this->targetUser->id)],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roleId' => [
                function (string $attribute, mixed $value, Closure $fail) {
                    if ($value !== '' && ! Role::whereKey($value)->exists()) {
                        $fail('The selected role is invalid.');
                    }
                },
            ],
        ];
    }

    public function save(): void
    {
        $this->authorize('update', $this->targetUser);

        $validated = $this->validate();

        $role = $validated['roleId'] !== '' ? Role::find($validated['roleId']) : null;
        $roleIsProtected = $role && SystemRole::isProtectedName($role->name);

        if ($roleIsProtected && ! auth()->user()->isSuperAdmin()) {
            $this->notifyError('Only a Super Admin can assign the Super Admin role.');

            return;
        }

        // Hard invariant, checked unconditionally regardless of actor — the
        // sole remaining Super Admin can never lose the role, including by
        // themselves. See PHASE-3-ROLES-PERMISSIONS.md §19.
        if ($this->targetUser->isTheLastSuperAdmin() && ! $roleIsProtected) {
            $this->notifyError('The last Super Admin must keep the Super Admin role.');

            return;
        }

        $this->targetUser->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        if ($this->targetUser->isDirty('email')) {
            $this->targetUser->email_verified_at = null;
        }

        if (filled($validated['password'])) {
            $this->targetUser->password = $validated['password'];
        }

        $this->targetUser->save();

        $this->targetUser->syncRoles($role ? [$role] : []);

        $this->notifySuccess('User updated successfully.');

        $this->redirectRoute('admin.users.index', navigate: true);
    }

    /**
     * @return Collection<int, Role>
     */
    protected function roles(): Collection
    {
        $query = Role::query()->orderBy('name');

        if (! auth()->user()->isSuperAdmin()) {
            $query->where('name', '!=', SystemRole::SuperAdmin->value);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.admin.users.edit', [
            'roles' => $this->roles(),
        ]);
    }
}
