<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use App\Models\ViolationType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Super Administrator', 'email' => 'admin@example.com', 'role' => Role::SuperAdmin, 'password' => 'Admin@123'],
            ['name' => 'Transportation Enforcer', 'email' => 'enforcer@example.com', 'role' => Role::Enforcer, 'password' => 'Enforcer@123'],
            ['name' => 'Clamping Officer', 'email' => 'clamp@example.com', 'role' => Role::ClampingOfficer, 'password' => 'Clamp@123'],
            ['name' => 'Cashier', 'email' => 'cashier@example.com', 'role' => Role::Cashier, 'password' => 'Cashier@123'],
            ['name' => 'Vehicle Owner', 'email' => 'owner@example.com', 'role' => Role::VehicleOwner, 'password' => 'Owner@123'],
        ];

        foreach ($users as $data) {
            User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'role' => $data['role'],
                    'password' => Hash::make($data['password']),
                    'is_active' => true,
                ]
            );
        }

        $violations = [
            ['code' => 'NO-PARK', 'name' => 'Illegal Parking', 'description' => 'Parking in a no-parking zone', 'penalty_amount' => 500],
            ['code' => 'RED-LIGHT', 'name' => 'Red Light Violation', 'description' => 'Running a red traffic light', 'penalty_amount' => 1500],
            ['code' => 'NO-HELMET', 'name' => 'No Helmet', 'description' => 'Riding without proper helmet', 'penalty_amount' => 300],
            ['code' => 'OVERLOAD', 'name' => 'Overloading', 'description' => 'Vehicle exceeding passenger/cargo limit', 'penalty_amount' => 1000],
            ['code' => 'NO-REG', 'name' => 'Unregistered Vehicle', 'description' => 'Operating an unregistered vehicle', 'penalty_amount' => 2000],
        ];

        foreach ($violations as $violation) {
            ViolationType::updateOrCreate(['code' => $violation['code']], $violation);
        }
    }
}
