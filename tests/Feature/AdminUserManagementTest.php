<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Str;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed permissions and roles
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_admin_can_create_user()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $response = $this->actingAs($admin)->get(route('admin.users.create'));
        $response->assertStatus(200);

        $email = 'newuser@example.com';
        $response = $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', ['email' => $email]);
    }

    public function test_user_without_permission_cannot_access_create()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('admin.users.create'));
        $response->assertStatus(403);

        $response = $this->actingAs($user)->post(route('admin.users.store'), [
            'name' => 'Blocked',
            'email' => 'blocked@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_delete_user()
    {
        $this->seed(\Database\Seeders\PermissionsSeeder::class);

        // Create a role that has user.delete permission
        $role = Role::firstOrCreate(['name' => 'deleter']);
        [$module, $action] = explode('.', 'user.delete', 2);
        $perm = Permission::firstOrCreate(['module' => $module, 'action' => $action]);
        $role->permissions()->syncWithoutDetaching([$perm->id]);

        $actor = User::factory()->create();
        $actor->roles()->sync([$role->id]);

        $target = User::factory()->create();

        $response = $this->actingAs($actor)->delete(route('admin.users.destroy', $target));
        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
