<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_admin_login_for_admin_routes()
    {
        $response = $this->get('/admin/bookings');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_guest_gets_401_json_for_api_requests()
    {
        $response = $this->getJson('/admin/bookings');
        $response->assertStatus(401);
    }
}
