<?php

namespace App\View\Composers;

use App\Enums\Role;
use Illuminate\View\View;

class NavigationComposer
{
    public function compose(View $view): void
    {
        $user = auth()->user();
        $items = [];

        if (! $user) {
            $view->with('navItems', $items);

            return;
        }

        $items[] = ['label' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'speedometer2'];

        if ($user->isRole(Role::VehicleOwner)) {
            $items[] = ['label' => 'My Citations', 'route' => 'owner.citations', 'icon' => 'file-earmark-text'];
            $items[] = ['label' => 'My Vehicles', 'route' => 'owner.vehicles', 'icon' => 'car-front'];
            $items[] = ['label' => 'Clamping Status', 'route' => 'owner.clamping', 'icon' => 'lock'];
            $items[] = ['label' => 'Appeals', 'route' => 'appeals.index', 'icon' => 'exclamation-circle'];
        } else {
            if ($user->isRole(Role::SuperAdmin, Role::Administrator, Role::Enforcer)) {
                $items[] = ['label' => 'Drivers', 'route' => 'drivers.index', 'icon' => 'person-badge'];
                $items[] = ['label' => 'Vehicles', 'route' => 'vehicles.index', 'icon' => 'car-front'];
            }

            if ($user->isRole(Role::SuperAdmin, Role::Administrator, Role::Enforcer)) {
                $items[] = ['label' => 'Citations', 'route' => 'citations.index', 'icon' => 'file-earmark-text'];
                $items[] = ['label' => 'Appeals', 'route' => 'appeals.index', 'icon' => 'exclamation-circle'];
            }

            if ($user->isRole(Role::SuperAdmin, Role::Administrator, Role::Cashier)) {
                $items[] = ['label' => 'Payments', 'route' => 'payments.index', 'icon' => 'cash-coin'];
            }

            if ($user->isRole(Role::SuperAdmin, Role::Administrator, Role::ClampingOfficer)) {
                $items[] = ['label' => 'Clamping', 'route' => 'clamping.index', 'icon' => 'lock'];
                $items[] = ['label' => 'Releases', 'route' => 'releases.index', 'icon' => 'unlock'];
            }

            if ($user->isRole(Role::SuperAdmin, Role::Administrator)) {
                $items[] = ['label' => 'Users', 'route' => 'users.index', 'icon' => 'people'];
            }
        }

        $view->with('navItems', $items);
    }
}
