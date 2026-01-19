<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminAuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_username()
    {
        $user = User::factory()->create(['name' => 'testuser', 'password' => bcrypt('secret')]);

        $response = $this->post(route('admin.login.post'), ['username' => 'testuser', 'password' => 'secret']);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_show_error()
    {
        $response = $this->post(route('admin.login.post'), ['username' => 'doesnotexist', 'password' => 'wrong']);
        $response->assertSessionHasErrors('username');
    }
}
