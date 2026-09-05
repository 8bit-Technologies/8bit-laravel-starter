<?php

namespace App\Enums;

enum SystemRole: string
{
    case SuperAdmin = 'Super Admin';
    case Admin = 'Admin';
    case Member = 'Member';

    /**
     * Determine whether the given role name refers to the protected
     * Super Admin role, regardless of casing or surrounding whitespace.
     *
     * This is the single source of truth for "is this the protected role
     * name" and must be used instead of a raw string comparison anywhere
     * a role name needs to be checked against the Super Admin concept.
     */
    public static function isProtectedName(string $name): bool
    {
        return strcasecmp(trim($name), self::SuperAdmin->value) === 0;
    }
}
