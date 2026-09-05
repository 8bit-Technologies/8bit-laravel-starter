<?php

use App\Support\ProtectedPermissions;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\QueryException;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

it('lists exactly the thirteen protected permissions', function () {
    expect(ProtectedPermissions::all())->toHaveCount(13)
        ->and(ProtectedPermissions::all())->toContain('access dashboard', 'view users', 'delete permissions');
});

it('recognizes a protected permission name regardless of case or whitespace', function () {
    expect(ProtectedPermissions::contains('access dashboard'))->toBeTrue()
        ->and(ProtectedPermissions::contains('  ACCESS DASHBOARD  '))->toBeTrue()
        ->and(ProtectedPermissions::contains('view properties'))->toBeFalse();
});

it('seeds exactly the thirteen protected permissions idempotently', function () {
    $this->seed(RolePermissionSeeder::class);
    $this->seed(RolePermissionSeeder::class);

    expect(Permission::count())->toBe(13);

    foreach (ProtectedPermissions::all() as $permission) {
        expect(Permission::where('name', $permission)->count())->toBe(1);
    }
});

it('grants the full permission catalogue to the Admin role and none to Super Admin', function () {
    $this->seed(RolePermissionSeeder::class);

    $admin = Role::findByName('Admin');
    $superAdmin = Role::findByName('Super Admin');

    expect($admin->permissions()->count())->toBe(13)
        ->and($superAdmin->permissions()->count())->toBe(0);
});

it('prevents creating a duplicate permission with a protected name', function () {
    Permission::create(['name' => 'access dashboard']);

    expect(fn () => Permission::create(['name' => 'access dashboard']))
        ->toThrow(PermissionAlreadyExists::class);
});

it('prevents renaming a different permission into a protected name at the database layer', function () {
    Permission::create(['name' => 'access dashboard']);
    $other = Permission::create(['name' => 'view properties']);

    expect(fn () => $other->update(['name' => 'access dashboard']))
        ->toThrow(QueryException::class);
});
