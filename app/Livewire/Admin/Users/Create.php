<?php

namespace App\Livewire\Admin\Users;

use App\Enums\SystemRole;
use App\Livewire\Concerns\Notifies;
use App\Models\User;
use Closure;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Role;

#[Layout('layouts.admin')]
class Create extends Component
{
    use Notifies;

    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $roleId = '';

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
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
        $this->authorize('create users');

        $validated = $this->validate();

        $role = $validated['roleId'] !== '' ? Role::find($validated['roleId']) : null;

        if ($role && SystemRole::isProtectedName($role->name) && ! auth()->user()->isSuperAdmin()) {
            $this->notifyError('Only a Super Admin can assign the Super Admin role.');

            return;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        // This account is created by an operator inside the Admin Panel;
        // the ordinary self-verification loop does not apply here, matching
        // the same rationale used by the 8bit:create-super-admin command.
        $user->forceFill(['email_verified_at' => now()])->save();

        if ($role) {
            $user->assignRole($role);
        }

        $this->notifySuccess('User created successfully.');

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
        return view('livewire.admin.users.create', [
            'roles' => $this->roles(),
        ]);
    }
}
