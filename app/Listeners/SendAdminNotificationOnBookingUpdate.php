<?php

namespace App\Listeners;

use App\Events\BookingUpdated;
use App\Models\UserNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAdminNotificationOnBookingUpdate
{
    /**
     * Handle the event - Send notification to admin when booking status is updated
     */
    public function handle(BookingUpdated $event): void
    {
        $booking = $event->booking;
        $changes = $event->changes;
        $updatedBy = $event->updatedBy;

        // Handle completed status change specifically
        if (isset($changes['action']) && $changes['action'] === 'completed') {
            $driverName = $updatedBy ? $updatedBy->name : 'Driver';
            
            $title = 'Job Completed';
            $message = sprintf(
                '%s has marked job #%s as completed. Status changed from %s to completed.',
                $driverName,
                $booking->booking_code ?? $booking->id,
                $changes['previous_status'] ?? 'confirmed'
            );

            // Log intention to create admin notifications
            \Log::info('SendAdminNotificationOnBookingUpdate: creating completed notification', [
                'booking_id' => $booking->id,
                'updated_by_type' => get_class($updatedBy),
                'updated_by_id' => $updatedBy ? $updatedBy->id : null,
                'title' => $title
            ]);

            // Create notifications for all admins
            UserNotification::createForAdmins($title, $message);
            
            \Log::info('SendAdminNotificationOnBookingUpdate: completed notification created for admins', [
                'booking_id' => $booking->id,
                'title' => $title
            ]);

            // Clear any cached counts
            \Cache::forget('booking_section_counts');
            return;
        }

        // Handle POB status change specifically (backward compatibility)
        if (isset($changes['action']) && $changes['action'] === 'pob') {
            $driverName = $updatedBy ? $updatedBy->name : 'Driver';
            
            $title = 'Job Marked as POB';
            $message = sprintf(
                '%s has marked job #%s as POB. Status changed from %s to POB.',
                $driverName,
                $booking->booking_code ?? $booking->id,
                $changes['previous_status'] ?? 'confirmed'
            );

            // Log intention to create admin notifications
            \Log::info('SendAdminNotificationOnBookingUpdate: creating POB notification', [
                'booking_id' => $booking->id,
                'updated_by_type' => get_class($updatedBy),
                'updated_by_id' => $updatedBy ? $updatedBy->id : null,
                'title' => $title
            ]);

            // Create notifications for all admins
            UserNotification::createForAdmins($title, $message);
            
            \Log::info('SendAdminNotificationOnBookingUpdate: POB notification created for admins', [
                'booking_id' => $booking->id,
                'title' => $title
            ]);

            // Clear any cached counts
            \Cache::forget('booking_section_counts');
            return;
        }

        // Handle other status changes if needed
        if (isset($changes['status_id'])) {
            // Only notify for significant status changes
            $statusName = optional($booking->status)->name;
            if (in_array($statusName, ['completed', 'cancelled', 'in_progress'])) {
                $title = 'Booking Status Updated';
                $message = sprintf(
                    'Booking #%s status has been updated to %s.',
                    $booking->booking_code ?? $booking->id,
                    ucfirst(str_replace('_', ' ', $statusName))
                );

                UserNotification::createForAdmins($title, $message);
                
                \Log::info('SendAdminNotificationOnBookingUpdate: status notification created', [
                    'booking_id' => $booking->id,
                    'new_status' => $statusName
                ]);

                // Clear any cached counts
                \Cache::forget('booking_section_counts');
            }
        }
    }
}