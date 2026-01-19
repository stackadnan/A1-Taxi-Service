<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class RoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Define users for each role
        $users = [
            [
                'name' => 'Super Admin',
                'username' => 'superadmin',
                'email' => 'superadmin@airport.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Super Admin',
            ],
            [
                'name' => 'Manager User',
                'username' => 'manager',
                'email' => 'manager@airport.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Manager',
            ],
            [
                'name' => 'Controller User',
                'username' => 'controller',
                'email' => 'controller@airport.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Controller',
            ],
            [
                'name' => 'Operator User',
                'username' => 'operator',
                'email' => 'operator@airport.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Operator',
            ],
            [
                'name' => 'Monitoring User',
                'username' => 'monitoring',
                'email' => 'monitoring@airport.com',
                'password' => Hash::make('password'),
                'is_active' => true,
                'role' => 'Monitoring',
            ],
        ];

        foreach ($users as $userData) {
            $roleName = $userData['role'];
            unset($userData['role']);

            // Create or update user
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            // Assign role
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                // Sync the role (removes other roles, assigns this one)
                $user->roles()->sync([$role->id]);
            }
        }

        $this->command->info('✓ Created/updated 5 users with their respective roles');
        $this->command->info('  - superadmin@airport.com (Super Admin)');
        $this->command->info('  - manager@airport.com (Manager)');
        $this->command->info('  - controller@airport.com (Controller)');
        $this->command->info('  - operator@airport.com (Operator)');
        $this->command->info('  - monitoring@airport.com (Monitoring)');
        $this->command->info('  All passwords: password');
    }
}
