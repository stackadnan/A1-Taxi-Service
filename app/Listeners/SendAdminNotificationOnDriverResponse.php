<?php

namespace App\Listeners;

use App\Events\DriverResponseUpdated;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotificationOnDriverResponse
{
    /**
     * Handle the event.
     */
    public function handle(DriverResponseUpdated $event): void
    {
        $booking = $event->booking;
        $driver = $event->driver;
        $response = $event->response;

        $title = $response === 'accepted' 
            ? 'Driver Accepted Job' 
            : 'Driver Rejected Job';

        $message = sprintf(
            'Driver %s has %s job #%s from %s to %s',
            $driver->name,
            $response === 'accepted' ? 'accepted' : 'rejected',
            $booking->booking_code,
            $booking->pickup_address,
            $booking->dropoff_address
        );

        UserNotification::createForAdmins($title, $message);
    }
}