<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class AdminRoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(
            ['name' => 'Super Admin'],
            ['description' => 'Super Administrator role', 'is_system_role' => true]
        );
    }
}
