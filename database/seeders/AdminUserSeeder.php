<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $role = Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Super Administrator role', 'is_system_role' => true]
        );

        $user = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('secret'),
            ]
        );

        $user->roles()->syncWithoutDetaching([$role->id]);
    }
}
