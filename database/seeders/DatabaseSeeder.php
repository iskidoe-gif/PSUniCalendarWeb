<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@psu.edu'],
            [
                'name' => 'Planning Office',
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@123'),
                'role' => 'planning_office',
                'remember_token' => \Illuminate\Support\Str::random(10),
            ]
        );

        $user->forceFill([
            'name' => 'Planning Office',
            'email_verified_at' => now(),
            'password' => Hash::make('Admin@123'),
            'role' => 'planning_office',
            'remember_token' => \Illuminate\Support\Str::random(10),
        ])->save();

        // Create campus-specific admin users for testing.
        $campusAdmins = [
            [
                'email' => 'alaminos.admin@psu.local',
                'name' => 'Alaminos Campus Admin',
            ],
            [
                'email' => 'lingayen.admin@psu.local',
                'name' => 'Lingayen Campus Admin',
            ],
            [
                'email' => 'binmaley.admin@psu.local',
                'name' => 'Binmaley Campus Admin',
            ],
        ];

        foreach ($campusAdmins as $campusAdmin) {
            $admin = User::firstOrCreate(
                ['email' => $campusAdmin['email']],
                [
                    'name' => $campusAdmin['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('Admin@123'),
                    'role' => 'admin',
                    'remember_token' => \Illuminate\Support\Str::random(10),
                ]
            );

            $admin->forceFill([
                'name' => $campusAdmin['name'],
                'email_verified_at' => now(),
                'password' => Hash::make('Admin@123'),
                'role' => 'admin',
                'remember_token' => \Illuminate\Support\Str::random(10),
            ])->save();
        }

        // Create office-specific accounts for testing.
        $offices = [
            [
                'email' => 'registrar@psu.local',
                'name' => 'Office of the Registrar',
                'password' => 'Registrar@123',
            ],
            [
                'email' => 'studentaffairs@psu.local',
                'name' => 'Office of Student Affairs',
                'password' => 'StudentAffairs@123',
            ],
            [
                'email' => 'guidance@psu.local',
                'name' => 'Guidance Office',
                'password' => 'Guidance@123',
            ],
        ];

        foreach ($offices as $office) {
            $officeUser = User::firstOrCreate(
                ['email' => $office['email']],
                [
                    'name' => $office['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make($office['password']),
                    'role' => 'office',
                    'remember_token' => \Illuminate\Support\Str::random(10),
                ]
            );

            $officeUser->forceFill([
                'name' => $office['name'],
                'email_verified_at' => now(),
                'password' => Hash::make($office['password']),
                'role' => 'office',
                'remember_token' => \Illuminate\Support\Str::random(10),
            ])->save();
        }
    }
}