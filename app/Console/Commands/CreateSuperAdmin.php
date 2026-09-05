<?php

namespace App\Console\Commands;

use App\Enums\SystemRole;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Role;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

class CreateSuperAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '8bit:create-super-admin {--force : Skip the confirmation prompt when a Super Admin already exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Super Admin account for this installation';

    public function handle(): int
    {
        $superAdminRole = Role::where('name', SystemRole::SuperAdmin->value)->first();

        $existing = $superAdminRole
            ? User::role($superAdminRole)->get(['name', 'email'])
            : collect();

        if ($existing->isNotEmpty() && ! $this->option('force')) {
            $this->warn('A Super Admin already exists:');

            foreach ($existing as $user) {
                $this->line("  - {$user->name} ({$user->email})");
            }

            if (! confirm('Create another Super Admin anyway?', default: false)) {
                $this->info('No changes made.');

                return self::SUCCESS;
            }
        }

        $name = text(
            label: 'Name',
            required: true,
        );

        $email = text(
            label: 'Email',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['email' => $value],
                ['email' => ['required', 'string', 'email', 'max:255', 'unique:users,email']],
            )->errors()->first('email'),
        );

        $password = password(
            label: 'Password',
            required: true,
            validate: fn (string $value) => Validator::make(
                ['password' => $value],
                ['password' => ['required', Password::defaults()]],
            )->errors()->first('password'),
        );

        password(
            label: 'Confirm password',
            required: true,
            validate: fn (string $value) => $value === $password ? null : 'The passwords do not match.',
        );

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);

        // email_verified_at is intentionally not mass-assignable; this
        // account is created by an operator with CLI access, so the
        // ordinary email-verification loop does not apply here.
        $user->forceFill(['email_verified_at' => now()])->save();

        $role = $superAdminRole ?? Role::firstOrCreate(['name' => SystemRole::SuperAdmin->value]);
        $user->assignRole($role);

        $this->info("Super Admin created: {$user->name} ({$user->email})");

        return self::SUCCESS;
    }
}
