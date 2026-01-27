<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

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
            $meta['driver_response_at'] = now()->toISOString();
            $booking->meta = $meta;
            $booking->save();
            
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
            $meta['driver_response_at'] = now()->toISOString();
            $booking->meta = $meta;
            $booking->save();
            
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