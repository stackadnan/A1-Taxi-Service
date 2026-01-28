<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;
use App\Models\UserNotification;
use App\Events\DriverResponseUpdated;

class DriverDashboardController extends Controller
{
    /**
     * Show the driver dashboard
     */
    public function index()
    {
        $driver = Auth::guard('driver')->user();
        
        // Get job counts
        $newJobsCount = $driver->getNewJobsCount();
        $acceptedJobsCount = $driver->getAcceptedJobsCount();
        $completedJobsCount = $driver->getCompletedJobsCount();
        $declinedJobsCount = $driver->getDeclinedJobsCount();

        return view('driver.dashboard', compact(
            'driver',
            'newJobsCount',
            'acceptedJobsCount',
            'completedJobsCount',
            'declinedJobsCount'
        ));
    }

    /**
     * Get new jobs for the driver
     */
    public function newJobs()
    {
        $driver = Auth::guard('driver')->user();
        
        $jobs = $driver->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'confirmed');
            })
            ->whereNull('meta->driver_response')
            ->with(['status'])
            ->latest()
            ->paginate(10);

        return view('driver.jobs.new', compact('jobs', 'driver'));
    }

    /**
     * Get accepted jobs for the driver
     */
    public function acceptedJobs()
    {
        $driver = Auth::guard('driver')->user();
        
        $jobs = $driver->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'confirmed');
            })
            ->where('meta->driver_response', 'accepted')
            ->with(['status'])
            ->latest()
            ->paginate(10);

        return view('driver.jobs.accepted', compact('jobs', 'driver'));
    }

    /**
     * Get completed jobs for the driver
     */
    public function completedJobs()
    {
        $driver = Auth::guard('driver')->user();
        
        $jobs = $driver->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'completed');
            })
            ->with(['status'])
            ->latest()
            ->paginate(10);

        return view('driver.jobs.completed', compact('jobs', 'driver'));
    }

    /**
     * Get declined jobs for the driver
     */
    public function declinedJobs()
    {
        $driver = Auth::guard('driver')->user();
        
        $jobs = $driver->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'confirmed');
            })
            ->where('meta->driver_response', 'declined')
            ->with(['status'])
            ->latest()
            ->paginate(10);

        return view('driver.jobs.declined', compact('jobs', 'driver'));
    }

    /**
     * Return unread driver notifications and mark them as delivered (is_read=true)
     */
    public function unreadNotifications()
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $notes = \App\Models\DriverNotification::where('driver_id', $driver->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        $count = $notes->count();

        // mark as read so they are not delivered repeatedly
        try {
            if ($count) {
                \App\Models\DriverNotification::where('driver_id', $driver->id)->where('is_read', false)->update(['is_read' => true]);
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to mark driver notifications as read: ' . $e->getMessage());
        }

        return response()->json(['count' => $count, 'notifications' => $notes]);
    }

    /**
     * Accept a job
     */
    public function acceptJob(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            \Log::info('Driver accepting job', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
                'booking_driver_id' => $booking->driver_id
            ]);
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }
            
            if ($booking->driver_id !== $driver->id) {
                \Log::warning('Unauthorized job accept attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id
                ]);
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }
    
            $meta = $booking->meta ?? [];
            $meta['driver_response'] = 'accepted';
            $meta['driver_response_at'] = now()->toDateTimeString();
            // also mark a status change/activity timestamp so admin ordering brings this to the top
            $meta['status_changed_at'] = now()->toDateTimeString();
            $booking->meta = $meta;
            $booking->save();
            
            // Fire event for notifications
            event(new DriverResponseUpdated($booking, $driver, 'accepted'));
            
            \Log::info('Job accepted successfully', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id
            ]);
    
            return response()->json(['success' => true, 'message' => 'Job accepted successfully']);
        } catch (\Exception $e) {
            \Log::error('Error accepting job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to accept job: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Decline a job
     */
    public function declineJob(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            \Log::info('Driver declining job', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
                'booking_driver_id' => $booking->driver_id
            ]);
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }
            
            if ($booking->driver_id !== $driver->id) {
                \Log::warning('Unauthorized job decline attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id
                ]);
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }
    
            $meta = $booking->meta ?? [];
            $meta['driver_response'] = 'declined';
            $meta['driver_response_at'] = now()->toDateTimeString();
            // also mark a status change/activity timestamp so admin ordering brings this to the top
            $meta['status_changed_at'] = now()->toDateTimeString();
            $booking->meta = $meta;
            $booking->save();
            
            // Fire event for notifications
            event(new DriverResponseUpdated($booking, $driver, 'declined'));
            
            \Log::info('Job declined successfully', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id
            ]);
    
            return response()->json(['success' => true, 'message' => 'Job declined']);
        } catch (\Exception $e) {
            \Log::error('Error declining job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to decline job: ' . $e->getMessage()], 500);
        }
    }
}