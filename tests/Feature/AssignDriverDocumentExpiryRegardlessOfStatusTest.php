<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingStatus;

class AssignDriverDocumentExpiryRegardlessOfStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_assigning_driver_with_expired_documents_is_blocked_even_if_status_active()
    {
        // Create a confirmed status
        $status = BookingStatus::firstOrCreate(['name' => 'confirmed'], ['description' => 'Confirmed']);

        // Create a booking
        $booking = Booking::create([
            'booking_code' => 'CD000002',
            'passenger_name' => 'Test2',
            'phone' => '000000',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 20,
        ]);

        // Create a driver with expired driving license but status active
        $driver = Driver::create([
            'name' => 'Expired Active',
            'phone' => '111',
            'status' => 'active',
            'driving_license_expiry' => now()->subDays(2)->toDateString(),
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
