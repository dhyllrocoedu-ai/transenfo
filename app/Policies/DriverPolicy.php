<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Driver;
use App\Models\User;

class DriverPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, Driver $driver): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $driver->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator, Role::Enforcer);
    }

    public function update(User $user, Driver $driver): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator);
    }

    public function delete(User $user, Driver $driver): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator);
    }
}
