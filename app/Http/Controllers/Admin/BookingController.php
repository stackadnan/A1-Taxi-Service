<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminSetting;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Driver;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $sections = [
            'new_manual' => 'New Manual Booking',
            'new' => 'New Bookings',
            'pending' => 'Pending Jobs',
            'confirmed' => 'Confirmed Jobs',
            'completed' => 'Completed Jobs',
            'cancelled' => 'Canceled Jobs',
            'junk' => 'Junk',
        ];

        $active = $request->get('section', 'new');
        if (!array_key_exists($active, $sections)) {
            $active = 'new';
        }

        // gather counts
        $counts = [];
        foreach (array_keys($sections) as $key) {
            $counts[$key] = $this->countForSection($key);
        }

        // Counts endpoint must return JSON even for AJAX requests.
        if ($request->boolean('counts') || $request->routeIs('admin.bookings.counts')) {
            return response()->json(['counts' => $counts]);
        }

        // build query depending on active section
        $query = Booking::with('status');

        switch ($active) {
            case 'new_manual':
                // new_manual shows manual bookings (created_by_user_id not null and status 'new')
                $query->whereNotNull('created_by_user_id')->whereHas('status', function($q){ $q->where('name', 'new'); });
                break;
            case 'new':
                $query->whereHas('status', function($q){ $q->where('name', 'new'); });
                break;
            case 'pending':
                // 'pending' tab should include bookings with status 'pending' or legacy 'in_progress'
                $query->whereHas('status', function($q){ $q->whereIn('name', ['pending','in_progress']); });
                break;
            case 'confirmed':
                $query->whereHas('status', function($q){ $q->where('name', 'confirmed'); });
                break;
            case 'completed':
                $query->whereHas('status', function($q){ $q->where('name', 'completed'); });
                break;
            case 'cancelled':
                $query->whereHas('status', function($q){ $q->where('name', 'cancelled'); });
                break;
            case 'junk':
                // stash junk in meta->junk = true OR where status name is 'junk' (support DB FK)
                $query->where(function($q){
                    $q->where('meta->junk', true)
                      ->orWhereHas('status', function($q2){ $q2->where('name','junk'); });
                });
                break;
        }

        // Prefer recent status changes (status_changed_at in meta) but fallback to updated_at
        $bookings = $query
            ->orderByRaw("GREATEST(UNIX_TIMESTAMP(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta,'{}'), '$.status_changed_at')), updated_at)), UNIX_TIMESTAMP(updated_at)) DESC")
            ->orderBy('id', 'desc')
            ->paginate(20)->withQueryString();

        // Prepare helper data for manual booking UI
        $statuses = BookingStatus::all();
        $vehicleTypes = ['Saloon','Business','MPV6','MPV8'];

        // If we're asking for the manual booking partial in a non-active scenario (i.e. the page will render it via include) we still want the bookings list available
        if ($active !== 'new_manual') {
            // leave bookings as-is
        }

        // If partial requested (ajax tab load) return only the relevant partial
        if ($request->get('partial') || $request->ajax()) {
            if ($active === 'new_manual') {
                return view('admin.bookings._manual', compact('statuses','vehicleTypes'));
            }
            return view('admin.bookings._list', compact('bookings','active'));
        }

        return view('admin.bookings.index', compact('sections', 'active', 'counts', 'bookings','statuses','vehicleTypes'));
    }

    protected function countForSection(string $key): int
    {
        switch ($key) {
            case 'new_manual':
                return Booking::whereNotNull('created_by_user_id')->whereHas('status', function($q){ $q->where('name', 'new'); })->count();
            case 'new':
                return Booking::whereHas('status', function($q){ $q->where('name', 'new'); })->count();
            case 'pending':
                // Count both 'pending' and 'in_progress' so tab badge is accurate
                return Booking::whereHas('status', function($q){ $q->whereIn('name', ['pending','in_progress']); })->count();
            case 'confirmed':
                return Booking::whereHas('status', function($q){ $q->where('name', 'confirmed'); })->count();
            case 'completed':
                return Booking::whereHas('status', function($q){ $q->where('name', 'completed'); })->count();
            case 'cancelled':
                return Booking::whereHas('status', function($q){ $q->where('name', 'cancelled'); })->count();
            case 'junk':
                return Booking::where(function($q){
                    $q->where('meta->junk', true)->orWhereHas('status', function($q2){ $q2->where('name','junk'); });
                })->count();
            default:
                return 0;
        }
    }

    public function storeManual(Request $request)
    {
        $data = $request->validate([
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'pickup_address_line' => 'nullable|string|max:255',
            'dropoff_address_line' => 'nullable|string|max:255',
            'passenger_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'nullable|email|max:255',
            'pickup_date' => 'required|date|after_or_equal:today',
            'pickup_time' => 'required',
            'vehicle_type' => 'nullable|string|max:100',
            'flight_number' => 'nullable|string|max:100',
            'flight_time' => 'nullable',
            'meet_and_greet' => 'nullable|boolean',
            'baby_seat' => 'nullable|boolean',
            'baby_seat_age' => 'nullable|string|max:50',
            'message_to_driver' => 'nullable|string|max:1000',
            'message_to_admin' => 'nullable|string|max:1000',
            'booking_charges' => 'nullable|numeric|min:0',
            'source' => 'nullable|string|max:50',
            'passengers' => 'nullable|integer|min:1',
            'luggage' => 'nullable|string|max:100',
            'booking_charges' => 'nullable|numeric|min:0',
            'is_return_booking' => 'nullable|boolean',
            'return_flight_number' => 'nullable|string|max:100',
            'return_pickup_date' => 'nullable|date|after_or_equal:today|required_if:is_return_booking,1',
            'return_pickup_time' => 'nullable|required_if:is_return_booking,1',
            'return_flight_time' => 'nullable',
            'return_meet_and_greet' => 'nullable|boolean',
            'return_baby_seat' => 'nullable|boolean'
        ]);

        $status = BookingStatus::where('name', 'new')->first();
        $isReturnBooking = (bool) ($data['is_return_booking'] ?? false);
        $userId = $request->user() ? $request->user()->id : null;
        $vatPercentage = max(0.0, min(100.0, (float) AdminSetting::get('vat_percentage', 0)));

        $pickupAddress = $data['pickup_address'] ?? $data['pickup_address_line'] ?? null;
        $dropoffAddress = $data['dropoff_address'] ?? $data['dropoff_address_line'] ?? null;

        $bookingCharges = isset($data['booking_charges']) ? (float) $data['booking_charges'] : null;
        $totalWithVat = $bookingCharges !== null
            ? round($bookingCharges + (($bookingCharges * $vatPercentage) / 100), 2)
            : null;

        try {
            $result = DB::transaction(function () use ($data, $request, $status, $isReturnBooking, $userId, $pickupAddress, $dropoffAddress, $totalWithVat) {
                $basePayload = [
                    'user_id' => $userId,
                    'passenger_name' => $data['passenger_name'] ?? null,
                    'phone' => $data['phone'] ?? null,
                    'email' => $data['email'] ?? null,
                    'vehicle_type' => $data['vehicle_type'] ?? ($request->input('vehicle_type_text') ?? null),
                    'message_to_driver' => $data['message_to_driver'] ?? null,
                    'message_to_admin' => $data['message_to_admin'] ?? null,
                    'created_by_user_id' => $userId,
                    'status_id' => $status ? $status->id : null,
                    'total_price' => $totalWithVat,
                    'passengers_count' => $data['passengers'] ?? 1,
                    'luggage_count' => is_numeric($data['luggage'] ?? null) ? $data['luggage'] : 0,
                ];

                $outbound = Booking::create(array_merge($basePayload, [
                    'booking_code' => $this->generateBookingCode(),
                    'pickup_address' => $pickupAddress,
                    'dropoff_address' => $dropoffAddress,
                    'pickup_date' => $data['pickup_date'] ?? null,
                    'pickup_time' => $data['pickup_time'] ?? null,
                    'flight_number' => $data['flight_number'] ?? null,
                    'meet_and_greet' => isset($data['meet_and_greet']) ? (bool) $data['meet_and_greet'] : false,
                    'baby_seat' => isset($data['baby_seat']) ? (bool) $data['baby_seat'] : false,
                    'baby_seat_age' => (isset($data['baby_seat']) && $data['baby_seat']) ? ($data['baby_seat_age'] ?? null) : null,
                    'meta' => [
                        'source' => $data['source'] ?? null,
                        'flight_time' => $data['flight_time'] ?? null,
                        'trip_leg' => $isReturnBooking ? 'outbound' : 'single',
                    ],
                ]));

                $created = [$outbound];

                if ($isReturnBooking) {
                    $returnBooking = Booking::create(array_merge($basePayload, [
                        'booking_code' => $this->generateBookingCode(),
                        'pickup_address' => $dropoffAddress,
                        'dropoff_address' => $pickupAddress,
                        'pickup_date' => $data['return_pickup_date'] ?? null,
                        'pickup_time' => $data['return_pickup_time'] ?? null,
                        'flight_number' => $data['return_flight_number'] ?? null,
                        'meet_and_greet' => isset($data['return_meet_and_greet']) ? (bool) $data['return_meet_and_greet'] : false,
                        'baby_seat' => isset($data['return_baby_seat']) ? (bool) $data['return_baby_seat'] : false,
                        'baby_seat_age' => null,
                        'meta' => [
                            'source' => $data['source'] ?? null,
                            'flight_time' => $data['return_flight_time'] ?? null,
                            'trip_leg' => 'return',
                        ],
                    ]));

                    $outbound->return_booking = true;
                    $outbound->return_booking_id = $returnBooking->id;
                    $outbound->save();

                    $returnBooking->return_booking = true;
                    $returnBooking->return_booking_id = $outbound->id;
                    $returnBooking->save();

                    $created[] = $returnBooking;
                }

                return [
                    'booking' => $outbound,
                    'bookings' => $created,
                ];
            });
        } catch (\Exception $e) {
            logger()->error('Manual booking create failed: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create booking', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'booking' => $result['booking'],
                'bookings' => $result['bookings'],
                'created_count' => count($result['bookings']),
                'return_booking' => $isReturnBooking,
            ], 201);
        }

        $message = $isReturnBooking ? 'Return bookings created successfully' : 'Booking created';
        return redirect()->route('admin.bookings.index', ['section' => 'new_manual'])->with('success', $message);
    }

    public function directions(Request $request)
    {
        $data = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lon' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lon' => 'required|numeric'
        ]);

        $cacheKey = 'directions:' . md5($data['pickup_lat'] . ',' . $data['pickup_lon'] . ':' . $data['dropoff_lat'] . ',' . $data['dropoff_lon']);

        $result = cache()->remember($cacheKey, 300, function() use ($data) {
            $apiKey = config('services.google.maps_api_key');
            $origin = $data['pickup_lat'] . ',' . $data['pickup_lon'];
            $destination = $data['dropoff_lat'] . ',' . $data['dropoff_lon'];
            $url = 'https://maps.googleapis.com/maps/api/directions/json?origin=' . $origin . '&destination=' . $destination . '&mode=driving&key=' . $apiKey . '&units=metric';

            try {
                $resp = \Illuminate\Support\Facades\Http::get($url);
                if (!$resp->ok()) return ['success' => false, 'error' => 'Directions provider error'];
                $json = $resp->json();
                if (empty($json['routes'])) return ['success' => false, 'error' => 'No route found'];

                $route = $json['routes'][0];
                $polyline = $route['overview_polyline']['points'] ?? null;
                $leg = $route['legs'][0] ?? null;
                $distance = $leg['distance']['value'] ?? null; // meters
                $duration = $leg['duration']['value'] ?? null; // seconds

                return ['success' => true, 'polyline' => $polyline, 'distance' => $distance, 'duration' => $duration];
            } catch (\Exception $e) {
                logger()->warning('Directions call failed: ' . $e->getMessage());
                return ['success' => false, 'error' => 'Exception contacting directions provider'];
            }
        });

        if (empty($result) || !$result['success']) {
            return response()->json(['success' => false, 'message' => $result['error'] ?? 'Failed to calculate route'], 422);
        }

        return response()->json($result);
    }

    /**
     * Show a single booking.
     */
    public function show(Request $request, Booking $booking)
    {
        if ($request->get('partial') || $request->ajax()) {
            return view('admin.bookings._show', compact('booking'));
        }
        return view('admin.bookings.show', compact('booking'));
    }

    /**
     * Show edit form for a booking (partial or full page)
     */
    public function edit(Request $request, Booking $booking)
    {
        $statuses = BookingStatus::all();
        $vehicleTypes = ['Saloon','Business','MPV6','MPV8'];
        // drivers for assignment dropdown - show active first, but include inactive so admin can see availability notes
        $activeDrivers = Driver::orderByRaw("CASE WHEN status='active' THEN 0 ELSE 1 END, name")->get(['id','name','status','unavailable_from','unavailable_to']);

        if ($request->get('partial') || $request->ajax()) {
            return view('admin.bookings._edit', compact('booking','statuses','vehicleTypes','activeDrivers'));
        }

        return view('admin.bookings.edit', compact('booking','statuses','vehicleTypes','activeDrivers'));
    }

    /**
     * Update booking
     */
    public function update(Request $request, Booking $booking)
    {
        $data = $request->validate([
            'pickup_address' => 'nullable|string|max:255',
            'dropoff_address' => 'nullable|string|max:255',
            'pickup_address_line' => 'nullable|string|max:255',
            'dropoff_address_line' => 'nullable|string|max:255',
            'passenger_name' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'alternate_phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'vehicle_type' => 'nullable|string|max:100',
            'flight_number' => 'nullable|string|max:100',
            'flight_time' => 'nullable',
            'booking_charges' => 'nullable|numeric|min:0',
            'payment_type' => 'nullable|string|max:50',
            'source' => 'nullable|string|max:2048',
            'meet_and_greet' => 'nullable|boolean',
            'baby_seat' => 'nullable|boolean',
            'baby_seat_age' => 'nullable|string|max:50',
            'message_to_driver' => 'nullable|string|max:1000',
            'message_to_admin' => 'nullable|string|max:1000',
            'status' => 'nullable|string|max:50',
            'passengers' => 'nullable|integer|min:1',
            'luggage' => 'nullable|string|max:100',
            'driver_id' => 'nullable',
            'driver_price' => 'nullable|numeric|min:0',
            'driver_display_price' => 'nullable|numeric|min:0',
            'use_percentage' => 'nullable|boolean',
            'driver_percentage' => 'nullable|numeric|min:0|max:100',
            'override_availability' => 'nullable|boolean'
        
        ]);

        $wasJunk = false;
        $originalAttributes = $booking->getAttributes();
        $originalMeta = is_array($booking->meta) ? $booking->meta : [];
        try {
            $booking->passenger_name = $data['passenger_name'] ?? $booking->passenger_name;
            $booking->phone = $data['phone'] ?? $booking->phone;
            $booking->alternate_phone = $data['alternate_phone'] ?? $booking->alternate_phone;
            $booking->email = $data['email'] ?? $booking->email;
            if (!empty($data['payment_type'])) {
                $booking->payment_type = $data['payment_type'];
            }
            if (!empty($data['source']) && filter_var($data['source'], FILTER_VALIDATE_URL)) {
                $booking->source_url = $data['source'];
            }
            $booking->pickup_date = $data['pickup_date'] ?? $booking->pickup_date;
            $booking->pickup_time = $data['pickup_time'] ?? $booking->pickup_time;
            $booking->vehicle_type = $data['vehicle_type'] ?? $booking->vehicle_type;
            $booking->flight_number = $data['flight_number'] ?? $booking->flight_number;
            $booking->meet_and_greet = isset($data['meet_and_greet']) ? (bool)$data['meet_and_greet'] : $booking->meet_and_greet;
            $booking->baby_seat = isset($data['baby_seat']) ? (bool)$data['baby_seat'] : $booking->baby_seat;
            $booking->baby_seat_age = (isset($data['baby_seat']) && $data['baby_seat']) ? ($data['baby_seat_age'] ?? null) : null;
            $booking->message_to_driver = $data['message_to_driver'] ?? $booking->message_to_driver;
            $booking->message_to_admin = $data['message_to_admin'] ?? $booking->message_to_admin;
            // update price if provided
            if (array_key_exists('booking_charges', $data)) {
                $booking->total_price = $data['booking_charges'];
            }

            // Save pickup/dropoff into dedicated columns (fallback to legacy *_line inputs)
            $booking->pickup_address = $data['pickup_address'] ?? $data['pickup_address_line'] ?? $booking->pickup_address;
            $booking->dropoff_address = $data['dropoff_address'] ?? $data['dropoff_address_line'] ?? $booking->dropoff_address;

            // passengers and luggage now have dedicated columns
            $booking->passengers_count = isset($data['passengers']) ? (int)$data['passengers'] : $booking->passengers_count;
            $booking->luggage_count = isset($data['luggage']) && is_numeric($data['luggage']) ? (int)$data['luggage'] : $booking->luggage_count;

            // driver assignment and driver price (only when provided)
            $oldDriverId = $booking->driver_id;
            if (array_key_exists('driver_id', $data)) {
                $incoming = $data['driver_id'];

                // Treat '__remove__' or empty string as explicit removal
                if ($incoming === '__remove__' || $incoming === '') {
                    // Notify previous driver that the job was removed (if any)
                    try {
                        if ($oldDriverId) {
                            \App\Models\DriverNotification::create([
                                'driver_id' => $oldDriverId,
                                'title' => 'Job Unassigned',
                                'message' => 'Booking #' . ($booking->booking_code ?? $booking->id) . ' has been unassigned from you.'
                            ]);
                        }
                    } catch (\Exception $e) {
                        logger()->warning('Failed to notify driver about removal: ' . $e->getMessage());
                    }

                    // Remove assignment
                    $booking->driver_id = null;
                    $booking->driver_name = null;

                    // Clear driver response meta and mark status change
                    $meta = is_array($booking->meta) ? $booking->meta : [];
                    if (isset($meta['driver_response'])) unset($meta['driver_response']);
                    if (isset($meta['driver_response_at'])) unset($meta['driver_response_at']);
                    $meta['status_changed_at'] = now()->toDateTimeString();
                    $booking->meta = $meta;
                } else {
                    // Assign to a driver id (validate existence)
                    $newDriverId = is_numeric($incoming) ? (int)$incoming : null;
                    $newDriver = $newDriverId ? Driver::find($newDriverId) : null;

                    // If driver's unavailable window has already expired, reactivate them immediately so assignment can proceed
                    try {
                        if ($newDriver && method_exists($newDriver, 'reactivateIfExpired')) {
                            if ($newDriver->reactivateIfExpired()) {
                                // refresh instance
                                $newDriver = $newDriver->fresh();
                                logger()->info('BookingController: reactivated driver during assignment check', ['driver_id' => $newDriver->id]);

                                // Record admin-triggered activity log
                                try {
                                    \DB::table('activity_logs')->insert([
                                        'user_id' => auth()->id() ?? null,
                                        'event' => 'driver_reactivated_by_admin',
                                        'auditable_type' => 'driver',
                                        'auditable_id' => $newDriver->id,
                                        'changes' => json_encode(['booking_id' => $booking->id, 'reason' => 'expired unavailability and reactivated at assignment']),
                                        'ip_address' => request()->ip() ?? null,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ]);
                                } catch (\Exception $e) { logger()->warning('Failed to record activity log for admin reactivation: ' . $e->getMessage()); }
                            }
                        }
                    } catch (\Exception $e) {
                        logger()->warning('Failed to reactivate driver during assignment: ' . $e->getMessage());
                    }

                    // Check availability conflict: if driver is inactive and has an unavailable window overlapping pickup
                    $override = isset($data['override_availability']) && $data['override_availability'];
                    try {
                        $pickupAt = null;
                        if ($booking->pickup_date && $booking->pickup_time) {
                            $pickupAt = \Carbon\Carbon::parse($booking->pickup_date . ' ' . $booking->pickup_time);
                        }

                        if ($newDriver && !$override) {
                            $assignmentWarning = null;
// Check driver documents: expired and expiring soon (15-day window)
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
                            if ($newDriver->{$field}) {
                                $expiry = \Carbon\Carbon::parse($newDriver->{$field});
                                if ($expiry->lt($today)) {
                                    $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                                    $hasExpired = true;
                                } elseif ($expiry->lte($threshold)) {
                                    $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                                }
                            }
                        }

                        if (!empty($docsList)) {
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json([
                                    'success' => false,
                                    'conflict' => true,
                                    'message' => $hasExpired ? 'Selected driver has expired documents that must be updated before assigning.' : 'Selected driver has documents that will expire soon.',
                                    'driver' => ['id' => $newDriver->id, 'name' => $newDriver->name],
                                    'documents' => $docsList,
                                    'has_expired' => $hasExpired
                                ], 200);
                            } else {
                                // For non-AJAX form submission: only block when there are expired docs.
                                $msg = $hasExpired ? 'Selected driver has expired documents that must be updated before assigning.' : 'Selected driver has documents that will expire soon.';
                                if ($hasExpired) {
                                    return redirect()->back()->with('error', $msg);
                                } else {
                                    // Expiring-only: allow assignment to proceed but notify admin
                                    try { session()->flash('warning', $msg); } catch (\Exception $e) { /* ignore session issues */ }
                                }
                            }
                        }
                            } catch (\Exception $e) {
                                logger()->warning('Document expiry check failed: ' . $e->getMessage());
                            }

                            // If driver is explicitly inactive, also check their unavailable window
                            if ($newDriver->status === 'inactive') {
                                // Re-check whether any documents are still expired — admin may have updated documents but not changed status yet
                                $expiryFields = ['driving_license_expiry', 'private_hire_drivers_license_expiry', 'private_hire_vehicle_insurance_expiry', 'private_hire_vehicle_license_expiry', 'private_hire_vehicle_mot_expiry'];
                                $hasExpiredDocs = false;
                                $today = \Carbon\Carbon::today();
                                foreach ($expiryFields as $ef) {
                                    if ($newDriver->{$ef} && \Carbon\Carbon::parse($newDriver->{$ef})->lt($today)) { $hasExpiredDocs = true; break; }
                                }

                                // If there are no expired documents, allow assignment to proceed even though status is still 'inactive'
                                if (! $hasExpiredDocs) {
                                    try { session()->flash('warning', 'Driver documents were updated; assignment is allowed though the driver remains marked as inactive.'); } catch (\Exception $e) { /* ignore */ }
                                } else {
                                    // If driver has an explicit unavailable window, only block when pickup falls inside it
                                    if ($newDriver->unavailable_from && $newDriver->unavailable_to && $pickupAt) {
                                        $from = \Carbon\Carbon::parse($newDriver->unavailable_from);
                                        $to = \Carbon\Carbon::parse($newDriver->unavailable_to);
                                        if ($pickupAt->betweenIncluded($from, $to)) {
                                            // Return a conflict response for AJAX clients to present a confirmation dialog
                                            if ($request->ajax() || $request->wantsJson()) {
                                                return response()->json([
                                                    'success' => false,
                                                    'conflict' => true,
                                                    'message' => 'Selected driver is unavailable between ' . $from->toDayDateTimeString() . ' and ' . $to->toDayDateTimeString(),
                                                    'driver' => ['id' => $newDriver->id, 'name' => $newDriver->name],
                                                    'unavailable_from' => $from->toIso8601String(),
                                                    'unavailable_to' => $to->toIso8601String(),
                                                    'pickup_at' => $pickupAt ? $pickupAt->toIso8601String() : null
                                                ], 200);
                                            } else {
                                                // Non-Ajax path: redirect back with message
                                                return redirect()->back()->with('error', 'Selected driver is unavailable between ' . $from->toDayDateTimeString() . ' and ' . $to->toDayDateTimeString());
                                            }
                                        }

                                    } else {
                                        // Driver is explicitly inactive with no window -> block assignment and show indefinite conflict
                                        if ($request->ajax() || $request->wantsJson()) {
                                            return response()->json([
                                                'success' => false,
                                                'conflict' => true,
                                                'message' => 'Selected driver is currently inactive.',
                                                'driver' => ['id' => $newDriver->id, 'name' => $newDriver->name],
                                                'unavailable_from' => null,
                                                'unavailable_to' => null,
                                                'pickup_at' => $pickupAt ? $pickupAt->toIso8601String() : null
                                            ], 200);
                                        } else {
                                            return redirect()->back()->with('error', 'Selected driver is currently inactive.');
                                        }
                                    }
                                }
                            }
                        }
                    } catch (\Exception $e) {
                        logger()->warning('Availability check failed: ' . $e->getMessage());
                    }

                    // If driver changed (including from assigned->assigned to different), clear previous driver's response
                    if ($oldDriverId && $newDriverId && $oldDriverId != $newDriverId) {
                        try {
                            // Prevent duplicates within a short window
                            $title = 'Job Unassigned';
                            $message = 'Booking #' . ($booking->booking_code ?? $booking->id) . ' has been unassigned from you.';
                            $recentWindow = 30; // seconds
                            $exists = \App\Models\DriverNotification::where('driver_id', $oldDriverId)
                                ->where('title', $title)
                                ->where('message', $message)
                                ->where('created_at', '>=', now()->subSeconds($recentWindow))
                                ->exists();
                            if (! $exists) {
                                \App\Models\DriverNotification::create(['driver_id' => $oldDriverId, 'title' => $title, 'message' => $message]);
                                logger()->info('BookingController: created DriverNotification for removal', ['driver_id' => $oldDriverId, 'booking_id' => $booking->id]);
                            } else {
                                logger()->info('BookingController: skipped duplicate DriverNotification for removal', ['driver_id' => $oldDriverId, 'booking_id' => $booking->id]);
                            }
                        } catch (\Exception $e) {
                            logger()->warning('Failed to notify previous driver about reassignment: ' . $e->getMessage());
                        }
                    }

                    $booking->driver_id = $newDriverId;
                    $booking->driver_name = $newDriver ? $newDriver->name : null;

                    // If the admin explicitly overrode availability, mark it in meta for auditing
                    if ($override) {
                        $meta = is_array($booking->meta) ? $booking->meta : [];
                        $meta['assigned_despite_unavailability'] = true;
                        $meta['assigned_despite_unavailability_at'] = now()->toDateTimeString();
                    }

                    // Clear any previous driver response since this is a new or changed assignment
                    $meta = is_array($booking->meta) ? $booking->meta : $meta;
                    if (isset($meta['driver_response'])) unset($meta['driver_response']);
                    if (isset($meta['driver_response_at'])) unset($meta['driver_response_at']);
                    $meta['status_changed_at'] = now()->toDateTimeString();
                    $booking->meta = $meta;
                }
            }

            // Pricing split:
            // - total_price: original booking fare (admin-side)
            // - meta.driver_display_price: fare shown to driver
            // - driver_price: final driver payout after percentage deduction from display price
            $driverPercent = null;
            if (isset($data['driver_percentage']) && $data['driver_percentage'] !== '') {
                $driverPercent = (float)$data['driver_percentage'];
                if ($driverPercent < 0) $driverPercent = 0;
                if ($driverPercent > 100) $driverPercent = 100;
            }

            $driverDisplayPrice = null;
            if (isset($data['driver_display_price']) && $data['driver_display_price'] !== '') {
                $driverDisplayPrice = (float)$data['driver_display_price'];
                if ($driverDisplayPrice < 0) $driverDisplayPrice = 0;
            }

            if ($driverDisplayPrice !== null) {
                $effectivePercent = $driverPercent;
                if ($effectivePercent === null) {
                    $existingMeta = is_array($booking->meta) ? $booking->meta : [];
                    $effectivePercent = isset($existingMeta['driver_percentage']) ? (float)$existingMeta['driver_percentage'] : 0;
                }
                $booking->driver_price = round($driverDisplayPrice * (1 - ($effectivePercent / 100)), 2);
            } elseif (array_key_exists('driver_price', $data)) {
                // Backward-compatible manual payout override if no display price provided
                $booking->driver_price = $data['driver_price'];
            }

            // Preserve other meta items (source, flight_time etc.)
            $meta = is_array($booking->meta) ? $booking->meta : [];
            $meta['source'] = $data['source'] ?? ($meta['source'] ?? null);
            if (!empty($data['flight_time'])) $meta['flight_time'] = $data['flight_time'];

            // Store or clear driver percentage meta
            if ($driverPercent !== null) {
                $meta['driver_percentage'] = $driverPercent;
            }

            if ($driverDisplayPrice !== null) {
                $meta['driver_display_price'] = $driverDisplayPrice;
            }

            $booking->meta = $meta; 

            // Handle status updates. Special value 'junk' sets a meta flag instead of a status record.
            $wasJunk = false;
            if (isset($data['status']) && $data['status'] !== '') {
                if ($data['status'] === 'junk') {
                    $meta = is_array($booking->meta) ? $booking->meta : [];
                    $meta['junk'] = true;
                    // record when the status was changed
                    $meta['status_changed_at'] = now()->toDateTimeString();
                    $booking->meta = $meta;

                    // ensure there's a 'junk' status record to satisfy FK constraints and make the booking queryable
                    $st = BookingStatus::firstOrCreate(['name' => 'junk'], ['description' => 'Junk']);
                    $booking->status_id = $st->id;
                    $wasJunk = true;
                } else {
                    // If we're setting a real status, clear the 'junk' flag if present
                    $meta = is_array($booking->meta) ? $booking->meta : [];
                    if (isset($meta['junk'])) { unset($meta['junk']); }

                    $st = BookingStatus::where('name', $data['status'])->first();
                    if ($st) {
                        $booking->status_id = $st->id;
                        // record when the status was changed
                        $meta['status_changed_at'] = now()->toDateTimeString();
                        $booking->meta = $meta;
                    }
                }
            }

            $meta = is_array($booking->meta) ? $booking->meta : [];
            $changeSet = $this->buildBookingChangeSet($booking, $originalAttributes, $originalMeta, $meta);
            if (!empty($changeSet)) {
                $changeLogs = isset($originalMeta['change_logs']) && is_array($originalMeta['change_logs'])
                    ? $originalMeta['change_logs']
                    : [];

                $actor = auth()->user();
                $actorName = $actor
                    ? ($actor->name ?? $actor->email ?? ('User #' . $actor->id))
                    : 'System';

                $changeLogs[] = [
                    'at' => now()->toDateTimeString(),
                    'by' => [
                        'id' => auth()->id(),
                        'name' => $actorName,
                    ],
                    'changes' => $changeSet,
                ];

                // Keep recent history bounded to avoid unbounded meta growth.
                if (count($changeLogs) > 200) {
                    $changeLogs = array_slice($changeLogs, -200);
                }

                $meta['change_logs'] = $changeLogs;
                $booking->meta = $meta;
            }

            $booking->save();

            // Refresh model and clear any cached relation so subsequent views/queries see the change immediately
            $booking->refresh();

            // Fire event for real-time updates. Include a driver_changed flag so listeners can avoid duplicate notifications.
            $data['driver_changed'] = (isset($oldDriverId) && $oldDriverId != $booking->driver_id) || (!isset($oldDriverId) && $booking->driver_id);
            event(new \App\Events\BookingUpdated($booking, auth()->user(), $data));

            // If driver assignment changed and a driver was assigned, create a driver notification
            try {
                if (array_key_exists('driver_id', $data) && $booking->driver_id && $oldDriverId != $booking->driver_id) {
                    // Prevent duplicate quick-fire assigned notifications
                    $title = 'New Job Assigned';
                    $message = 'You have been assigned booking #' . ($booking->booking_code ?? $booking->id);
                    $recentWindow = 30; // seconds
                    $exists = \App\Models\DriverNotification::where('driver_id', $booking->driver_id)
                        ->where('title', $title)
                        ->where('message', $message)
                        ->where('created_at', '>=', now()->subSeconds($recentWindow))
                        ->exists();
                    if (! $exists) {
                        \App\Models\DriverNotification::create([
                            'driver_id' => $booking->driver_id,
                            'title' => $title,
                            'message' => $message
                        ]);
                        logger()->info('BookingController: created DriverNotification for assignment', ['driver_id' => $booking->driver_id, 'booking_id' => $booking->id]);
                    } else {
                        logger()->info('BookingController: skipped duplicate DriverNotification for assignment', ['driver_id' => $booking->driver_id, 'booking_id' => $booking->id]);
                    }
                }
            } catch (\Exception $e) {
                logger()->warning('Failed to create driver notification: ' . $e->getMessage());
            }

            if ($wasJunk) {
                $booking->setRelation('status', null);
            }
        } catch (\Exception $e) {
            logger()->error('Failed to update booking: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to update booking', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to update booking: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            $resp = ['success' => true, 'booking' => $booking, 'moved_to' => ($wasJunk ? 'junk' : null)];
            if (isset($assignmentWarning) && $assignmentWarning) $resp['warning'] = $assignmentWarning;
            return response()->json($resp, 200);
        }

        // For non-AJAX updates, stay on the edit page and show a toast/alert instead of redirecting to the show page.
        $flashMsg = $wasJunk ? 'Booking moved to Junk' : 'Booking updated';
        $redir = redirect()->back()->with('success', $flashMsg)->with('moved_to', ($wasJunk ? 'junk' : null));
        if (isset($assignmentWarning) && $assignmentWarning) {
            try { $redir = $redir->with('warning', $assignmentWarning); } catch (\Exception $e) { /* ignore */ }
        }
        return $redir;
    }

    /**
     * Search previous bookings by booking code, passenger name or phone.
     * Returns a JSON array of matching bookings with fields used to autofill the form.
     */
    public function search(Request $request)
    {
        $data = $request->validate([
            'q' => 'required|string|max:255'
        ]);

        $q = trim($data['q']);

        $matches = Booking::query()
            ->where('booking_code', 'like', "%{$q}%")
            ->orWhere('passenger_name', 'like', "%{$q}%")
            ->orWhere('phone', 'like', "%{$q}%")
            ->orderBy('pickup_date', 'desc')
            ->limit(10)
            ->get(['id','booking_code','passenger_name','phone','email','pickup_date','pickup_time','pickup_address','dropoff_address','vehicle_type','total_price','baby_seat','baby_seat_age','meta']);

        return response()->json(['success' => true, 'results' => $matches]);
    }

    /**
     * Send booking confirmation email to customer from admin panel.
     */
    public function sendConfirmationEmail(Request $request, Booking $booking)
    {
        try {
            $email = trim((string)($booking->email ?? ''));
            if ($email === '') {
                return response()->json(['success' => false, 'message' => 'Customer email is missing for this booking.'], 422);
            }

            $subject = 'Booking Confirmation - ' . ($booking->booking_code ?? ('#' . $booking->id));
            $bodyLines = [
                'Dear ' . ($booking->passenger_name ?: 'Customer') . ',',
                '',
                'Your booking has been confirmed.',
                '',
                'Booking Reference: ' . ($booking->booking_code ?? ('#' . $booking->id)),
                'Pickup: ' . ($booking->pickup_address ?: '-'),
                'Dropoff: ' . ($booking->dropoff_address ?: '-'),
                'Pickup Date: ' . (optional($booking->pickup_date)->format('Y-m-d') ?: '-'),
                'Pickup Time: ' . ($booking->pickup_time ?: '-'),
                'Price: ' . ($booking->total_price !== null ? ('£' . number_format((float)$booking->total_price, 2)) : '-'),
                '',
                'Thank you for booking with us.',
            ];
            $body = implode("\n", $bodyLines);

            $this->sendPlainTextMail($email, $subject, $body);

            return response()->json(['success' => true, 'message' => 'Confirmation email sent successfully.']);
        } catch (\Throwable $e) {
            logger()->error('Failed to send booking confirmation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            $message = 'Failed to send confirmation email.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }
            return response()->json(['success' => false, 'message' => $message], 500);
        }
    }

    /**
     * Send booking cancellation email to customer from admin panel.
     */
    public function sendCancellationEmail(Request $request, Booking $booking)
    {
        try {
            $email = trim((string)($booking->email ?? ''));
            if ($email === '') {
                return response()->json(['success' => false, 'message' => 'Customer email is missing for this booking.'], 422);
            }

            $statusName = strtolower((string)($booking->status->name ?? ''));
            if ($statusName !== 'cancelled' && $statusName !== 'canceled') {
                return response()->json(['success' => false, 'message' => 'Cancellation email can only be sent for canceled bookings.'], 422);
            }

            $subject = 'Booking Cancellation - ' . ($booking->booking_code ?? ('#' . $booking->id));
            $bodyLines = [
                'Dear ' . ($booking->passenger_name ?: 'Customer') . ',',
                '',
                'Your booking has been cancelled.',
                '',
                'Booking Reference: ' . ($booking->booking_code ?? ('#' . $booking->id)),
                'Pickup: ' . ($booking->pickup_address ?: '-'),
                'Dropoff: ' . ($booking->dropoff_address ?: '-'),
                'Pickup Date: ' . (optional($booking->pickup_date)->format('Y-m-d') ?: '-'),
                'Pickup Time: ' . ($booking->pickup_time ?: '-'),
                '',
                'If you have any questions, please contact support.',
            ];
            $body = implode("\n", $bodyLines);

            $this->sendPlainTextMail($email, $subject, $body);

            return response()->json(['success' => true, 'message' => 'Cancellation email sent successfully.']);
        } catch (\Throwable $e) {
            logger()->error('Failed to send booking cancellation email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            $message = 'Failed to send cancellation email.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }
            return response()->json(['success' => false, 'message' => $message], 500);
        }
    }

    /**
     * Queue a pending review approval request for a completed booking.
     */
    public function sendReviewApprovalRequest(Request $request, Booking $booking)
    {
        try {
            $statusName = strtolower((string) ($booking->status->name ?? ''));
            if ($statusName !== 'completed') {
                return response()->json(['success' => false, 'message' => 'Review approval can only be requested for completed bookings.'], 422);
            }

            if (trim((string) ($booking->email ?? '')) === '') {
                return response()->json(['success' => false, 'message' => 'Customer email is missing for this booking.'], 422);
            }

            $booking->review_status = Booking::REVIEW_PENDING;
            $booking->review_requested_at = now();
            $booking->review_approved_at = null;
            $booking->review_rejected_at = null;
            $booking->review_email_sent_at = null;
            $booking->save();

            return response()->json(['success' => true, 'message' => 'Review approval request queued successfully.']);
        } catch (\Throwable $e) {
            logger()->error('Failed to queue review approval request', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);

            $message = 'Failed to queue review approval request.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }

            return response()->json(['success' => false, 'message' => $message], 500);
        }
    }

    /**
     * Send assigned driver information email to customer from admin panel.
     */
    public function sendDriverInfoEmail(Request $request, Booking $booking)
    {
        try {
            $email = trim((string)($booking->email ?? ''));
            if ($email === '') {
                return response()->json(['success' => false, 'message' => 'Customer email is missing for this booking.'], 422);
            }

            $driver = $booking->driver_id ? Driver::find($booking->driver_id) : null;
            if (!$driver) {
                return response()->json(['success' => false, 'message' => 'No driver is assigned to this booking yet.'], 422);
            }

            $subject = 'Driver Details - ' . ($booking->booking_code ?? ('#' . $booking->id));
            $bodyLines = [
                'Dear ' . ($booking->passenger_name ?: 'Customer') . ',',
                '',
                'Your assigned driver details are below:',
                '',
                'Driver Name: ' . ($driver->name ?: '-'),
                'Driver Phone: ' . ($driver->phone ?: '-'),
                'Vehicle: ' . trim(($driver->vehicle_make ?: '') . ' ' . ($driver->vehicle_model ?: '')),
                'Plate Number: ' . ($driver->vehicle_plate ?: '-'),
                '',
                'Booking Reference: ' . ($booking->booking_code ?? ('#' . $booking->id)),
                'Pickup: ' . ($booking->pickup_address ?: '-'),
                'Dropoff: ' . ($booking->dropoff_address ?: '-'),
                'Pickup Date: ' . (optional($booking->pickup_date)->format('Y-m-d') ?: '-'),
                'Pickup Time: ' . ($booking->pickup_time ?: '-'),
                '',
                'Thank you for choosing us.',
            ];
            $body = implode("\n", $bodyLines);

            $this->sendPlainTextMail($email, $subject, $body);

            return response()->json(['success' => true, 'message' => 'Driver information email sent successfully.']);
        } catch (\Throwable $e) {
            logger()->error('Failed to send driver info email', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
            ]);
            $message = 'Failed to send driver information email.';
            if (config('app.debug')) {
                $message .= ' ' . $e->getMessage();
            }
            return response()->json(['success' => false, 'message' => $message], 500);
        }
    }

    // Generate a unique numeric booking code prefixed with CD (e.g., CD123456)
    protected function sendPlainTextMail(string $to, string $subject, string $body): void
    {
        Mail::raw($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    private function buildBookingChangeSet(Booking $booking, array $originalAttributes, array $originalMeta, array $newMeta): array
    {
        $newAttributes = $booking->getAttributes();

        $trackedFields = [
            'passenger_name' => 'Name',
            'phone' => 'Phone',
            'alternate_phone' => 'Alt Phone',
            'email' => 'Email',
            'pickup_address' => 'Pickup Address',
            'dropoff_address' => 'Dropoff Address',
            'pickup_date' => 'Pickup Date',
            'pickup_time' => 'Pickup Time',
            'vehicle_type' => 'Vehicle Type',
            'flight_number' => 'Flight Number',
            'meet_and_greet' => 'Meet & Greet',
            'baby_seat' => 'Baby Seat',
            'baby_seat_age' => 'Baby Seat Age',
            'message_to_driver' => 'Note To Driver',
            'message_to_admin' => 'Note To Admin',
            'total_price' => 'Booking Price',
            'payment_type' => 'Payment Type',
            'source_url' => 'Source',
            'passengers_count' => 'Passengers',
            'luggage_count' => 'Luggage',
            'driver_id' => 'Driver',
            'driver_name' => 'Driver Name',
            'driver_price' => 'Driver Payout',
            'status_id' => 'Status',
        ];

        $trackedMeta = [
            'flight_time' => 'Flight Time',
            'driver_percentage' => 'Driver Percentage',
            'driver_display_price' => 'Driver Visible Price',
            'junk' => 'Junk Flag',
        ];

        $changes = [];

        foreach ($trackedFields as $field => $label) {
            $old = $originalAttributes[$field] ?? null;
            $new = $newAttributes[$field] ?? null;

            if ($field === 'status_id') {
                $old = $this->resolveBookingStatusName($old);
                $new = $this->resolveBookingStatusName($new);
            }

            $old = $this->normalizeAuditValueForField($field, $old);
            $new = $this->normalizeAuditValueForField($field, $new);

            if (! $this->auditValuesEqual($old, $new)) {
                $changes[] = [
                    'field' => $label,
                    'old' => $this->formatAuditValue($old),
                    'new' => $this->formatAuditValue($new),
                ];
            }
        }

        foreach ($trackedMeta as $metaKey => $label) {
            $old = $originalMeta[$metaKey] ?? null;
            $new = $newMeta[$metaKey] ?? null;

            $old = $this->normalizeAuditValueForField($metaKey, $old);
            $new = $this->normalizeAuditValueForField($metaKey, $new);

            if (! $this->auditValuesEqual($old, $new)) {
                $changes[] = [
                    'field' => $label,
                    'old' => $this->formatAuditValue($old),
                    'new' => $this->formatAuditValue($new),
                ];
            }
        }

        return $changes;
    }

    private function resolveBookingStatusName($statusId): ?string
    {
        if ($statusId === null || $statusId === '') {
            return null;
        }

        static $statusMap = null;
        if ($statusMap === null) {
            $statusMap = BookingStatus::query()->pluck('name', 'id')->toArray();
        }

        $statusId = (int) $statusId;

        return $statusMap[$statusId] ?? (string) $statusId;
    }

    private function auditValuesEqual($left, $right): bool
    {
        if (is_bool($left) && is_bool($right)) {
            return $left === $right;
        }

        if (is_numeric($left) && is_numeric($right)) {
            return (float) $left === (float) $right;
        }

        if (is_array($left) && is_array($right)) {
            return json_encode($left) === json_encode($right);
        }

        return (string) ($left ?? '') === (string) ($right ?? '');
    }

    private function formatAuditValue($value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        if (is_array($value)) {
            return json_encode($value);
        }

        if (is_float($value)) {
            $formatted = number_format($value, 2, '.', '');
            return rtrim(rtrim($formatted, '0'), '.');
        }

        return (string) $value;
    }

    private function normalizeAuditValueForField(string $field, $value)
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                $value = null;
            }
        }

        if (in_array($field, ['meet_and_greet', 'baby_seat', 'junk'], true)) {
            return $this->toAuditBool($value);
        }

        if ($field === 'pickup_date') {
            if ($value === null) {
                return null;
            }

            try {
                return \Carbon\Carbon::parse((string) $value)->format('Y-m-d');
            } catch (\Throwable $e) {
                return (string) $value;
            }
        }

        if (in_array($field, ['pickup_time', 'flight_time'], true)) {
            if ($value === null) {
                return null;
            }

            try {
                return \Carbon\Carbon::parse((string) $value)->format('H:i');
            } catch (\Throwable $e) {
                $raw = (string) $value;
                return strlen($raw) >= 5 ? substr($raw, 0, 5) : $raw;
            }
        }

        if (in_array($field, ['total_price', 'driver_price', 'driver_display_price', 'driver_percentage'], true)) {
            if ($value === null || $value === '') {
                return null;
            }
            return round((float) $value, 2);
        }

        if (in_array($field, ['passengers_count', 'luggage_count', 'driver_id'], true)) {
            if ($value === null || $value === '') {
                return null;
            }
            return (int) $value;
        }

        if (is_array($value)) {
            return $value;
        }

        return $value;
    }

    private function toAuditBool($value): bool
    {
        if ($value === null || $value === '') {
            return false;
        }

        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return ((int) $value) === 1;
        }

        $normalized = strtolower(trim((string) $value));
        return in_array($normalized, ['1', 'true', 'yes', 'on'], true);
    }

    protected function generateBookingCode(): string
    {
        $rawPrefix = strtoupper((string) AdminSetting::get('booking_reference_prefix', 'CD'));
        $lettersOnly = preg_replace('/[^A-Z]/', '', $rawPrefix) ?? '';
        $prefix = strlen($lettersOnly) >= 2 ? substr($lettersOnly, 0, 2) : 'CD';
        $maxAttempts = 10;
        for ($i = 0; $i < $maxAttempts; $i++) {
            try {
                $num = random_int(100000, 999999); // 6 digits
            } catch (\Exception $e) {
                // fallback if random_int unavailable for some reason
                $num = mt_rand(100000, 999999);
            }
            $code = $prefix . $num;
            if (! Booking::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        // Fallback: if too many collisions (very unlikely), use a unique string
        return strtoupper($prefix . uniqid());
    }
}
