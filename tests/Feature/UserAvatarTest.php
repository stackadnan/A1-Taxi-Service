<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class UserAvatarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_initials_display_when_no_avatar()
    {
        $user = User::factory()->create(['name' => 'Jane Doe']);
        $role = Role::where('name', 'Super Admin')->first();
        $user->roles()->sync([$role->id]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('JD');
    }

    public function test_avatar_image_shows_if_present()
    {
        $user = User::factory()->create(['name' => 'Samuel', 'avatar' => 'https://example.test/avatar.jpg']);
        $role = Role::where('name', 'Super Admin')->first();
        $user->roles()->sync([$role->id]);

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('https://example.test/avatar.jpg');
    }
}