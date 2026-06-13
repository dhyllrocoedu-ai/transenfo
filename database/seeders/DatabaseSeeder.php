<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Driver;
use App\Models\User;
use App\Models\Vehicle;
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

        $owner = User::where('email', 'owner@example.com')->first();

        $driver = Driver::updateOrCreate(
            ['license_number' => 'DL-2024-001234'],
            [
                'user_id' => $owner->id,
                'first_name' => 'Pedro',
                'last_name' => 'Santos',
                'license_expiry' => now()->addYears(3),
                'phone' => '09171234567',
                'email' => 'owner@itevcms.local',
                'address' => '123 Main St, Metro City',
            ]
        );

        Vehicle::updateOrCreate(
            ['plate_number' => 'ABC-1234'],
            [
                'owner_id' => $owner->id,
                'driver_id' => $driver->id,
                'classification' => 'Sedan',
                'make' => 'Toyota',
                'model' => 'Vios',
                'color' => 'White',
                'year' => 2022,
                'registration_status' => 'active',
            ]
        );

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
