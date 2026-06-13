<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use App\Models\VehicleRelease;

class ReleasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, VehicleRelease $vehicleRelease): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $vehicleRelease->clampingRecord?->vehicle?->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isRole(
            Role::SuperAdmin,
            Role::Administrator,
            Role::ClampingOfficer
        );
    }
}
