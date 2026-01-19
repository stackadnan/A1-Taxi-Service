<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions (derived from matrix)
        $permissions = [
            // Booking
            'booking.view','booking.edit','booking.create',
            // Pricing
            'pricing.view','pricing.edit','pricing.create',
            // Users
            'user.view','user.edit','user.create','user.delete',
            // Drivers
            'driver.view','driver.edit','driver.create',
            // Accounts
            'accounts.view','accounts.edit','accounts.create',
            // Concerns/Reviews
            'concern.view','concern.edit','concern.create',
            // Admin settings & notifications
            'admin_settings.view','admin_settings.edit',
            'notifications.view','notifications.edit',
            // Dashboard feature views
            'dashboard.view_counts_amount','dashboard.booking_basic_info','dashboard.booking_source_url','dashboard.search_count','dashboard.quotes_view'
        ];

        // Insert permissions as module/action pairs (matches migration schema)
        foreach ($permissions as $p) {
            $parts = explode('.', $p, 2);
            $module = $parts[0] ?? null;
            $action = $parts[1] ?? null;
            if (! $module || ! $action) continue;
            Permission::firstOrCreate(['module' => $module, 'action' => $action], ['description' => null]);
        }

        // Create roles if missing
        $roles = ['Super Admin','Manager','Controller','Operator','Monitoring'];
        foreach ($roles as $r) {
            Role::firstOrCreate(['name' => $r]);
        }

        // Ensure no other roles remain — limit roles to the allowed set
        $allowed = ['Super Admin','Manager','Controller','Operator','Monitoring'];
        Role::whereNotIn('name', $allowed)->delete();

        // Assign permissions according to matrix (approximation)
        $rolePermissions = [
            'Super Admin' => $permissions, // all
            'Manager' => [
                'booking.view','booking.edit','booking.create',
                'pricing.view','pricing.edit',
                'user.view','user.edit','user.delete',
                'driver.view','driver.edit',
                'accounts.view','concern.view','admin_settings.view','notifications.view',
                'dashboard.view_counts_amount','dashboard.search_count'
            ],
            'Controller' => [
                'booking.view','booking.edit',
                'driver.view',
                'accounts.view','concern.view','notifications.view',
                'dashboard.search_count'
            ],
            'Operator' => [
                'booking.view','booking.create',
                'driver.view',
                'concern.view','notifications.view'
            ],
            'Monitoring' => [
                'booking.view','dashboard.view_counts_amount','dashboard.search_count'
            ]
        ];

        foreach ($rolePermissions as $roleName => $perms) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $permIds = [];
                foreach ($perms as $permName) {
                    $parts = explode('.', $permName, 2);
                    $module = $parts[0] ?? null;
                    $action = $parts[1] ?? null;
                    if (! $module || ! $action) continue;
                    $p = Permission::where('module', $module)->where('action', $action)->first();
                    if ($p) $permIds[] = $p->id;
                }

                if (! empty($permIds)) {
                    $role->permissions()->syncWithoutDetaching($permIds);
                }
            }
        }
    }
}
