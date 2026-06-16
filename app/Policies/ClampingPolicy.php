<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\ClampingRecord;
use App\Models\User;

class ClampingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff();
    }

    public function view(User $user, ClampingRecord $clampingRecord): bool
    {
        return $user->isStaff();
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
