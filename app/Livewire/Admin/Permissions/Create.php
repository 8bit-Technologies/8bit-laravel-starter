<?php

namespace App\Livewire\Admin\Permissions;

use App\Livewire\Concerns\Notifies;
use Closure;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Spatie\Permission\Models\Permission;

#[Layout('layouts.admin')]
class Create extends Component
{
    use Notifies;

    public string $name = '';

    protected function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function (string $attribute, mixed $value, Closure $fail) {
                    $normalized = strtolower(trim($value));

                    if (Permission::query()->whereRaw('LOWER(TRIM(name)) = ?', [$normalized])->exists()) {
                        $fail('A permission with this name already exists.');
                    }
                },
            ],
        ];
    }

    public function save(): void
    {
        $this->authorize('create permissions');

        $validated = $this->validate();

        Permission::create(['name' => trim($validated['name'])]);

        $this->notifySuccess('Permission created successfully.');

        $this->redirectRoute('admin.permissions.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.admin.permissions.create');
    }
}
