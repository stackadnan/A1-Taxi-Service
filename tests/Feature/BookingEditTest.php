<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Booking;
use App\Models\BookingStatus;

class BookingEditTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PermissionsSeeder::class);
        $this->seed(\Database\Seeders\AdminRoleSeeder::class);
    }

    public function test_admin_can_view_booking()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $status = BookingStatus::firstOrCreate(['name' => 'new']);
        $b = Booking::create([
            'booking_code' => 'CD123456', 'user_id' => $admin->id, 'passenger_name' => 'Alice', 'phone' => '0712345678', 'status_id' => $status->id
        ]);

        $response = $this->actingAs($admin)->get(route('admin.bookings.show', $b));
        $response->assertStatus(200);
        $response->assertSee('Alice');
    }

    public function test_admin_can_update_booking()
    {
        $admin = User::factory()->create();
        $role = Role::where('name', 'Super Admin')->first();
        $admin->roles()->sync([$role->id]);

        $status = BookingStatus::firstOrCreate(['name' => 'new']);
        $b = Booking::create([
            'booking_code' => 'CD222222', 'user_id' => $admin->id, 'passenger_name' => 'Bob', 'phone' => '0799999999', 'status_id' => $status->id
        ]);

        $payload = [
            'passenger_name' => 'Robert',
            'phone' => '0790000000',
            'pickup_date' => now()->format('Y-m-d'),
            'pickup_time' => now()->format('H:i'),
        ];

        $response = $this->actingAs($admin)->put(route('admin.bookings.update', $b), $payload);
        $response->assertRedirect(route('admin.bookings.show', $b));

        $b->refresh();
        $this->assertEquals('Robert', $b->passenger_name);
        $this->assertEquals('0790000000', $b->phone);
    }
}
