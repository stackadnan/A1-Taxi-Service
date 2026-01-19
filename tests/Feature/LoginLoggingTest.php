<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class LoginLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_records_ip_and_updates_user()
    {
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);

        $password = 'secret123';
        $user = User::factory()->create([
            'email' => 'loginuser@example.com',
            'password' => bcrypt($password),
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect(route('admin.dashboard'));

        $this->assertDatabaseHas('user_login_logs', [
            'user_id' => $user->id,
            'ip_address' => '127.0.0.1',
        ]);

        $this->assertDatabaseMissing('user_login_logs', ['ip_address' => null]);

        $user->refresh();
        $this->assertNotNull($user->last_login_at);
        $this->assertEquals('127.0.0.1', $user->last_login_ip);
    }
}
