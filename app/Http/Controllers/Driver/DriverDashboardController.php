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
     * Show a single booking to the driver (only if assigned and accepted or completed)
     */
    public function show(Booking $booking)
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) {
            abort(403);
        }

        // Ensure booking is assigned to this driver
        if ($booking->driver_id !== $driver->id) {
            abort(403);
        }

        // Allow viewing only if booking is accepted (meta driver_response = accepted and confirmed) or completed
        $statusName = optional($booking->status)->name;
        $driverResponse = $booking->meta['driver_response'] ?? null;

        $allowed = false;
        if ($statusName === 'completed') $allowed = true;
        if ($statusName === 'confirmed' && $driverResponse === 'accepted') $allowed = true;

        if (! $allowed) {
            abort(403);
        }

        return view('driver.jobs.show', ['job' => $booking, 'driver' => $driver]);
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
                \App\Models\DriverNotification::where('driver_id', $driver->id)->where('is_read', false)->update(['is_read' => true, 'read_at' => now()]);
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to mark driver notifications as read: ' . $e->getMessage());
        }

        return response()->json(['count' => $count, 'notifications' => $notes]);
    }

    /**
     * Return current job counts for the authenticated driver
     */
    public function counts()
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) {
            return response()->json(['success' => false, 'message' => 'Not authenticated', 'counts' => [ 'new' => 0, 'accepted' => 0, 'completed' => 0, 'declined' => 0 ]]);
        }

        $counts = [
            'new' => $driver->getNewJobsCount(),
            'accepted' => $driver->getAcceptedJobsCount(),
            'completed' => $driver->getCompletedJobsCount(),
            'declined' => $driver->getDeclinedJobsCount(),
        ];

        return response()->json(['success' => true, 'counts' => $counts]);
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
    
            // Return updated counts
            $counts = [
                'new' => $driver->getNewJobsCount(),
                'accepted' => $driver->getAcceptedJobsCount(),
                'completed' => $driver->getCompletedJobsCount(),
                'declined' => $driver->getDeclinedJobsCount(),
            ];
    
            return response()->json([
                'success' => true, 
                'message' => 'Job accepted successfully',
                'counts' => $counts
            ]);
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
    
            // Return updated counts
            $counts = [
                'new' => $driver->getNewJobsCount(),
                'accepted' => $driver->getAcceptedJobsCount(),
                'completed' => $driver->getCompletedJobsCount(),
                'declined' => $driver->getDeclinedJobsCount(),
            ];
    
            return response()->json([
                'success' => true, 
                'message' => 'Job declined',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error declining job', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to decline job: ' . $e->getMessage()], 500);
        }
    }
}