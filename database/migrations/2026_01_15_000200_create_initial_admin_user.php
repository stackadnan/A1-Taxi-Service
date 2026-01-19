<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('users')) {
            return;
        }

        $email = 'example@gmail.com';

        if (DB::table('users')->where('email', $email)->exists()) {
            return;
        }

        // Ensure Super Admin role exists
        $roleId = null;
        try {
            $role = DB::table('roles')->where('name', 'Super Admin')->first();
            if (! $role) {
                $roleId = DB::table('roles')->insertGetId([
                    'name' => 'Super Admin',
                    'description' => 'Super Administrator role',
                    'is_system_role' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            } else {
                $roleId = $role->id;
            }
        } catch (\Throwable $e) {}

        // User data
        $userData = [
            'name' => 'Admin',
            'email' => $email,
            'password' => Hash::make('secret'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add username if column exists
        if (Schema::hasColumn('users', 'username')) {
            $userData['username'] = 'admin';
        }

        if (Schema::hasColumn('users', 'is_admin')) {
            $userData['is_admin'] = true;
        }

        if (Schema::hasColumn('users', 'is_active')) {
            $userData['is_active'] = true;
        }

        $userId = DB::table('users')->insertGetId($userData);

        // Attach role
        if ($roleId && Schema::hasTable('user_roles')) {
            try {
                DB::table('user_roles')->insertOrIgnore([
                    'user_id' => $userId,
                    'role_id' => $roleId
                ]);
            } catch (\Throwable $e) {}
        }
    }

    public function down(): void
    {
        $email = 'example@gmail.com';

        if (Schema::hasTable('users')) {
            $user = DB::table('users')->where('email', $email)->first();
            if ($user) {
                if (Schema::hasTable('user_roles')) {
                    DB::table('user_roles')->where('user_id', $user->id)->delete();
                }
                DB::table('users')->where('id', $user->id)->delete();
            }
        }
    }
};
