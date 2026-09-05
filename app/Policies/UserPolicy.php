<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the actor can update the target user.
     *
     * A normal Admin may never update a Super Admin's account — only
     * another Super Admin may. See PHASE-3-ROLES-PERMISSIONS.md §8.
     */
    public function update(User $user, User $model): bool
    {
        if ($model->isSuperAdmin() && ! $user->isSuperAdmin()) {
            return false;
        }

        return $user->can('update users');
    }
}
