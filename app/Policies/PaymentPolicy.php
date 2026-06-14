<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isStaff() || $user->isRole(Role::VehicleOwner);
    }

    public function view(User $user, Payment $payment): bool
    {
        if ($user->isRole(Role::SuperAdmin, Role::Administrator, Role::Cashier)) {
            return true;
        }

        return $payment->citation?->vehicle?->owner_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isStaff();
    }
}
