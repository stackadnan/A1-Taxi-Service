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

        // Count expired and expiring documents for this driver (15-day window)
        $documentIssues = []; // will contain both 'expired' and 'expiring' items
        $expiredDocs = [];
        $expiringDocs = [];
        $docs = [
            'driving_license_expiry' => 'Driving License',
            'private_hire_drivers_license_expiry' => 'Private Hire Drivers License',
            'private_hire_vehicle_insurance_expiry' => 'Private Hire Vehicle Insurance',
            'private_hire_vehicle_license_expiry' => 'Private Hire Vehicle License',
            'private_hire_vehicle_mot_expiry' => 'Private Hire Vehicle MOT',
        ];

        $today = \Carbon\Carbon::today();
        $cutoff = $today->copy()->addDays(15);

        foreach ($docs as $field => $label) {
            if ($driver->{$field}) {
                try {
                    $expiry = \Carbon\Carbon::parse($driver->{$field});
                    if ($expiry->lt($today)) {
                        $expiredDocs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                        $documentIssues[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                    } elseif ($expiry->between($today, $cutoff)) {
                        $expiringDocs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                        $documentIssues[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                    }
                } catch (\Exception $e) { /* ignore parse errors */ }
            }
        }

        // Show combined count on the dashboard (expired + expiring)
        $expiredDocsCount = count($documentIssues);
        $expiringDocsCount = count($expiringDocs);

        return view('driver.dashboard', compact(
            'driver',
            'newJobsCount',
            'acceptedJobsCount',
            'completedJobsCount',
            'declinedJobsCount',
            'expiredDocsCount'
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
     * Get accepted jobs for the driver (includes POB status)
     */
    public function acceptedJobs()
    {
        $driver = Auth::guard('driver')->user();
        
        $jobs = $driver->bookings()
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['confirmed','pob']);
            })
            ->where(function($q) {
                $q->where('meta->driver_response', 'accepted')
                  ->orWhereHas('status', function($sq) { $sq->where('name', 'pob'); });
            })
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
        if ((int) $booking->driver_id !== (int) $driver->id) {
            abort(403);
        }

        // Allow viewing only if booking is accepted (meta driver_response = accepted and confirmed), POB status, or completed
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
     * Show driver's expired documents (self-service view)
     */
    public function expiredDocuments()
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) abort(403);

        $documentIssues = [];
        $expiredDocs = [];
        $expiringDocs = [];
        $docs = [
            'driving_license_expiry' => 'Driving License',
            'private_hire_drivers_license_expiry' => 'Private Hire Drivers License',
            'private_hire_vehicle_insurance_expiry' => 'Private Hire Vehicle Insurance',
            'private_hire_vehicle_license_expiry' => 'Private Hire Vehicle License',
            'private_hire_vehicle_mot_expiry' => 'Private Hire Vehicle MOT',
        ];

        $today = \Carbon\Carbon::today();
        $cutoff = $today->copy()->addDays(15);

        foreach ($docs as $field => $label) {
            if ($driver->{$field}) {
                try {
                    $expiry = \Carbon\Carbon::parse($driver->{$field});
                    if ($expiry->lt($today)) {
                        $expiredDocs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                        $documentIssues[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                    } elseif ($expiry->between($today, $cutoff)) {
                        $expiringDocs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                        $documentIssues[] = ['field' => $field, 'label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                    }
                } catch (\Exception $e) { }
            }
        }

        return view('driver.documents.expired', compact('driver', 'documentIssues', 'expiredDocs', 'expiringDocs'));
    }

    /**
     * Show profile edit form for driver (self-service)
     */
    public function editProfile()
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) abort(403);
        return view('driver.profile.edit', compact('driver'));
    }

    /**
     * Update driver profile (self-service)
     */
    public function updateProfile(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) return redirect()->route('driver.dashboard')->with('error', 'Not authenticated');

        $data = $request->validate([
            'driving_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driving_license_expiry' => 'nullable|date',
            'private_hire_drivers_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_drivers_license_expiry' => 'nullable|date',
            'private_hire_vehicle_insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_insurance_expiry' => 'nullable|date',
            'private_hire_vehicle_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_license_expiry' => 'nullable|date',
            'private_hire_vehicle_mot' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_mot_expiry' => 'nullable|date',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255'
        ]);

        try {
            // files
            $docs = ['driving_license', 'private_hire_drivers_license', 'private_hire_vehicle_insurance', 'private_hire_vehicle_license', 'private_hire_vehicle_mot'];
            foreach ($docs as $doc) {
                if ($request->hasFile($doc)) {
                    if ($driver->$doc && \Storage::disk('public')->exists($driver->$doc)) {
                        \Storage::disk('public')->delete($driver->$doc);
                    }
                    $driver->$doc = $request->file($doc)->store('drivers/documents', 'public');
                }
            }

            // expiries and basic fields
            $fields = ['driving_license_expiry','private_hire_drivers_license_expiry','private_hire_vehicle_insurance_expiry','private_hire_vehicle_license_expiry','private_hire_vehicle_mot_expiry','name','phone','email'];
            foreach ($fields as $f) { if (array_key_exists($f, $data)) $driver->$f = $data[$f]; }

            $driver->save();

            return redirect()->route('driver.documents.expired')->with('success', 'Profile updated');
        } catch (\Exception $e) {
            logger()->error('Failed to update driver profile: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update profile');
        }
    }

    /**
     * Update driver availability/status
     */
    public function updateAvailability(Request $request)
    {
        $driver = Auth::guard('driver')->user();
        if (! $driver) return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);

        $data = $request->validate([
            'status' => 'required|in:active,inactive',
            'unavailable_from' => 'required_if:status,inactive|nullable|date',
            'unavailable_to' => 'required_if:status,inactive|nullable|date|after:unavailable_from',
        ]);

        try {
            $driver->status = $data['status'];
            if ($data['status'] === 'inactive') {
                $driver->unavailable_from = $data['unavailable_from'];
                $driver->unavailable_to = $data['unavailable_to'];
            } else {
                $driver->unavailable_from = null;
                $driver->unavailable_to = null;
            }
            $driver->save();

            return response()->json(['success' => true, 'message' => 'Availability updated', 'driver' => [ 'status' => $driver->status, 'unavailable_from' => $driver->unavailable_from, 'unavailable_to' => $driver->unavailable_to ]]);
        } catch (\Exception $e) {
            \Log::error('Failed to update driver availability', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Failed to update availability'], 500);
        }
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
            
            if ((int) $booking->driver_id !== (int) $driver->id) {
                // Detailed logging to help debug production 403s (temporary)
                \Log::warning('Unauthorized job accept attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id,
                    'session_id' => session()->getId(),
                    'ip' => $request->ip(),
                    'headers' => [
                        'referer' => $request->headers->get('referer'),
                        'user_agent' => $request->headers->get('user-agent'),
                        'cookie' => $request->headers->get('cookie'),
                        'x_csrf_token' => $request->header('X-CSRF-TOKEN')
                    ]
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
            
            if ((int) $booking->driver_id !== (int) $driver->id) {
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

    /**
     * Mark a job as In Route (driver is traveling to pickup location)
     */
    public function markAsInRoute(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            \Log::info('Driver marking job as In Route', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
                'booking_driver_id' => $booking->driver_id
            ]);
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }
            
            if ((int) $booking->driver_id !== (int) $driver->id) {
                \Log::warning('Unauthorized In Route attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id
                ]);
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }

            // Check if job is currently confirmed and accepted
            $statusName = optional($booking->status)->name;
            $driverResponse = $booking->meta['driver_response'] ?? null;
            
            if ($statusName !== 'confirmed' || $driverResponse !== 'accepted') {
                return response()->json(['error' => 'Job must be confirmed and accepted before marking as In Route'], 400);
            }

            // Validate booking is scheduled for today
            if ($booking->scheduled_at) {
                $scheduledDate = \Carbon\Carbon::parse($booking->scheduled_at);
                if (!$scheduledDate->isToday()) {
                    $formattedDate = $scheduledDate->format('D, M j, Y \a\t g:i A');
                    return response()->json([
                        'error' => 'Cannot mark as In Route yet. Pickup is scheduled for ' . $formattedDate
                    ], 400);
                }
            }

            // Check if already in route
            $meta = $booking->meta ?? [];
            if (isset($meta['in_route']) && $meta['in_route'] === true) {
                return response()->json(['error' => 'Job is already marked as In Route'], 400);
            }

            // Update booking meta to mark as in_route
            $meta['in_route'] = true;
            $meta['in_route_at'] = now()->toDateTimeString();
            $meta['in_route_by_driver_id'] = $driver->id;
            $meta['status_changed_at'] = now()->toDateTimeString();
            
            $booking->meta = $meta;
            $booking->save();
            
            // Fire event for notifications and SSE
            event(new \App\Events\DriverResponseUpdated($booking, $driver, 'in_route'));

            // Also create an explicit admin notification as a fallback to ensure admins see the In Route update immediately
            try {
                $title = 'Driver In Route';
                $message = sprintf('Driver %s is In Route for job #%s (to pickup at %s)', $driver->name, $booking->booking_code ?? $booking->id, $booking->pickup_address);
                \App\Models\UserNotification::createForAdmins($title, $message);
                \Log::info('DriverDashboardController: created admin notification for In Route', ['driver_id' => $driver->id, 'booking_id' => $booking->id]);
            } catch (\Exception $e) {
                \Log::warning('Failed to create admin notification for In Route: ' . $e->getMessage());
            }
            
            \Log::info('Job marked as In Route successfully', [
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
                'message' => 'Marked as In Route. Admin can now track your journey to pickup.',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking job as In Route', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to mark job as In Route: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark a job as Arrived at Pickup (driver has arrived at the pickup location)
     */
    public function markAsArrivedAtPickup(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();

            \Log::info('Driver marking job as Arrived at Pickup', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
            ]);

            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }

            if ((int) $booking->driver_id !== (int) $driver->id) {
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }

            // Must be confirmed & accepted and already In Route
            $statusName    = optional($booking->status)->name;
            $driverResponse = $booking->meta['driver_response'] ?? null;

            if ($statusName !== 'confirmed' || $driverResponse !== 'accepted') {
                return response()->json(['error' => 'Job must be confirmed and accepted before marking as Arrived'], 400);
            }

            $meta = $booking->meta ?? [];

            if (empty($meta['in_route']) || $meta['in_route'] !== true) {
                return response()->json(['error' => 'Job must be In Route before marking as Arrived at Pickup'], 400);
            }

            if (!empty($meta['arrived_at_pickup'])) {
                return response()->json(['error' => 'Job is already marked as Arrived at Pickup'], 400);
            }

            // Update meta
            $meta['arrived_at_pickup']              = true;
            $meta['arrived_at_pickup_at']           = now()->toDateTimeString();
            $meta['arrived_at_pickup_by_driver_id'] = $driver->id;
            $meta['status_changed_at']              = now()->toDateTimeString();

            $booking->meta = $meta;
            $booking->save();

            // Fire event for SSE / notifications
            event(new \App\Events\DriverResponseUpdated($booking, $driver, 'arrived_at_pickup'));

            // Create explicit admin notification
            try {
                $title   = 'Driver Arrived at Pickup';
                $message = sprintf(
                    'Driver %s has arrived at the pickup location for job #%s (%s)',
                    $driver->name,
                    $booking->booking_code ?? $booking->id,
                    $booking->pickup_address
                );
                \App\Models\UserNotification::createForAdmins($title, $message);
            } catch (\Exception $e) {
                \Log::warning('Failed to create admin notification for Arrived at Pickup: ' . $e->getMessage());
            }

            \Log::info('Job marked as Arrived at Pickup successfully', [
                'driver_id'  => $driver->id,
                'booking_id' => $booking->id,
            ]);

            $counts = [
                'new'       => $driver->getNewJobsCount(),
                'accepted'  => $driver->getAcceptedJobsCount(),
                'completed' => $driver->getCompletedJobsCount(),
                'declined'  => $driver->getDeclinedJobsCount(),
            ];

            return response()->json([
                'success' => true,
                'message' => 'Marked as Arrived at Pickup. Admin has been notified.',
                'counts'  => $counts,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking job as Arrived at Pickup', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['error' => 'Failed to mark as Arrived at Pickup: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark a job as Proof of Business (POB)
     */

    public function markAsProofOfBusiness(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            \Log::info('Driver marking job as POB', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
                'booking_driver_id' => $booking->driver_id
            ]);
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }
            
            if ((int) $booking->driver_id !== (int) $driver->id) {
                \Log::warning('Unauthorized POB attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id
                ]);
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }

            // Check if job is currently confirmed and accepted
            $statusName = optional($booking->status)->name;
            $driverResponse = $booking->meta['driver_response'] ?? null;
            
            if ($statusName !== 'confirmed' || $driverResponse !== 'accepted') {
                return response()->json(['error' => 'Job must be confirmed and accepted before marking as POB'], 400);
            }

            // Get POB status
            $pobStatus = \App\Models\BookingStatus::where('name', 'pob')->first();
            if (!$pobStatus) {
                return response()->json(['error' => 'POB status not found'], 500);
            }

            // Update booking status and meta
            $meta = $booking->meta ?? [];
            $meta['pob_marked_at'] = now()->toDateTimeString();
            $meta['pob_marked_by_driver_id'] = $driver->id;
            $meta['status_changed_at'] = now()->toDateTimeString();
            
            // Clear in_route flag since driver has now picked up passenger
            if (isset($meta['in_route'])) {
                $meta['in_route'] = false;
                $meta['in_route_completed_at'] = now()->toDateTimeString();
            }

            $booking->status_id = $pobStatus->id;
            $booking->meta = $meta;
            $booking->save();
            
            // Fire event for notifications and SSE
            event(new \App\Events\BookingUpdated($booking, $driver, ['status_id' => $pobStatus->id, 'previous_status' => $statusName, 'action' => 'pob']));
            
            \Log::info('Job marked as POB successfully', [
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
                'message' => 'Job marked as POB successfully. You can now complete it.',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking job as POB', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to mark job as POB: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mark a job as completed
     */
    public function markAsCompleted(Request $request, Booking $booking)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            \Log::info('Driver marking job as completed', [
                'driver_id' => $driver->id,
                'booking_id' => $booking->id,
                'booking_driver_id' => $booking->driver_id
            ]);
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }
            
            if ((int) $booking->driver_id !== (int) $driver->id) {
                \Log::warning('Unauthorized completion attempt', [
                    'driver_id' => $driver->id,
                    'booking_id' => $booking->id,
                    'booking_driver_id' => $booking->driver_id
                ]);
                return response()->json(['error' => 'Unauthorized - This job is not assigned to you'], 403);
            }

            // Check if job is currently in POB status
            $statusName = optional($booking->status)->name;
            
            if ($statusName !== 'pob') {
                return response()->json(['error' => 'Job must be in POB status before marking as completed'], 400);
            }

            // Get completed status
            $completedStatus = \App\Models\BookingStatus::where('name', 'completed')->first();
            if (!$completedStatus) {
                return response()->json(['error' => 'Completed status not found'], 500);
            }

            // Update booking status and meta
            $meta = $booking->meta ?? [];
            $meta['completed_at'] = now()->toDateTimeString();
            $meta['completed_by_driver_id'] = $driver->id;
            $meta['status_changed_at'] = now()->toDateTimeString();
            
            $booking->status_id = $completedStatus->id;
            $booking->meta = $meta;
            $booking->save();
            
            // Fire event for notifications and SSE
            event(new \App\Events\BookingUpdated($booking, $driver, ['status_id' => $completedStatus->id, 'previous_status' => $statusName, 'action' => 'completed']));
            
            \Log::info('Job marked as completed successfully', [
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
                'message' => 'Job completed successfully',
                'counts' => $counts
            ]);
        } catch (\Exception $e) {
            \Log::error('Error marking job as completed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to complete job: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Update driver's current location
     */
    public function updateLocation(Request $request)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }

            $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'accuracy' => 'nullable|numeric',
                'heading' => 'nullable|numeric|between:0,360',
                'speed' => 'nullable|numeric|min:0'
            ]);

            // Update or create driver location in driver_locations table
            \App\Models\DriverLocation::updateOrCreate(
                ['driver_id' => $driver->id],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'accuracy' => $request->accuracy ?? 0
                ]
            );

            // Also update driver's last active time
            $driver->last_active_at = now();
            $driver->save();

            \Log::info('Driver location updated', [
                'driver_id' => $driver->id,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully',
                'timestamp' => now()->toDateTimeString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating driver location', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Failed to update location'], 500);
        }
    }

    /**
     * Get driver's location sharing status
     */
    public function getLocationStatus(Request $request)
    {
        try {
            $driver = Auth::guard('driver')->user();
            
            if (!$driver) {
                return response()->json(['error' => 'Driver not authenticated'], 401);
            }

            $meta = $driver->meta ?? [];
            $currentLocation = $meta['current_location'] ?? null;
            $lastUpdate = $meta['location_updated_at'] ?? null;
            
            // Check if driver has active POB jobs
            $activePobJob = $driver->bookings()
                ->whereHas('status', function($q) {
                    $q->where('name', 'pob');
                })
                ->first();

            return response()->json([
                'success' => true,
                'location_sharing_enabled' => !is_null($currentLocation),
                'last_update' => $lastUpdate,
                'has_active_pob_job' => !is_null($activePobJob),
                'active_job' => $activePobJob ? [
                    'id' => $activePobJob->id,
                    'booking_code' => $activePobJob->booking_code,
                    'to_address' => $activePobJob->to_address
                ] : null
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting location status', [
                'error' => $e->getMessage()
            ]);
            return response()->json(['error' => 'Failed to get location status'], 500);
        }
    }
}