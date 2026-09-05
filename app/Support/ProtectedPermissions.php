<?php

namespace App\Support;

final class ProtectedPermissions
{
    /**
     * The full list of protected/system permission names.
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return config('permissions.protected', []);
    }

    /**
     * Determine whether the given permission name is protected, regardless
     * of casing or surrounding whitespace.
     */
    public static function contains(string $name): bool
    {
        $name = strtolower(trim($name));

        foreach (self::all() as $protected) {
            if (strtolower($protected) === $name) {
                return true;
            }
        }

        return false;
    }
}
