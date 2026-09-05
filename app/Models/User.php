<?php

namespace App\Models;

use App\Enums\SystemRole;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The single, centralized check for the protected Super Admin concept.
     * No other file should perform a raw hasRole('Super Admin') comparison
     * — see PHASE-3-ROLES-PERMISSIONS.md §6.1, Rule 1.
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole(SystemRole::SuperAdmin->value);
    }

    /**
     * Whether this user is the sole remaining Super Admin. This hard
     * invariant must be checked unconditionally wherever a mutation could
     * remove Super Admin protection — it is never expressed as a Gate
     * ability, so it is never bypassed by the Gate::before Super Admin
     * shortcut. See PHASE-3-ROLES-PERMISSIONS.md §19.
     */
    public function isTheLastSuperAdmin(): bool
    {
        return $this->isSuperAdmin()
            && self::role(SystemRole::SuperAdmin->value)->count() === 1;
    }
}
