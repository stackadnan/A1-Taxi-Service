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
            $booking->booking_code ?? $booking->id,
            $booking->pickup_address,
            $booking->dropoff_address
        );

        // Log intention to create admin notifications
        \Log::info('SendAdminNotificationOnDriverResponse: creating notifications', [
            'booking_id' => $booking->id,
            'driver_id' => $driver->id,
            'response' => $response,
            'title' => $title
        ]);

        // Create notifications and log results
        UserNotification::createForAdmins($title, $message);
        \Log::info('SendAdminNotificationOnDriverResponse: createForAdmins called', ['title' => $title]);
        try {
            $count = \App\Models\UserNotification::where('title', $title)->where('message', $message)->where('is_read', false)->count();
            \Log::info('SendAdminNotificationOnDriverResponse: unread notifications matching', ['count' => $count]);
        } catch (\Exception $e) {
            \Log::warning('SendAdminNotificationOnDriverResponse: failed to count notifications', ['error' => $e->getMessage()]);
        }

        // Clear any cached counts
        \Cache::forget('booking_section_counts');
    }
}