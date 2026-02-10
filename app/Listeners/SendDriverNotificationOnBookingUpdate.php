<?php

namespace App\Listeners;

use App\Events\BookingUpdated;
use App\Models\DriverNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendDriverNotificationOnBookingUpdate
{
    /**
     * Handle the event - Send notification to driver when booking is updated
     */
    public function handle(BookingUpdated $event): void
    {
        $booking = $event->booking;
        $changes = $event->changes;

        // Only notify if booking has an assigned driver
        if (!$booking->driver_id) {
            return;
        }

        // If driver assignment changed, skip generic update notifications (assignment is handled separately)
        if (isset($changes['driver_changed']) && $changes['driver_changed']) {
            \Log::info('SendDriverNotificationOnBookingUpdate: skipping notifications due to driver_changed flag', ['driver_id' => $booking->driver_id, 'booking_id' => $booking->id]);
            return;
        }

        // Handle completed status change specifically
        if (isset($changes['action']) && $changes['action'] === 'completed') {
            $title = 'Job Status Updated';
            $message = sprintf(
                'Booking #%s has been marked as completed. Great job!',
                $booking->booking_code ?? $booking->id
            );

            // Create notification
            DriverNotification::create([
                'driver_id' => $booking->driver_id,
                'title' => $title,
                'message' => $message
            ]);
            
            \Log::info('SendDriverNotificationOnBookingUpdate: created completed notification', [
                'driver_id' => $booking->driver_id, 
                'booking_id' => $booking->id
            ]);
            return;
        }

        // Handle POB status change specifically (backward compatibility)
        if (isset($changes['action']) && $changes['action'] === 'pob') {
            $title = 'Job Status Updated';
            $message = sprintf(
                'Booking #%s has been marked as POB (Proof of Business). Status updated successfully.',
                $booking->booking_code ?? $booking->id
            );

            // Create notification
            DriverNotification::create([
                'driver_id' => $booking->driver_id,
                'title' => $title,
                'message' => $message
            ]);
            
            \Log::info('SendDriverNotificationOnBookingUpdate: created POB notification', [
                'driver_id' => $booking->driver_id, 
                'booking_id' => $booking->id
            ]);
            return;
        }

        // Check if important fields changed
        $importantFields = ['pickup_date', 'pickup_time', 'pickup_address', 'dropoff_address', 'status_id'];
        $hasImportantChanges = false;
        
        foreach ($importantFields as $field) {
            if (isset($changes[$field])) {
                $hasImportantChanges = true;
                break;
            }
        }

        if (!$hasImportantChanges) {
            return; // No need to notify for minor changes
        }

        $title = 'Booking Updated';
        $message = sprintf(
            'Booking #%s has been updated. Please check the details.',
            $booking->booking_code ?? $booking->id
        );

        // Prevent duplicate notifications within a short window
        $recentWindow = 30; // seconds
        $exists = DriverNotification::where('driver_id', $booking->driver_id)
            ->where('title', $title)
            ->where('message', $message)
            ->where('created_at', '>=', now()->subSeconds($recentWindow))
            ->exists();

        if (! $exists) {
            DriverNotification::create([
                'driver_id' => $booking->driver_id,
                'title' => $title,
                'message' => $message
            ]);
            \Log::info('SendDriverNotificationOnBookingUpdate: created driver notification', ['driver_id' => $booking->driver_id, 'booking_id' => $booking->id]);
        } else {
            \Log::info('SendDriverNotificationOnBookingUpdate: skipped duplicate driver notification', ['driver_id' => $booking->driver_id, 'booking_id' => $booking->id]);
        }
    }
}
