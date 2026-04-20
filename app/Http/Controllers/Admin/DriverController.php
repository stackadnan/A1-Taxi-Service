<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\AdminSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $tab = $request->get('tab', 'active');
        $timingSettings = AdminSetting::driverWarningThresholds();

        $query = Driver::query();

        // Apply search filter
        if ($q !== '') {
            $query->where(function($qq) use ($q){
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('coverage_area', 'like', "%{$q}%")
                   ->orWhere('badge_number', 'like', "%{$q}%")
                   ->orWhere('vehicle_plate', 'like', "%{$q}%");
            });
        }

        // Tab filters
        $today = \Carbon\Carbon::today()->toDateString();
        if ($tab === 'active') {
            // Active drivers must have status=active AND have no expired documents (all expiry fields null or >= today)
            $query->where('status', '=', 'active')
                  ->where(function($q) use ($today) { $q->whereNull('driving_license_expiry')->orWhere('driving_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_drivers_license_expiry')->orWhere('private_hire_drivers_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_insurance_expiry')->orWhere('private_hire_vehicle_insurance_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_license_expiry')->orWhere('private_hire_vehicle_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_mot_expiry')->orWhere('private_hire_vehicle_mot_expiry', '>=', $today); });
        } elseif ($tab === 'inactive') {
            // Inactive drivers include those explicitly inactive or those with any expired document (< today)
            $query->where(function($q) use ($today){
                $q->where('status', '=', 'inactive')
                  ->orWhere(function($q2) use ($today){
                      $q2->whereNotNull('driving_license_expiry')->where('driving_license_expiry', '<', \Carbon\Carbon::today()->toDateString())
                         ->orWhere(function($q3){ $q3->whereNotNull('private_hire_drivers_license_expiry')->where('private_hire_drivers_license_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q4){ $q4->whereNotNull('private_hire_vehicle_insurance_expiry')->where('private_hire_vehicle_insurance_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q5){ $q5->whereNotNull('private_hire_vehicle_license_expiry')->where('private_hire_vehicle_license_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q6){ $q6->whereNotNull('private_hire_vehicle_mot_expiry')->where('private_hire_vehicle_mot_expiry', '<', \Carbon\Carbon::today()->toDateString()); });
                  });
            });
        } elseif ($tab === 'documents') {
            $soon = \Carbon\Carbon::today()->addDays(15)->toDateString();
            $query->where(function($qdoc) use ($soon){
                $qdoc->whereNotNull('driving_license_expiry')->where('driving_license_expiry', '<=', $soon)
                     ->orWhere(function($q2) use ($soon){ $q2->whereNotNull('private_hire_drivers_license_expiry')->where('private_hire_drivers_license_expiry', '<=', $soon); })
                     ->orWhere(function($q3) use ($soon){ $q3->whereNotNull('private_hire_vehicle_insurance_expiry')->where('private_hire_vehicle_insurance_expiry', '<=', $soon); })
                     ->orWhere(function($q4) use ($soon){ $q4->whereNotNull('private_hire_vehicle_license_expiry')->where('private_hire_vehicle_license_expiry', '<=', $soon); })
                     ->orWhere(function($q5) use ($soon){ $q5->whereNotNull('private_hire_vehicle_mot_expiry')->where('private_hire_vehicle_mot_expiry', '<=', $soon); });
            });
        }

        $drivers = $query->orderBy('name')->paginate(20)->withQueryString();

        // Prepare additional data for documents tab
        if ($tab === 'documents') {
            $today = \Carbon\Carbon::today();
            foreach ($drivers as $drv) {
                $docs = [];
                $fields = [
                    'driving_license' => 'Driving License',
                    'private_hire_drivers_license' => 'Private Hire Drivers License',
                    'private_hire_vehicle_insurance' => 'Private Hire Vehicle Insurance',
                    'private_hire_vehicle_license' => 'Private Hire Vehicle License',
                    'private_hire_vehicle_mot' => 'Private Hire Vehicle MOT',
                ];
                foreach ($fields as $field => $label) {
                    $expiryField = $field . '_expiry';
                    $expiry = $drv->{$expiryField} ?? null;
                    if ($expiry) {
                        $expiryDate = \Carbon\Carbon::parse($expiry);
                        if ($expiryDate->lte($today->copy()->addDays(15))) {
                            $docs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiryDate, 'status' => $expiryDate->lt($today) ? 'expired' : 'expiring'];
                        }
                    }
                }
                // sort by expiry asc
                usort($docs, function($a,$b){ return $a['expiry']->timestamp <=> $b['expiry']->timestamp; });
                $drv->expiring_documents = $docs;
            }
        }

        // Prepare driver status data for the 'status' tab
        if ($tab === 'status') {
            // Get status ids that mean a booking is finished
            $finishedStatusIds = \App\Models\BookingStatus::whereIn('name', ['completed', 'cancelled'])->pluck('id')->toArray();

            foreach ($drivers as $drv) {
                // find most relevant active booking for this driver (not completed/cancelled)
                $currentBooking = \App\Models\Booking::where('driver_id', $drv->id)
                    ->whereNotIn('status_id', $finishedStatusIds)
                    ->orderBy('scheduled_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $drv->current_booking = $currentBooking;

                if ($currentBooking) {
                    $meta = $currentBooking->meta ?? [];
                    $isInRoute        = isset($meta['in_route']) && $meta['in_route'] === true;
                    $isArrivedPickup  = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;
                    $statusKey = $currentBooking->status->name ?? 'in_progress';
                    
                    // Priority: POB > arrived_at_pickup > in_route > other
                    if ($statusKey === 'pob') {
                        $label = 'POB';
                        $color = 'orange';
                        $sinceFrom = isset($meta['pob_marked_at']) ? $meta['pob_marked_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } elseif ($isArrivedPickup) {
                        $label = 'Arrived';
                        $color = 'blue';
                        $sinceFrom = isset($meta['arrived_at_pickup_at']) ? $meta['arrived_at_pickup_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } elseif ($isInRoute) {
                        $label = 'In Route';
                        $color = 'purple';
                        $sinceFrom = isset($meta['in_route_at']) ? $meta['in_route_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } else {
                        $labelMap = [
                            'in_progress' => ['On Route', 'green'],
                            'confirmed' => ['Accepted', 'yellow'],
                            'new' => ['New', 'gray'],
                        ];
                        $label = $labelMap[$statusKey][0] ?? ucfirst(str_replace('_', ' ', $statusKey));
                        $color = $labelMap[$statusKey][1] ?? 'gray';
                        $sinceFrom = $drv->last_assigned_at ?? $currentBooking->updated_at ?? $drv->last_active_at;
                    }
                } else {
                    $label = 'Idle';
                    $color = 'yellow';

                    // Show idle time since the driver's last completed booking if available,
                    // otherwise fall back to driver's last_active_at
                    $lastCompleted = \App\Models\Booking::where('driver_id', $drv->id)
                        ->whereHas('status', function($q){ $q->where('name', 'completed'); })
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    // Debug log to check what we're finding
                    if ($lastCompleted) {
                        $meta = $lastCompleted->meta ?? [];
                        $completed_at = $meta['completed_at'] ?? null;
                        \Log::info("Driver {$drv->id} last completed booking found", [
                            'booking_id' => $lastCompleted->id,
                            'updated_at' => $lastCompleted->updated_at,
                            'meta_completed_at' => $completed_at,
                            'meta_keys' => array_keys($meta)
                        ]);
                        $sinceFrom = $completed_at ?? $lastCompleted->updated_at ?? $drv->last_active_at;
                    } else {
                        \Log::info("Driver {$drv->id} no completed bookings found, using last_active_at", [
                            'last_active_at' => $drv->last_active_at
                        ]);
                        $sinceFrom = $drv->last_active_at;
                    }
                }

                $sinceStr = '-';
                if ($sinceFrom) {
                    try {
                        $sinceCarbon = \Carbon\Carbon::parse($sinceFrom);
                        // Format as "HH:MM DD/MM/YYYY"
                        $formatted = $sinceCarbon->format('H:i d/m/Y');

                        // POB should be prefixed with "since ", In Route and Idle show just the timestamp
                        if (isset($label) && $label === 'POB') {
                            $sinceStr =$formatted;
                        } else {
                            $sinceStr = $formatted;
                        }
                    } catch (\Exception $e) {
                        $sinceStr = '-';
                    }
                }

                $drv->status_label = $label;
                $drv->status_color = $color;
                $drv->status_since = $sinceStr;
            }
        }

        // Ensure drivers in the 'inactive' tab due to expired documents show status 'inactive'
        if ($tab === 'inactive') {
            $today = \Carbon\Carbon::today();
            foreach ($drivers as $drv) {
                // if already explicitly inactive skip
                if (($drv->status ?? '') === 'inactive') continue;
                $expiryFields = ['driving_license_expiry', 'private_hire_drivers_license_expiry', 'private_hire_vehicle_insurance_expiry', 'private_hire_vehicle_license_expiry', 'private_hire_vehicle_mot_expiry'];
                foreach ($expiryFields as $ef) {
                    if ($drv->{$ef} && \Carbon\Carbon::parse($drv->{$ef})->lt($today)) {
                        $drv->status = 'inactive';
                        break;
                    }
                }
            }
        }

        if ($request->ajax() || $request->get('partial')) {
            if ($tab === 'documents') {
                return view('admin.drivers._documents', compact('drivers'));
            }
            if ($tab === 'status') {
                return view('admin.drivers._status', compact('drivers', 'timingSettings'));
            }
            return view('admin.drivers._list', compact('drivers'));
        }

        return view('admin.drivers.index', compact('drivers', 'tab', 'timingSettings'));
    }

    public function show(Request $request, Driver $driver)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._show', compact('driver'));
        }

        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * Show all jobs assigned to a specific driver.
     */
    public function jobs(Request $request, Driver $driver)
    {
        $jobs = Booking::with('status')
            ->where('driver_id', $driver->id)
            ->orderByRaw("GREATEST(UNIX_TIMESTAMP(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta,'{}'), '$.status_changed_at')), updated_at)), UNIX_TIMESTAMP(updated_at)) DESC")
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('admin.drivers.jobs', compact('driver', 'jobs'));
    }

    /**
     * Check availability and documents for a driver (AJAX helper used by booking edit UI)
     */
    public function checkAvailability(Request $request, Driver $driver)
    {
        $result = ['success' => true, 'driver' => ['id' => $driver->id, 'name' => $driver->name], 'now' => now()->toIso8601String()];

        // Reactivate if window expired
        try {
            if (method_exists($driver, 'reactivateIfExpired')) {
                if ($driver->reactivateIfExpired()) {
                    $result['reactivated'] = true;
                    $driver = $driver->fresh();
                }
            }
        } catch (\Exception $e) {
            logger()->warning('checkAvailability reactivate failed: ' . $e->getMessage(), ['driver_id' => $driver->id]);
        }

        // Include unavailable window if present
        if ($driver->unavailable_from) $result['unavailable_from'] = $driver->unavailable_from;
        if ($driver->unavailable_to) $result['unavailable_to'] = $driver->unavailable_to;

        // Check for expired or soon-to-expire documents (15 day window)
        try {
            $docsList = [];
            $docs = [
                'driving_license_expiry' => 'Driving License',
                'private_hire_drivers_license_expiry' => 'Private Hire Drivers License',
                'private_hire_vehicle_insurance_expiry' => 'Private Hire Vehicle Insurance',
                'private_hire_vehicle_license_expiry' => 'Private Hire Vehicle License',
                'private_hire_vehicle_mot_expiry' => 'Private Hire Vehicle MOT',
            ];
            $today = \Carbon\Carbon::today();
            $threshold = $today->copy()->addDays(15);
            $hasExpired = false;

            foreach ($docs as $field => $label) {
                if ($driver->{$field}) {
                    $expiry = \Carbon\Carbon::parse($driver->{$field});
                    if ($expiry->lt($today)) {
                        $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                        $hasExpired = true;
                    } elseif ($expiry->lte($threshold)) {
                        $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                    }
                }
            }
            if (!empty($docsList)) {
                $result['documents'] = $docsList;
                $result['has_expired'] = $hasExpired;
                // Ensure we flag the driver status to the client as well
                $result['status'] = $driver->status;
            }
        } catch (\Exception $e) {
            logger()->warning('checkAvailability document check failed: ' . $e->getMessage(), ['driver_id' => $driver->id]);
        }

        return response()->json($result, 200);
    }

    /**
     * Show live tracking page for driver
     */
    public function track(Driver $driver, \App\Models\Booking $booking)
    {
        // Ensure booking belongs to this driver
        if ((int) $booking->driver_id !== (int) $driver->id) {
            abort(403, 'This booking is not assigned to the selected driver.');
        }

        // Check if booking is in trackable status (POB, arrived_at_pickup, or in_route)
        $meta = $booking->meta ?? [];
        $isInRoute       = isset($meta['in_route']) && $meta['in_route'] === true;
        $isArrivedPickup = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;
        $isPob = $booking->status && $booking->status->name === 'pob';
        
        if (!$isPob && !$isInRoute && !$isArrivedPickup) {
            abort(400, 'Driver tracking is available for jobs in In Route, Arrived at Pickup, or POB status.');
        }

        return view('admin.drivers.track', compact('driver', 'booking'));
    }


    /**
     * Get driver's current location (API endpoint)
     * If bookingId is 0 or booking not found, just return driver location (for non-POB tracking)
     */
    public function getLocation(Driver $driver, $bookingId = 0)
    {
        try {
            // Try to find the booking if a valid booking ID is provided
            $booking = null;
            if ($bookingId && $bookingId > 0) {
                $booking = \App\Models\Booking::find($bookingId);
                
                // Ensure booking belongs to this driver
                if ($booking && (int) $booking->driver_id !== (int) $driver->id) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            \Log::info('Getting driver location', [
                'driver_id' => $driver->id,
                'booking_id' => $booking ? $booking->id : 'none',
                'booking_status' => $booking ? (optional($booking->status)->name) : 'no booking'
            ]);

            // Get driver's last known location from driver_locations table
            $driverLocation = $driver->currentLocation;
            
            $location = null;
            $lastUpdate = null;
            
            if ($driverLocation) {
                $location = [
                    'lat' => (float) $driverLocation->latitude,
                    'lng' => (float) $driverLocation->longitude,
                    'accuracy' => $driverLocation->accuracy,
                ];
                $lastUpdate = $driverLocation->updated_at->toDateTimeString();
                \Log::info('Found real driver location', [
                    'driver_id' => $driver->id,
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                    'updated_at' => $lastUpdate
                ]);
            } else {
                \Log::warning('No location found for driver', ['driver_id' => $driver->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Driver location not available. Driver may not have shared location yet.'
                ], 404);
            }
            
            // Get booking destination - provide defaults if missing
            $destination = [
                'lat' => $booking ? ($booking->to_latitude ?? 51.5164) : null,
                'lng' => $booking ? ($booking->to_longitude ?? -0.1276) : null,
                'address' => $booking ? ($booking->to_address ?? 'London, UK') : null
            ];
            
            // Get pickup location - provide defaults if missing
            $pickup = [
                'lat' => $booking ? ($booking->from_latitude ?? 51.5014) : null,
                'lng' => $booking ? ($booking->from_longitude ?? -0.1419) : null,
                'address' => $booking ? ($booking->from_address ?? 'Pickup Location, London, UK') : null
            ];

            // Check if booking is in "in_route" or "arrived_at_pickup" status
            $meta = $booking ? ($booking->meta ?? []) : [];
            $isInRoute       = isset($meta['in_route']) && $meta['in_route'] === true;
            $isArrivedPickup = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;

            return response()->json([
                'success' => true,
                'in_route'          => $isInRoute,
                'arrived_at_pickup' => $isArrivedPickup,
                'driver' => [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'phone' => $driver->phone,
                    'vehicle_plate' => $driver->vehicle_plate,
                    'accuracy' => $location['accuracy'] ?? null,
                    'heading' => $location['heading'] ?? null,
                    'speed' => $location['speed'] ?? null
                ],
                'booking' => $booking ? [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'status' => optional($booking->status)->name ?? 'unknown'
                ] : null,
                'pickup' => [
                    'latitude' => $pickup['lat'],
                    'longitude' => $pickup['lng'],
                    'address' => $pickup['address']
                ],
                'destination' => [
                    'latitude' => $destination['lat'],
                    'longitude' => $destination['lng'],
                    'address' => $destination['address']
                ],
                'last_update' => $lastUpdate
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting driver location', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'driver_id' => $driver->id ?? 'unknown',
                'booking_id' => $bookingId ?? 'unknown'
            ]);
            return response()->json(['error' => 'Failed to get location: ' . $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $councils = DB::table('councils')->orderBy('council_name')->get();

        // Ensure $driver variable exists for form bindings (create vs edit)
        $driver = new Driver();
        
        // Full page create
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('councils', 'driver'));
        }
        return view('admin.drivers.create', compact('councils', 'driver'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            // Driver Info
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'council_id' => 'nullable|integer|exists:councils,id',
            'driver_lives' => 'nullable|string|max:500',
            'driver_address' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'driver_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Vehicle Info
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate',
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'passenger_capacity' => 'nullable|integer|min:1|max:20',
            'luggage_capacity' => 'nullable|integer|min:0|max:50',
            'vehicle_license_number' => 'nullable|string|max:100',
            'vehicle_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Driver Documents
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
            
            // Other
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number',
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            $data['driver_picture'] = $request->file('driver_picture')->store('drivers/pictures', 'public');
        }

        // Handle document uploads
        $documents = ['driving_license', 'private_hire_drivers_license', 'private_hire_vehicle_insurance', 'private_hire_vehicle_license', 'private_hire_vehicle_mot'];
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                $data[$doc] = $request->file($doc)->store('drivers/documents', 'public');
            }
        }

        // Handle multiple vehicle pictures
        if ($request->hasFile('vehicle_pictures')) {
            $vehiclePictures = [];
            foreach ($request->file('vehicle_pictures') as $file) {
                $vehiclePictures[] = $file->store('drivers/vehicles', 'public');
            }
            $data['vehicle_pictures'] = $vehiclePictures;
        }

        if (!empty($data['password'])) {
            // let the Driver model's setPasswordAttribute handle hashing
            // assign plain password so it gets hashed by the model
            // (avoid double-hashing which breaks login)
        } else {
            unset($data['password']);
        }

        $driver = Driver::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'driver' => $driver], 201);
        }

        return redirect()->route('admin.drivers.index')->with('success', 'Driver created');
    }

    public function edit(Request $request, Driver $driver)
    {
        $councils = DB::table('councils')->orderBy('council_name')->get();
        
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('driver', 'councils'));
        }
        return view('admin.drivers.edit', compact('driver', 'councils'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            // Driver Info
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'council_id' => 'nullable|integer|exists:councils,id',
            'driver_lives' => 'nullable|string|max:500',
            'driver_address' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'driver_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Vehicle Info
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate,'.$driver->id,
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'passenger_capacity' => 'nullable|integer|min:1|max:20',
            'luggage_capacity' => 'nullable|integer|min:0|max:50',
            'vehicle_license_number' => 'nullable|string|max:100',
            'vehicle_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Driver Documents
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
            
            // Other
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number,'.$driver->id,
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            // Delete old picture if exists
            if ($driver->driver_picture && \Storage::disk('public')->exists($driver->driver_picture)) {
                \Storage::disk('public')->delete($driver->driver_picture);
            }
            $data['driver_picture'] = $request->file('driver_picture')->store('drivers/pictures', 'public');
        }

        // Handle document uploads
        $documents = ['driving_license', 'private_hire_drivers_license', 'private_hire_vehicle_insurance', 'private_hire_vehicle_license', 'private_hire_vehicle_mot'];
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                // Delete old document if exists
                if ($driver->$doc && \Storage::disk('public')->exists($driver->$doc)) {
                    \Storage::disk('public')->delete($driver->$doc);
                }
                $data[$doc] = $request->file($doc)->store('drivers/documents', 'public');
            }
        }

        // Handle multiple vehicle pictures
        if ($request->hasFile('vehicle_pictures')) {
            // Delete old pictures if exist
            if ($driver->vehicle_pictures) {
                foreach ($driver->vehicle_pictures as $oldPicture) {
                    if (\Storage::disk('public')->exists($oldPicture)) {
                        \Storage::disk('public')->delete($oldPicture);
                    }
                }
            }
            
            $vehiclePictures = [];
            foreach ($request->file('vehicle_pictures') as $file) {
                $vehiclePictures[] = $file->store('drivers/vehicles', 'public');
            }
            $data['vehicle_pictures'] = $vehiclePictures;
        }

        if (!empty($data['password'])) {
            // let the Driver model's setPasswordAttribute handle hashing
            // assign plain password so it gets hashed by the model
            // (avoid double-hashing which breaks login)
        } else {
            unset($data['password']);
        }

        $driver->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'driver' => $driver], 200);
        }

        return redirect()->route('admin.drivers.show', $driver)->with('success', 'Driver updated');
    }

    public function destroy(Request $request, Driver $driver)
    {
        $driver->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted');
    }

    /**
     * Return pickup timing data for accepted drivers (used by the status-tab AJAX poller).
     * Accepts optional ?ids=1,2,3 to scope to specific driver IDs visible on screen.
     */
    public function getBookingTiming(Request $request)
    {
        $finishedStatusIds = \App\Models\BookingStatus::whereIn('name', ['completed', 'cancelled'])->pluck('id')->toArray();

        $query = Driver::where('status', 'active');

        // Scope to the driver IDs currently visible on the admin page
        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->get('ids'))));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $drivers = $query->get(['id', 'name']);

        $data = [];
        foreach ($drivers as $driver) {
            $booking = \App\Models\Booking::where('driver_id', $driver->id)
                ->whereNotIn('status_id', $finishedStatusIds)
                ->orderBy('scheduled_at', 'asc')
                ->orderBy('id', 'desc')
                ->first();

            $item = [
                'driver_id'         => $driver->id,
                'driver_name'       => $driver->name,
                'booking_id'        => null,
                'booking_code'      => null,
                'scheduled_at'      => null,
                'remaining_minutes' => null,
                'is_in_route'       => false,
                'is_pob'            => false,
                'pickup_address'    => null,
            ];

            if ($booking) {
                // Resolve pickup datetime: pickup_date+pickup_time first, scheduled_at as fallback
                $pickupAt = null;
                if ($booking->pickup_date && $booking->pickup_time) {
                    $pickupAt = \Carbon\Carbon::parse($booking->pickup_date->format('Y-m-d') . ' ' . $booking->pickup_time);
                } elseif ($booking->scheduled_at) {
                    $pickupAt = $booking->scheduled_at;
                }

                if ($pickupAt) {
                    $meta             = $booking->meta ?? [];
                    $isInRoute        = (isset($meta['in_route']) && $meta['in_route'] === true)
                                     || (isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true);
                    $isPob            = optional($booking->status)->name === 'pob';
                    $remainingMinutes = (int) now()->diffInMinutes($pickupAt, false);

                    $item['booking_id']        = $booking->id;
                    $item['booking_code']      = $booking->booking_code;
                    $item['scheduled_at']      = $pickupAt->toIso8601String();
                    $item['remaining_minutes'] = $remainingMinutes;
                    $item['is_in_route']       = $isInRoute;
                    $item['is_pob']            = $isPob;
                    $item['pickup_address']    = $booking->pickup_address;
                }
            }

            $data[] = $item;
        }

        return response()->json(['success' => true, 'drivers' => $data]);
    }

    /**
     * Send a late-warning notification to the driver (and admin for urgent warnings).
     * Called by the status-tab AJAX poller when thresholds are crossed.
     */
    public function sendLateWarning(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'booking_id'        => 'required|integer',
            'reason'            => 'required|string|in:two_hour_warning,urgent_warning',
            'remaining_minutes' => 'required|integer',
            'eta_minutes'       => 'nullable|integer',
        ]);

        $booking = \App\Models\Booking::find($validated['booking_id']);
        if (!$booking || (int) $booking->driver_id !== (int) $driver->id) {
            return response()->json(['success' => false, 'message' => 'Invalid booking'], 404);
        }

        // Deduplication: skip if same warning type was sent too recently
        $meta       = $booking->meta ?? [];
        $warningKey = 'late_warning_' . $validated['reason'] . '_sent_at';
        $lastSent   = isset($meta[$warningKey]) ? \Carbon\Carbon::parse($meta[$warningKey]) : null;

        if ($validated['reason'] === 'two_hour_warning' && $lastSent && $lastSent->diffInMinutes(now()) < 25) {
            return response()->json(['success' => false, 'message' => 'Already sent recently', 'skipped' => true]);
        }
        if ($validated['reason'] === 'urgent_warning' && $lastSent && $lastSent->diffInSeconds(now()) < 55) {
            return response()->json(['success' => false, 'message' => 'Already sent recently', 'skipped' => true]);
        }

        $remainingText = $this->formatMinutesText($validated['remaining_minutes']);
        $etaText       = isset($validated['eta_minutes']) ? $this->formatMinutesText($validated['eta_minutes']) : null;

        if ($validated['reason'] === 'two_hour_warning') {
            $title = 'Please Select "In Route"';
            $body  = "Your pickup is in {$remainingText}."
                   . ($etaText ? " ETA to pickup location: {$etaText}." : '')
                   . ' Please mark yourself as In Route now.';
        } else {
            $title = 'Urgent: Select "In Route" NOW';
            $body  = "Only {$remainingText} until your pickup! You must select In Route immediately.";
        }

        // Create driver notification - model observer auto-sends Expo push
        \App\Models\DriverNotification::create([
            'driver_id' => $driver->id,
            'title'     => $title,
            'message'   => $body,
        ]);

        // For urgent warnings, also notify all admins
        if ($validated['reason'] === 'urgent_warning') {
            $adminTitle = "Driver Not In Route – {$driver->name}";
            $adminMsg   = "Driver {$driver->name} has NOT selected In Route."
                        . " Booking: {$booking->booking_code}."
                        . " Time remaining: {$remainingText}."
                        . ($etaText ? " ETA to pickup: {$etaText}." : '');
            \App\Models\UserNotification::createForAdmins($adminTitle, $adminMsg);
        }

        // Record warning timestamp in booking meta (saveQuietly avoids model events)
        $meta[$warningKey] = now()->toIso8601String();
        $booking->meta     = $meta;
        $booking->saveQuietly();

        \Log::info("Late warning sent [{$validated['reason']}]", [
            'driver_id'         => $driver->id,
            'booking_id'        => $booking->id,
            'remaining_minutes' => $validated['remaining_minutes'],
            'eta_minutes'       => $validated['eta_minutes'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Warning sent']);
    }

    /** Format minutes as human-readable string, e.g. "3h 45m" */
    private function formatMinutesText(int $minutes): string
    {
        if ($minutes < 0) return 'overdue';
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        if ($hours > 0 && $mins > 0) return "{$hours}h {$mins}m";
        if ($hours > 0) return "{$hours}h";
        return "{$mins}m";
    }
}
