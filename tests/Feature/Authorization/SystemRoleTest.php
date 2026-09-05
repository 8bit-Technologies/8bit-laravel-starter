<?php

use App\Enums\SystemRole;

it('identifies the exact protected role name', function () {
    expect(SystemRole::isProtectedName('Super Admin'))->toBeTrue();
});

it('identifies the protected role name regardless of case or whitespace', function () {
    expect(SystemRole::isProtectedName('super admin'))->toBeTrue()
        ->and(SystemRole::isProtectedName('  SUPER ADMIN  '))->toBeTrue();
});

it('does not treat an ordinary role name as protected', function () {
    expect(SystemRole::isProtectedName('Manager'))->toBeFalse()
        ->and(SystemRole::isProtectedName('Admin'))->toBeFalse();
});
