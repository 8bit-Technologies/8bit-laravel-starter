<?php

namespace Database\Seeders;

use App\Enums\SystemRole;
use App\Support\ProtectedPermissions;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seed the starter's system roles and permissions.
     *
     * Idempotent: safe to run multiple times without creating duplicates.
     * Creates no users — the first Super Admin is created separately via
     * the `8bit:create-super-admin` console command.
     */
    public function run(): void
    {
        foreach (ProtectedPermissions::all() as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        $superAdmin = Role::firstOrCreate(['name' => SystemRole::SuperAdmin->value]);
        $admin = Role::firstOrCreate(['name' => SystemRole::Admin->value]);
        Role::firstOrCreate(['name' => SystemRole::Member->value]);

        // Admin receives the full starter permission catalogue. Super Admin
        // needs none — its access comes from the Gate::before bypass, not
        // from an assigned permission set (PHASE-3-ROLES-PERMISSIONS.md §11).
        $admin->syncPermissions(ProtectedPermissions::all());
        $superAdmin->syncPermissions([]);
    }
}
