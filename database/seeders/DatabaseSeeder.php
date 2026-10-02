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

        // Create office-specific accounts for testing, grouped by campus.
        $offices = [
            [
                'email' => 'registrar@psu.local',
                'name' => 'Office of the Registrar',
                'password' => 'Registrar@123',
                'campus' => 'Lingayen Campus',
            ],
            [
                'email' => 'studentaffairs@psu.local',
                'name' => 'Office of Student Affairs',
                'password' => 'StudentAffairs@123',
                'campus' => 'Lingayen Campus',
            ],
            [
                'email' => 'guidance@psu.local',
                'name' => 'Guidance Office',
                'password' => 'Guidance@123',
                'campus' => 'Lingayen Campus',
            ],
            [
                'email' => 'registrar.alaminos@psu.local',
                'name' => 'Office of the Registrar - Alaminos',
                'password' => 'Registrar@123',
                'campus' => 'Alaminos Campus',
            ],
            [
                'email' => 'studentaffairs.alaminos@psu.local',
                'name' => 'Office of Student Affairs - Alaminos',
                'password' => 'StudentAffairs@123',
                'campus' => 'Alaminos Campus',
            ],
            [
                'email' => 'registrar.binmaley@psu.local',
                'name' => 'Office of the Registrar - Binmaley',
                'password' => 'Registrar@123',
                'campus' => 'Binmaley Campus',
            ],
            [
                'email' => 'studentaffairs.binmaley@psu.local',
                'name' => 'Office of Student Affairs - Binmaley',
                'password' => 'StudentAffairs@123',
                'campus' => 'Binmaley Campus',
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
                    'campus' => $office['campus'],
                    'remember_token' => \Illuminate\Support\Str::random(10),
                ]
            );

            $officeUser->forceFill([
                'name' => $office['name'],
                'email_verified_at' => now(),
                'password' => Hash::make($office['password']),
                'role' => 'office',
                'campus' => $office['campus'],
                'remember_token' => \Illuminate\Support\Str::random(10),
            ])->save();
        }

        // University offices and campus offices. All use the password Office@123.
        // 'University Offices' and the campus names are the sidebar groups in the Planning Office.
        $officeAccounts = [
            // University Offices
            ['email' => 'op@psu.local', 'name' => 'Office of the President (OP)', 'campus' => 'University Offices'],
            ['email' => 'ovpaass@psu.local', 'name' => 'Office of the Vice President for Academic Affairs and Student Services (OVPAASS)', 'campus' => 'University Offices'],
            ['email' => 'ovpafm@psu.local', 'name' => 'Office of the Vice President for Administration and Finance Management (OVPAFM)', 'campus' => 'University Offices'],
            ['email' => 'ovprei@psu.local', 'name' => 'Office of the Vice President for Research, Extension and Innovation (OVPREI)', 'campus' => 'University Offices'],
            ['email' => 'ovpqa@psu.local', 'name' => 'Office of the Vice President for Quality Assurance (OVPQA)', 'campus' => 'University Offices'],
            ['email' => 'ovplia@psu.local', 'name' => 'Office of the Vice President for Local and International Affairs (OVPLIA)', 'campus' => 'University Offices'],
            ['email' => 'sas@psu.local', 'name' => 'Student Affairs and Services (SAS)', 'campus' => 'University Offices'],
            ['email' => 'ous@psu.local', 'name' => 'Open University System (OUS)', 'campus' => 'University Offices'],

            // Campuses
            ['email' => 'alaminos@psu.local', 'name' => 'PSU Alaminos Campus', 'campus' => 'Alaminos Campus'],
            ['email' => 'binmaley@psu.local', 'name' => 'PSU Binmaley Campus', 'campus' => 'Binmaley Campus'],
            ['email' => 'lingayen@psu.local', 'name' => 'PSU Lingayen Campus', 'campus' => 'Lingayen Campus'],
        ];

        foreach ($officeAccounts as $account) {
            $officeUser = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('Office@123'),
                    'role' => 'office',
                    'campus' => $account['campus'],
                    'remember_token' => \Illuminate\Support\Str::random(10),
                ]
            );

            $officeUser->forceFill([
                'name' => $account['name'],
                'email_verified_at' => now(),
                'password' => Hash::make('Office@123'),
                'role' => 'office',
                'campus' => $account['campus'],
                'remember_token' => \Illuminate\Support\Str::random(10),
            ])->save();
        }
    }
}