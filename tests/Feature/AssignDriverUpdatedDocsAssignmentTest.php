<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingStatus;

class AssignDriverUpdatedDocsAssignmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_succeeds_if_admin_updates_expired_documents_before_submit()
    {
        $status = BookingStatus::firstOrCreate(['name' => 'confirmed'], ['description' => 'Confirmed']);

        $booking = Booking::create([
            'booking_code' => 'CD000004',
            'passenger_name' => 'UpdateDocsTest',
            'phone' => '012345',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 40,
        ]);

        $driver = Driver::create([
            'name' => 'Was Expired',
            'phone' => '666',
            'status' => 'inactive',
            'driving_license_expiry' => now()->subDays(5)->toDateString(),
        ]);

        // Admin updates driver's expiry to future (simulate update happening before booking submit)
        $driver->driving_license_expiry = now()->addDays(30)->toDateString();
        $driver->save();

        $resp = $this->post(route('admin.bookings.update', $booking), [
            'driver_id' => $driver->id,
        ]);

        $booking->refresh();
        $this->assertEquals($driver->id, $booking->driver_id, 'Booking should be assigned after driver documents updated even if status remains inactive');

        // Expect a session warning about documents updated but driver remains inactive
        $resp->assertSessionHas('warning');

        // Test AJAX form submission also includes a warning in the JSON response
        $booking2 = Booking::create([
            'booking_code' => 'CD000005',
            'passenger_name' => 'UpdateDocsTest2',
            'phone' => '012345',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 50,
        ]);

        $ajax = $this->json('POST', route('admin.bookings.update', $booking2), ['driver_id' => $driver->id]);
        $ajax->assertStatus(200);
        $ajaxJson = $ajax->json();
        $this->assertTrue($ajaxJson['success']);
        $this->assertArrayHasKey('warning', $ajaxJson);
    }
}
