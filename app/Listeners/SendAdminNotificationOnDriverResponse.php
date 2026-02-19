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

        // Determine title and message depending on response type
        if ($response === 'accepted') {
            $title = 'Driver Accepted Job';
            $message = sprintf('Driver %s has accepted job #%s from %s to %s', $driver->name, $booking->booking_code ?? $booking->id, $booking->pickup_address, $booking->dropoff_address);
        } elseif ($response === 'in_route') {
            $title = 'Driver In Route';
            $message = sprintf('Driver %s is In Route for job #%s (to pickup at %s)', $driver->name, $booking->booking_code ?? $booking->id, $booking->pickup_address);
        } elseif ($response === 'arrived_at_pickup') {
            $title = 'Driver Arrived at Pickup';
            $message = sprintf('Driver %s has arrived at the pickup location for job #%s (%s)', $driver->name, $booking->booking_code ?? $booking->id, $booking->pickup_address);
        } elseif ($response === 'declined' || $response === 'rejected') {
            $title = 'Driver Rejected Job';
            $message = sprintf('Driver %s has rejected job #%s from %s to %s', $driver->name, $booking->booking_code ?? $booking->id, $booking->pickup_address, $booking->dropoff_address);
        } else {
            $title = 'Driver Response';
            $message = sprintf('Driver %s updated response for job #%s', $driver->name, $booking->booking_code ?? $booking->id);
        }

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