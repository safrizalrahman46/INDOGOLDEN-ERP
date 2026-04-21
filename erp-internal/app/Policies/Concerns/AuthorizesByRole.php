<?php

namespace App\Policies\Concerns;

use App\Enums\UserRole;
use App\Models\User;

trait AuthorizesByRole
{
    /**
     * @param  list<UserRole>  $roles
     */
    protected function hasRole(User $user, array $roles): bool
    {
        return $user->hasAnyRole(array_map(static fn (UserRole $role) => $role->value, $roles));
    }

    protected function isOwner(User $user): bool
    {
        return $user->hasRole(UserRole::Owner->value);
    }

    protected function isBranch(User $user): bool
    {
        return $user->hasRole(UserRole::Branch->value);
    }
}
