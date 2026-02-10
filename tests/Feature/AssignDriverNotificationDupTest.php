<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\DriverNotification;

class AssignDriverNotificationDupTest extends TestCase
{
    use RefreshDatabase;

    public function test_assignment_creates_single_driver_notification()
    {
        $status = BookingStatus::firstOrCreate(['name' => 'confirmed'], ['description' => 'Confirmed']);

        $booking = Booking::create([
            'booking_code' => 'CD000003',
            'passenger_name' => 'NotifyTest',
            'phone' => '012345',
            'pickup_date' => now()->toDateString(),
            'pickup_time' => now()->format('H:i'),
            'status_id' => $status->id,
            'total_price' => 30,
        ]);

        $driver = Driver::create([
            'name' => 'Notify Driver',
            'phone' => '777',
            'status' => 'active'
        ]);

        // Ensure no notifications exist before
        $this->assertEquals(0, DriverNotification::where('driver_id', $driver->id)->count());

        // Submit assignment form (non-AJAX)
        $resp = $this->post(route('admin.bookings.update', $booking), [
            'driver_id' => $driver->id,
        ]);

        // After assignment, exactly one notification should be present
        $this->assertEquals(1, DriverNotification::where('driver_id', $driver->id)->count());
        $note = DriverNotification::where('driver_id', $driver->id)->first();
        $this->assertStringContainsString('assigned', strtolower($note->title));
    }
}
