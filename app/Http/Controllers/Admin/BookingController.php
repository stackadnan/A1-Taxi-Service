<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\Driver;

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

        // If an AJAX request asks for counts only (used by the UI to refresh tab badges)
        if ($request->get('counts') || $request->ajax() && $request->get('counts')) {
            $countsArray = [];
            foreach (array_keys($sections) as $key) {
                $countsArray[$key] = $this->countForSection($key);
            }
            return response()->json(['counts' => $countsArray]);
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
            'booking_charges' => 'nullable|numeric|min:0'
        ]);

        $status = BookingStatus::where('name', 'new')->first();

        try {
            $booking = Booking::create([
                'booking_code' => $this->generateBookingCode(),
                'user_id' => $request->user() ? $request->user()->id : null,
                'passenger_name' => $data['passenger_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'email' => $data['email'] ?? null,
                'pickup_address' => $data['pickup_address'] ?? $data['pickup_address_line'] ?? null,
                'dropoff_address' => $data['dropoff_address'] ?? $data['dropoff_address_line'] ?? null,
                'pickup_date' => $data['pickup_date'] ?? null,
                'pickup_time' => $data['pickup_time'] ?? null,
                'vehicle_type' => $data['vehicle_type'] ?? ($request->input('vehicle_type_text') ?? null),
                'flight_number' => $data['flight_number'] ?? null,
                'meet_and_greet' => isset($data['meet_and_greet']) ? (bool) $data['meet_and_greet'] : false,
                'baby_seat' => isset($data['baby_seat']) ? (bool) $data['baby_seat'] : false,
                'baby_seat_age' => (isset($data['baby_seat']) && $data['baby_seat']) ? ($data['baby_seat_age'] ?? null) : null,
                'message_to_driver' => $data['message_to_driver'] ?? null,
                'message_to_admin' => $data['message_to_admin'] ?? null,
                'created_by_user_id' => $request->user() ? $request->user()->id : null,
                'status_id' => $status ? $status->id : null,
                'total_price' => isset($data['booking_charges']) ? $data['booking_charges'] : null,
                'passengers_count' => $data['passengers'] ?? 1,
                'luggage_count' => is_numeric($data['luggage'] ?? null) ? $data['luggage'] : 0,
                'meta' => [
                    'source' => $data['source'] ?? null,
                    'flight_time' => $data['flight_time'] ?? null
                ]
            ]);
        } catch (\Exception $e) {
            logger()->error('Manual booking create failed: ' . $e->getMessage(), ['exception' => $e]);
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to create booking', 'error' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Failed to create booking: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'booking' => $booking], 201);
        }

        return redirect()->route('admin.bookings.index', ['section' => 'new_manual'])->with('success', 'Booking created');
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
        // active drivers for assignment dropdown
        $activeDrivers = Driver::where('status','active')->orderBy('name')->get(['id','name']);

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
            'email' => 'nullable|email|max:255',
            'pickup_date' => 'required|date',
            'pickup_time' => 'required',
            'vehicle_type' => 'nullable|string|max:100',
            'flight_number' => 'nullable|string|max:100',
            'flight_time' => 'nullable',
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
            'use_percentage' => 'nullable|boolean',
            'driver_percentage' => 'nullable|numeric|min:0|max:100'  
        ]);

        $wasJunk = false;
        try {
            $booking->passenger_name = $data['passenger_name'] ?? $booking->passenger_name;
            $booking->phone = $data['phone'] ?? $booking->phone;
            $booking->email = $data['email'] ?? $booking->email;
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

                    // If driver changed (including from assigned->assigned to different), clear previous driver's response
                    if ($oldDriverId && $newDriverId && $oldDriverId != $newDriverId) {
                        try {
                            \App\Models\DriverNotification::create([
                                'driver_id' => $oldDriverId,
                                'title' => 'Job Reassigned',
                                'message' => 'Booking #' . ($booking->booking_code ?? $booking->id) . ' has been reassigned to another driver.'
                            ]);
                        } catch (\Exception $e) {
                            logger()->warning('Failed to notify previous driver about reassignment: ' . $e->getMessage());
                        }
                    }

                    $booking->driver_id = $newDriverId;
                    $booking->driver_name = $newDriver ? $newDriver->name : null;

                    // Clear any previous driver response since this is a new or changed assignment
                    $meta = is_array($booking->meta) ? $booking->meta : [];
                    if (isset($meta['driver_response'])) unset($meta['driver_response']);
                    if (isset($meta['driver_response_at'])) unset($meta['driver_response_at']);
                    $meta['status_changed_at'] = now()->toDateTimeString();
                    $booking->meta = $meta;
                }
            }

            // Respect explicit driver_price unless percentage mode is enabled
            if (array_key_exists('driver_price', $data)) {
                $booking->driver_price = $data['driver_price'];
            }

            // Percentage-based driver price support
            $driverPercent = null;
            if (!empty($data['use_percentage']) && isset($data['driver_percentage'])) {
                $driverPercent = (float)$data['driver_percentage'];
                $base = (float)($booking->total_price ?? 0);
                $computed = round($base * (1 - ($driverPercent / 100)), 2);
                $booking->driver_price = $computed;
            }

            // Preserve other meta items (source, flight_time etc.)
            $meta = is_array($booking->meta) ? $booking->meta : [];
            $meta['source'] = $data['source'] ?? ($meta['source'] ?? null);
            if (!empty($data['flight_time'])) $meta['flight_time'] = $data['flight_time'];

            // Store or clear driver percentage meta
            if ($driverPercent !== null) {
                $meta['driver_percentage'] = $driverPercent;
            } else {
                if (isset($meta['driver_percentage'])) unset($meta['driver_percentage']);
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

            $booking->save();

            // Refresh model and clear any cached relation so subsequent views/queries see the change immediately
            $booking->refresh();

            // If driver assignment changed and a driver was assigned, create a driver notification
            try {
                if (array_key_exists('driver_id', $data) && $booking->driver_id && $oldDriverId != $booking->driver_id) {
                    \App\Models\DriverNotification::create([
                        'driver_id' => $booking->driver_id,
                        'title' => 'New Job Assigned',
                        'message' => 'You have been assigned booking #' . ($booking->booking_code ?? $booking->id)
                    ]);
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
            return response()->json(['success' => true, 'booking' => $booking, 'moved_to' => ($wasJunk ? 'junk' : null)], 200);
        }

        // For non-AJAX updates, stay on the edit page and show a toast/alert instead of redirecting to the show page.
        $flashMsg = $wasJunk ? 'Booking moved to Junk' : 'Booking updated';
        return redirect()->back()->with('success', $flashMsg)->with('moved_to', ($wasJunk ? 'junk' : null));
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

    // Generate a unique numeric booking code prefixed with CD (e.g., CD123456)
    protected function generateBookingCode(): string
    {
        $prefix = 'CD';
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
