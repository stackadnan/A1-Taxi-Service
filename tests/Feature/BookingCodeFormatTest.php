<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;

class BookingCodeFormatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed permissions and roles so route middleware permits creating bookings
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_manual_booking_has_cd_numeric_booking_code()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $payload = [
            'passenger_name' => 'Test Passenger',
            'phone' => '07123456789',
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => now()->format('H:i'),
        ];

        $response = $this->actingAs($admin)->postJson(route('admin.bookings.manual.store'), $payload);
        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $booking = \App\Models\Booking::orderBy('id', 'desc')->first();
        $this->assertNotNull($booking, 'Booking was not created');

        $this->assertMatchesRegularExpression('/^CD\d{6}$/', $booking->booking_code);
    }
}
