<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Ensure Super Admin role exists
        $role = DB::table('roles')->where('name', 'Super Admin')->first();
        if (! $role) {
            $id = DB::table('roles')->insertGetId([
                'name' => 'Super Admin',
                'description' => 'Super Administrator role',
                'is_system_role' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $role = DB::table('roles')->where('id', $id)->first();
        }

        // Assign Super Admin role to any user with is_admin = true
        if (Schema::hasColumn('users', 'is_admin')) {
            $users = DB::table('users')->where('is_admin', true)->get();
            foreach ($users as $u) {
                // insert into user_roles if not already
                $exists = DB::table('user_roles')->where('user_id', $u->id)->where('role_id', $role->id)->exists();
                if (! $exists) {
                    DB::table('user_roles')->insert(['user_id' => $u->id, 'role_id' => $role->id]);
                }
            }

            // drop the column
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_admin');
            });
        }
    }

    public function down(): void
    {
        // recreate is_admin column (default false)
        if (! Schema::hasColumn('users', 'is_admin')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_admin')->default(false)->after('password');
            });
        }

        // optionally remove role assignments added (skip to be safe)
    }
};