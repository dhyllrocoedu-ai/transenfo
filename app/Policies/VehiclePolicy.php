<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\Vehicle;

class VehiclePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Vehicle $vehicle): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $vehicle->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isRole(
            Role::SuperAdmin,
            Role::Administrator,
            Role::Enforcer
        );
    }

    public function update(User $user, Vehicle $vehicle): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator);
    }

    public function delete(User $user, Vehicle $vehicle): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator);
    }
}
