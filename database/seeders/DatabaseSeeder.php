<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Create a stable test user without violating unique constraints when seeding repeatedly
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => Hash::make('password'),
                'is_active' => true,
            ]
        );

        // Seed permissions, admin role and user
        $this->call([
            \Database\Seeders\PermissionsSeeder::class,
            \Database\Seeders\AdminRoleSeeder::class,
            \Database\Seeders\AdminUserSeeder::class,
            \Database\Seeders\RoleUsersSeeder::class,
            \Database\Seeders\BroadcastSeeder::class,
            \Database\Seeders\ZoneCsvSeeder::class,
        ]);
    }
}
