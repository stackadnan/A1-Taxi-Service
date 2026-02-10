<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingStatus;

class AssignDriverExpiringDocsAllowedTest extends TestCase
{
    use RefreshDatabase;

    public function test_ajax_reports_expiring_but_allows_assignment_on_form_submit()
    {
        // Create a confirmed status
        $status = BookingStatus::firstOrCreate(['name' => 'confirmed'], ['description' => 'Confirmed']);

        // Create a booking
        $booking = Booking::create([
            'booking_code' => 'CD000002',
            'passenger_name' => 'Test2',
            'phone' => '012345',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 20,
        ]);

        // Create a driver with a document expiring within 15 days and active status
        $driver = Driver::create([
            'name' => 'Expiring Driver',
            'phone' => '888',
            'status' => 'active',
            'driving_license_expiry' => now()->addDays(10)->toDateString(),
        ]);

        // AJAX request should report conflict but has_expired should be false
        $ajaxResp = $this->json('POST', route('admin.bookings.update', $booking), [
            'driver_id' => $driver->id,
        ]);

        $ajaxResp->assertStatus(200);
        $ajaxJson = $ajaxResp->json();
        $this->assertArrayHasKey('conflict', $ajaxJson);
        $this->assertTrue((bool)$ajaxJson['conflict']);
        $this->assertArrayHasKey('has_expired', $ajaxJson);
        $this->assertFalse((bool)$ajaxJson['has_expired']);

        // Non-AJAX form submit should allow assignment to proceed
        $formResp = $this->post(route('admin.bookings.update', $booking), [
            'driver_id' => $driver->id,
        ]);

        $this->assertTrue($booking->fresh()->driver_id === $driver->id, 'Booking should be assigned to the driver when only expiring docs are present');
    }
}
