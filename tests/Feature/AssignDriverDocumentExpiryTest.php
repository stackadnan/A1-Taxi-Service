<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingStatus;

class AssignDriverDocumentExpiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_driver_with_expired_documents_is_blocked()
    {
        // Create a confirmed status
        $status = BookingStatus::firstOrCreate(['name' => 'confirmed'], ['description' => 'Confirmed']);

        // Create a booking
        $booking = Booking::create([
            'booking_code' => 'CD000001',
            'passenger_name' => 'Test',
            'phone' => '012345',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 10,
        ]);

        // Create a driver with expired driving license and inactive status
        $driver = Driver::create([
            'name' => 'Expired Driver',
            'phone' => '999',
            'status' => 'inactive',
            'driving_license_expiry' => now()->subDays(1)->toDateString(),
        ]);

        // Attempt to assign via AJAX
        $response = $this->json('POST', route('admin.bookings.update', $booking), [
            'driver_id' => $driver->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson(['success' => false, 'conflict' => true]);
        $json = $response->json();
        $this->assertArrayHasKey('documents', $json);
        $this->assertNotEmpty($json['documents']);
        $this->assertEquals('Driving License', $json['documents'][0]['label']);
    }
}
