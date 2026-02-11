<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $tab = $request->get('tab', 'active');

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
                    $isInRoute = isset($meta['in_route']) && $meta['in_route'] === true;
                    $statusKey = $currentBooking->status->name ?? 'in_progress';
                    
                    // Priority: POB (actual status) > in_route (meta flag) > other statuses
                    // This ensures POB status is shown correctly even if in_route flag wasn't cleared
                    if ($statusKey === 'pob') {
                        $label = 'POB';
                        $color = 'orange';
                        $sinceFrom = isset($meta['pob_marked_at']) ? $meta['pob_marked_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
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
                return view('admin.drivers._status', compact('drivers'));
            }
            return view('admin.drivers._list', compact('drivers'));
        }

        return view('admin.drivers.index', compact('drivers','tab'));
    }

    public function show(Request $request, Driver $driver)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._show', compact('driver'));
        }

        return view('admin.drivers.show', compact('driver'));
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
        if ($booking->driver_id !== $driver->id) {
            abort(403, 'This booking is not assigned to the selected driver.');
        }

        // Check if booking is in trackable status (POB or in_route)
        $meta = $booking->meta ?? [];
        $isInRoute = isset($meta['in_route']) && $meta['in_route'] === true;
        $isPob = $booking->status && $booking->status->name === 'pob';
        
        if (!$isPob && !$isInRoute) {
            abort(400, 'Driver tracking is available for jobs in In Route or POB status.');
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
                if ($booking && $booking->driver_id !== $driver->id) {
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

            // Check if booking is in "in_route" status (traveling to pickup)
            $meta = $booking ? ($booking->meta ?? []) : [];
            $isInRoute = isset($meta['in_route']) && $meta['in_route'] === true;

            return response()->json([
                'success' => true,
                'in_route' => $isInRoute,  // NEW: flag to indicate driver is traveling to pickup
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
}
