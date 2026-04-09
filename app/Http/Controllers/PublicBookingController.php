<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    public function preflight(Request $request)
    {
        return response('', 200)->withHeaders($this->corsHeaders($request));
    }

    public function save(Request $request)
    {
        $data = $request->validate([
            'quote_ref' => ['nullable', 'string', 'max:100'],
            'return_ref' => ['nullable', 'string', 'max:100'],
            'pickup' => ['required', 'string', 'max:500'],
            'dropoff' => ['required', 'string', 'max:500'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'string', 'max:20'],
            'return_pickup_date' => ['nullable', 'date', 'required_if:trip_type,return'],
            'return_pickup_time' => ['nullable', 'string', 'max:20', 'required_if:trip_type,return'],
            'passenger_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'passengers' => ['nullable', 'integer', 'min:1', 'max:16'],
            'suitcases' => ['nullable', 'integer', 'min:0', 'max:20'],
            'meet_and_greet' => ['nullable', 'boolean'],
            'message_to_driver' => ['nullable', 'string', 'max:2000'],
            'vehicle_type' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'trip_type' => ['required', 'in:one-way,return'],
            'payment_type' => ['nullable', 'in:cash,card'],
            'flight_number' => ['nullable', 'string', 'max:100'],
            'flight_landing_time' => ['nullable', 'string', 'max:20'],
            'source_url' => ['nullable', 'string', 'max:500'],
        ]);

        $status = BookingStatus::where('name', 'new')->first();
        $isReturn = $data['trip_type'] === 'return';

        try {
            $result = DB::transaction(function () use ($request, $data, $status, $isReturn) {
                $basePayload = [
                    'passenger_name' => $data['passenger_name'],
                    'phone' => $data['phone'],
                    'email' => $data['email'],
                    'passengers_count' => (int) ($data['passengers'] ?? 1),
                    'luggage_count' => (int) ($data['suitcases'] ?? 0),
                    'vehicle_type' => $data['vehicle_type'],
                    'total_price' => $data['price'],
                    'payment_type' => $data['payment_type'] ?? null,
                    'message_to_driver' => $data['message_to_driver'] ?? null,
                    'source_url' => $data['source_url'] ?? null,
                    'source_ip' => $request->ip(),
                    'status_id' => $status?->id,
                    'meta' => [
                        'source' => 'frontend',
                        'quote_ref' => $data['quote_ref'] ?? null,
                        'return_ref' => $data['return_ref'] ?? null,
                        'payment_type' => $data['payment_type'] ?? null,
                    ],
                ];

                $outbound = Booking::create(array_merge($basePayload, [
                    'booking_code' => $this->generateBookingCode(),
                    'pickup_address' => $data['pickup'],
                    'dropoff_address' => $data['dropoff'],
                    'pickup_date' => $data['pickup_date'],
                    'pickup_time' => $data['pickup_time'],
                    'flight_number' => $isReturn ? null : ($data['flight_number'] ?? null),
                    'meet_and_greet' => (bool) ($data['meet_and_greet'] ?? false),
                    'baby_seat' => false,
                    'baby_seat_age' => null,
                    'meta' => array_merge($basePayload['meta'], [
                        'trip_leg' => $isReturn ? 'outbound' : 'single',
                    ]),
                ]));

                $created = [$outbound];

                if ($isReturn) {
                    $returnBooking = Booking::create(array_merge($basePayload, [
                        'booking_code' => $this->generateBookingCode(),
                        'pickup_address' => $data['dropoff'],
                        'dropoff_address' => $data['pickup'],
                        'pickup_date' => $data['return_pickup_date'] ?? null,
                        'pickup_time' => $data['return_pickup_time'] ?? null,
                        'flight_number' => $data['flight_number'] ?? null,
                        'meet_and_greet' => (bool) ($data['meet_and_greet'] ?? false),
                        'baby_seat' => false,
                        'baby_seat_age' => null,
                        'meta' => array_merge($basePayload['meta'], [
                            'trip_leg' => 'return',
                            'flight_landing_time' => $data['flight_landing_time'] ?? null,
                        ]),
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
                    'booking_refs' => array_map(fn (Booking $booking) => $booking->booking_code, $created),
                ];
            });
        } catch (\Throwable $e) {
            \Log::error('Public booking save failed', [
                'error' => $e->getMessage(),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not save your booking right now.',
            ], 500)->withHeaders($this->corsHeaders($request));
        }

        return response()->json([
            'success' => true,
            'message' => $isReturn ? 'Return booking submitted successfully.' : 'Booking submitted successfully.',
            'booking' => $result['booking'],
            'bookings' => $result['bookings'],
            'booking_refs' => $result['booking_refs'],
            'created_count' => count($result['bookings']),
            'return_booking' => $isReturn,
        ], 201)->withHeaders($this->corsHeaders($request));
    }

    protected function corsHeaders(Request $request): array
    {
        $origin = $request->header('Origin', '*');

        $allowed = ['executiveairportcars.com', 'www.executiveairportcars.com', 'admin.executiveairportcars.com'];
        $host = parse_url($origin, PHP_URL_HOST) ?? '';
        $allowedOrigin = (in_array($host, $allowed, true) || str_ends_with($host, '.executiveairportcars.com') || empty($host))
            ? $origin
            : '*';

        return [
            'Access-Control-Allow-Origin' => $allowedOrigin,
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Requested-With, Accept',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Max-Age' => '86400',
        ];
    }

    protected function generateBookingCode(): string
    {
        $prefix = 'CD';
        for ($i = 0; $i < 10; $i++) {
            try {
                $num = random_int(100000, 999999);
            } catch (\Throwable $e) {
                $num = mt_rand(100000, 999999);
            }

            $code = $prefix . $num;
            if (!Booking::where('booking_code', $code)->exists()) {
                return $code;
            }
        }

        return strtoupper($prefix . uniqid());
    }
}