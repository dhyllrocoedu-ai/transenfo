<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Citation;
use App\Models\User;

class CitationPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Citation $citation): bool
    {
        if ($user->isStaff()) {
            return true;
        }

        return $citation->vehicle?->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isRole(
            Role::SuperAdmin,
            Role::Administrator,
            Role::Enforcer
        );
    }

    public function update(User $user, Citation $citation): bool
    {
        return $user->isRole(Role::SuperAdmin, Role::Administrator);
    }
}
