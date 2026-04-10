<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class ManageBookingController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $input = $request->all();
        $input['pickup_date'] = $this->normalizeDateInput($input['pickup_date'] ?? null);
        $input['return_pickup_date'] = $this->normalizeDateInput($input['return_pickup_date'] ?? null);

        $validator = Validator::make($input, [
            'quote_ref' => ['nullable', 'string', 'max:255'],
            'return_ref' => ['nullable', 'string', 'max:255'],
            'pickup' => ['required', 'string', 'max:500'],
            'dropoff' => ['required', 'string', 'max:500'],
            'pickup_date' => ['required', 'date'],
            'pickup_time' => ['required', 'date_format:H:i'],
            'passenger_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'passengers' => ['required', 'integer', 'min:1', 'max:20'],
            'suitcases' => ['required', 'integer', 'min:0', 'max:50'],
            'meet_and_greet' => ['nullable', 'boolean'],
            'message_to_driver' => ['nullable', 'string'],
            'vehicle_type' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'trip_type' => ['nullable', 'in:one-way,return'],
            'payment_type' => ['required', 'in:cash,card'],
            'flight_number' => ['nullable', 'string', 'max:255'],
            'flight_time' => ['nullable', 'date_format:H:i'],
            'baby_seat' => ['nullable', 'boolean'],
            'baby_seat_age' => ['nullable', 'string', 'max:50'],
            'return_pickup_date' => ['nullable', 'date', 'required_if:trip_type,return'],
            'return_pickup_time' => ['nullable', 'date_format:H:i', 'required_if:trip_type,return'],
            'return_flight_number' => ['nullable', 'string', 'max:255'],
            'return_flight_time' => ['nullable', 'date_format:H:i'],
            'return_meet_and_greet' => ['nullable', 'boolean'],
            'return_baby_seat' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first() ?: 'Please check your booking details and try again.',
            ], 422);
        }

        $validated = $validator->validated();
        $isReturnBooking = (($validated['trip_type'] ?? 'one-way') === 'return');

        try {
            $newStatusId = $this->resolveNewStatusId();
        } catch (\Throwable $e) {
            Log::error('Booking status resolution failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not create booking because booking status configuration is missing.',
            ], 500);
        }

        $now = now();
        $basePrice = array_key_exists('price', $validated) && $validated['price'] !== null
            ? (float) $validated['price']
            : null;
        $perLegBasePrice = ($isReturnBooking && $basePrice !== null)
            ? round($basePrice / 2, 2)
            : $basePrice;

        $basePayload = [
            'user_id' => 1,
            'status_id' => $newStatusId,
            'passenger_name' => $validated['passenger_name'],
            'email' => strtolower(trim($validated['email'])),
            'phone' => trim($validated['phone']),
            'passengers_count' => (int) $validated['passengers'],
            'luggage_count' => (int) $validated['suitcases'],
            'vehicle_type' => $this->blankToNull($validated['vehicle_type'] ?? null),
            'payment_type' => $validated['payment_type'],
            'message_to_driver' => $this->blankToNull($validated['message_to_driver'] ?? null),
            'created_by_user_id' => 1,
            'currency' => 'GBP',
            'created_at' => $now,
            'updated_at' => $now,
        ];

        try {
            $createdBookings = DB::transaction(function () use ($validated, $basePayload, $isReturnBooking, $perLegBasePrice) {
                $outboundCode = $this->generateBookingCode();
                $outboundPayload = array_merge($basePayload, [
                    'booking_code' => $outboundCode,
                    'pickup_address' => trim($validated['pickup']),
                    'dropoff_address' => trim($validated['dropoff']),
                    'pickup_date' => $validated['pickup_date'],
                    'pickup_time' => $validated['pickup_time'],
                    'flight_number' => $this->blankToNull($validated['flight_number'] ?? null),
                    'flight_arrival_time' => $this->combineDateAndTime($validated['pickup_date'] ?? null, $validated['flight_time'] ?? null),
                    'meet_and_greet' => (int) ($validated['meet_and_greet'] ?? 0),
                    'total_price' => $this->applyMeetAndGreetCharge($perLegBasePrice, (bool) ($validated['meet_and_greet'] ?? false)),
                    'baby_seat' => (int) ($validated['baby_seat'] ?? 0),
                    'baby_seat_age' => $this->blankToNull($validated['baby_seat_age'] ?? null),
                ]);

                $outboundId = DB::table('executiveairport_database.bookings')->insertGetId($outboundPayload);

                $created = [
                    'outbound' => [
                        'id' => $outboundId,
                        'code' => $outboundCode,
                    ],
                ];

                if ($isReturnBooking) {
                    $returnCode = $this->generateBookingCode();
                    $returnPayload = array_merge($basePayload, [
                        'booking_code' => $returnCode,
                        'pickup_address' => trim($validated['dropoff']),
                        'dropoff_address' => trim($validated['pickup']),
                        'pickup_date' => $validated['return_pickup_date'],
                        'pickup_time' => $validated['return_pickup_time'],
                        'flight_number' => $this->blankToNull($validated['return_flight_number'] ?? null),
                        'flight_arrival_time' => $this->combineDateAndTime($validated['return_pickup_date'] ?? null, $validated['return_flight_time'] ?? null),
                        'meet_and_greet' => (int) ($validated['return_meet_and_greet'] ?? 0),
                        'total_price' => $this->applyMeetAndGreetCharge($perLegBasePrice, (bool) ($validated['return_meet_and_greet'] ?? false)),
                        'baby_seat' => (int) ($validated['return_baby_seat'] ?? 0),
                        'baby_seat_age' => null,
                    ]);

                    $returnId = DB::table('executiveairport_database.bookings')->insertGetId($returnPayload);

                    DB::table('executiveairport_database.bookings')->where('id', $outboundId)->update([
                        'return_booking' => 1,
                        'return_booking_id' => $returnId,
                        'updated_at' => now(),
                    ]);

                    DB::table('executiveairport_database.bookings')->where('id', $returnId)->update([
                        'return_booking' => 1,
                        'return_booking_id' => $outboundId,
                        'updated_at' => now(),
                    ]);

                    $created['return'] = [
                        'id' => $returnId,
                        'code' => $returnCode,
                    ];
                }

                return $created;
            });
        } catch (\Throwable $e) {
            Log::error('Booking submit insert failed', [
                'error' => $e->getMessage(),
                'payload' => $validated,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Could not save booking. Please try again.',
            ], 500);
        }

        $primaryBookingId = (int) $createdBookings['outbound']['id'];
        $primaryBookingCode = (string) $createdBookings['outbound']['code'];
        $returnBookingId = isset($createdBookings['return']['id']) ? (int) $createdBookings['return']['id'] : null;
        $returnBookingCode = isset($createdBookings['return']['code']) ? (string) $createdBookings['return']['code'] : null;

        $this->notifyAdminsForNewBooking(
            $primaryBookingCode,
            (string) ($validated['passenger_name'] ?? ''),
            (string) ($validated['pickup'] ?? ''),
            (string) ($validated['dropoff'] ?? '')
        );

        if (($validated['payment_type'] ?? 'cash') === 'card') {
            try {
                $checkoutUrl = $this->createStripeCheckoutSession(
                    $validated,
                    $primaryBookingId,
                    $primaryBookingCode,
                    $returnBookingId,
                    $returnBookingCode
                );
            } catch (\Throwable $e) {
                Log::error('Stripe Checkout session creation failed', [
                    'error' => $e->getMessage(),
                    'primary_booking_id' => $primaryBookingId,
                    'return_booking_id' => $returnBookingId,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Card payment could not be started. Please try again.',
                ], 500);
            }

            $this->sendBookingEmails($primaryBookingId, $primaryBookingCode, $validated, $returnBookingId, $returnBookingCode);

            return response()->json([
                'success' => true,
                'created_count' => $returnBookingId ? 2 : 1,
                'booking_code' => $primaryBookingCode,
                'return_booking_code' => $returnBookingCode,
                'redirect_url' => $checkoutUrl,
            ]);
        }

        $this->sendBookingEmails($primaryBookingId, $primaryBookingCode, $validated, $returnBookingId, $returnBookingCode);

        session()->flash('booking_confirmation', [
            'booking_id' => $primaryBookingId,
            'booking_code' => $primaryBookingCode,
            'return_booking_id' => $returnBookingId,
            'return_booking_code' => $returnBookingCode,
            'created_count' => $returnBookingId ? 2 : 1,
            'passenger_name' => $validated['passenger_name'],
            'email' => strtolower(trim($validated['email'])),
            'payment_type' => $validated['payment_type'],
        ]);

        return response()->json([
            'success' => true,
            'created_count' => $returnBookingId ? 2 : 1,
            'booking_code' => $primaryBookingCode,
            'return_booking_code' => $returnBookingCode,
            'redirect_url' => route('booking.thank-you'),
        ]);
    }

    public function stripeSuccess(Request $request): RedirectResponse
    {
        $sessionId = trim((string) $request->query('session_id', ''));

        if ($sessionId === '') {
            return redirect()->route('home');
        }

        try {
            $session = $this->fetchStripeCheckoutSession($sessionId);
            $paymentStatus = strtolower((string) ($session['payment_status'] ?? ''));
            if ($paymentStatus !== 'paid') {
                Log::warning('Stripe Checkout returned non-paid status', [
                    'session_id' => $sessionId,
                    'payment_status' => $session['payment_status'] ?? null,
                ]);

                return redirect()->route('home');
            }

            $metadata = is_array($session['metadata'] ?? null) ? $session['metadata'] : [];

            $primaryBookingId = (int) ($metadata['primary_booking_id'] ?? 0);
            $returnBookingId = (int) ($metadata['return_booking_id'] ?? 0);
            $primaryBookingCode = (string) ($metadata['booking_code'] ?? '');
            $returnBookingCode = (string) ($metadata['return_booking_code'] ?? '');
            $passengerName = (string) ($metadata['passenger_name'] ?? '');
            $customerEmail = (string) ($metadata['customer_email'] ?? '');

            if ($primaryBookingId < 1) {
                Log::warning('Stripe Checkout metadata missing primary booking id', [
                    'session_id' => $sessionId,
                    'metadata' => $metadata,
                ]);

                return redirect()->route('home');
            }

            $paymentIntent = $session['payment_intent'] ?? null;
            $paymentId = null;
            if (is_array($paymentIntent)) {
                $paymentId = $paymentIntent['id'] ?? null;
            } elseif (is_string($paymentIntent)) {
                $paymentId = $paymentIntent;
            }
            $paymentId = $this->blankToNull($paymentId);

            try {
                if ($paymentId !== null) {
                    $bookingIds = [$primaryBookingId];
                    if ($returnBookingId > 0) {
                        $bookingIds[] = $returnBookingId;
                    }

                    $updatedRows = DB::table('executiveairport_database.bookings')
                        ->whereIn('id', $bookingIds)
                        ->update([
                            'payment_id' => $paymentId,
                            'updated_at' => now(),
                        ]);

                    if ($updatedRows === 0) {
                        $bookingCodes = array_values(array_filter([
                            $primaryBookingCode,
                            $returnBookingCode,
                        ], static fn ($value) => is_string($value) && trim($value) !== ''));

                        if (!empty($bookingCodes)) {
                            $updatedRows = DB::table('executiveairport_database.bookings')
                                ->whereIn('booking_code', $bookingCodes)
                                ->update([
                                    'payment_id' => $paymentId,
                                    'updated_at' => now(),
                                ]);
                        }
                    }

                    if ($updatedRows === 0) {
                        Log::warning('Stripe payment id update affected zero rows', [
                            'session_id' => $sessionId,
                            'payment_id' => $paymentId,
                            'primary_booking_id' => $primaryBookingId,
                            'return_booking_id' => $returnBookingId,
                            'primary_booking_code' => $primaryBookingCode,
                            'return_booking_code' => $returnBookingCode,
                        ]);
                    } else {
                        Log::info('Stripe payment id saved from callback', [
                            'session_id' => $sessionId,
                            'payment_id' => $paymentId,
                            'updated_rows' => $updatedRows,
                        ]);
                    }
                }
            } catch (\Throwable $dbUpdateError) {
                Log::error('Stripe payment id update failed', [
                    'session_id' => $sessionId,
                    'payment_id' => $paymentId,
                    'primary_booking_id' => $primaryBookingId,
                    'return_booking_id' => $returnBookingId,
                    'error' => $dbUpdateError->getMessage(),
                ]);
            }

            try {
                $primaryBooking = DB::table('executiveairport_database.bookings')
                    ->where('id', $primaryBookingId)
                    ->first();

                if ($primaryBooking) {
                    if ($primaryBookingCode === '') {
                        $primaryBookingCode = (string) ($primaryBooking->booking_code ?? '');
                    }
                    if ($passengerName === '') {
                        $passengerName = (string) ($primaryBooking->passenger_name ?? '');
                    }
                    if ($customerEmail === '') {
                        $customerEmail = (string) ($primaryBooking->email ?? '');
                    }
                }

                if ($returnBookingId > 0 && $returnBookingCode === '') {
                    $returnBooking = DB::table('executiveairport_database.bookings')
                        ->where('id', $returnBookingId)
                        ->first();
                    $returnBookingCode = (string) ($returnBooking?->booking_code ?? '');
                }
            } catch (\Throwable $dbReadError) {
                Log::warning('Stripe success DB read fallback failed', [
                    'session_id' => $sessionId,
                    'primary_booking_id' => $primaryBookingId,
                    'error' => $dbReadError->getMessage(),
                ]);
            }

            $this->notifyAdminsForCardPayment(
                $primaryBookingCode,
                $returnBookingCode !== '' ? $returnBookingCode : null,
                $paymentId,
                $passengerName
            );

            session()->flash('booking_confirmation', [
                'booking_id' => $primaryBookingId,
                'booking_code' => $primaryBookingCode,
                'return_booking_id' => $returnBookingId > 0 ? $returnBookingId : null,
                'return_booking_code' => $returnBookingCode !== '' ? $returnBookingCode : null,
                'created_count' => $returnBookingId > 0 ? 2 : 1,
                'passenger_name' => $passengerName,
                'email' => $customerEmail,
                'payment_type' => 'card',
                'payment_id' => $paymentId,
            ]);

            return redirect()->route('booking.thank-you', [
                'booking_id' => $primaryBookingId,
                'payment_id' => $paymentId,
            ]);
        } catch (\Throwable $e) {
            Log::error('Stripe success callback processing failed', [
                'session_id' => $sessionId,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('home');
        }
    }

    public function stripeCancel(Request $request): RedirectResponse
    {
        $bookingId = (int) $request->query('booking_id', 0);

        if ($bookingId > 0) {
            Log::info('Stripe Checkout canceled by user', [
                'booking_id' => $bookingId,
            ]);
        }

        return redirect()->route('home');
    }

    public function thankYou(): View
    {
        $booking = session('booking_confirmation');

        if (!is_array($booking)) {
            $bookingId = (int) request()->query('booking_id', 0);
            $paymentId = trim((string) request()->query('payment_id', ''));

            $fallback = [
                'booking_id' => $bookingId > 0 ? $bookingId : null,
                'booking_code' => '-',
                'return_booking_id' => null,
                'return_booking_code' => null,
                'created_count' => 1,
                'passenger_name' => '-',
                'email' => '-',
                'payment_type' => 'card',
                'payment_id' => $paymentId !== '' ? $paymentId : null,
            ];

            if ($bookingId > 0) {
                try {
                    $savedBooking = DB::table('executiveairport_database.bookings')
                        ->where('id', $bookingId)
                        ->first();

                    if ($savedBooking) {
                        $fallback['booking_code'] = (string) ($savedBooking->booking_code ?? '-');
                        $fallback['passenger_name'] = (string) ($savedBooking->passenger_name ?? '-');
                        $fallback['email'] = (string) ($savedBooking->email ?? '-');
                        if ($paymentId === '') {
                            $fallback['payment_id'] = (string) ($savedBooking->payment_id ?? '');
                        }
                    }
                } catch (\Throwable $e) {
                    Log::warning('Thank-you fallback booking read failed', [
                        'booking_id' => $bookingId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            $booking = $fallback;
        }

        $thankYouBookingId = (int) ($booking['booking_id'] ?? request()->query('booking_id', 0));
        $thankYouPaymentId = trim((string) ($booking['payment_id'] ?? request()->query('payment_id', '')));
        $thankYouBookingCodes = array_values(array_filter([
            (string) ($booking['booking_code'] ?? ''),
            (string) ($booking['return_booking_code'] ?? ''),
        ], static fn ($value) => trim((string) $value) !== ''));

        if ($thankYouPaymentId !== '') {
            try {
                $updatedRows = 0;

                if ($thankYouBookingId > 0) {
                    $updatedRows = DB::table('executiveairport_database.bookings')
                        ->where('id', $thankYouBookingId)
                        ->update([
                            'payment_id' => $thankYouPaymentId,
                            'updated_at' => now(),
                        ]);
                }

                if ($updatedRows === 0 && !empty($thankYouBookingCodes)) {
                    $updatedRows = DB::table('executiveairport_database.bookings')
                        ->whereIn('booking_code', $thankYouBookingCodes)
                        ->update([
                            'payment_id' => $thankYouPaymentId,
                            'updated_at' => now(),
                        ]);
                }

                if ($updatedRows > 0) {
                    Log::info('Thank-you fallback saved stripe payment id', [
                        'booking_id' => $thankYouBookingId,
                        'payment_id' => $thankYouPaymentId,
                        'updated_rows' => $updatedRows,
                    ]);
                }
            } catch (\Throwable $e) {
                Log::warning('Thank-you fallback payment save failed', [
                    'booking_id' => $thankYouBookingId,
                    'payment_id' => $thankYouPaymentId,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return view('booking-thank-you', [
            'bookingConfirmation' => $booking,
        ]);
    }

    public function lookup(Request $request): View
    {
        $validated = $request->validate([
            'booking_email' => ['required', 'email'],
            'booking_reference' => ['required', 'string', 'max:255'],
        ]);

        $email = strtolower(trim($validated['booking_email']));
        $reference = trim($validated['booking_reference']);

        $bookingQuery = DB::table('executiveairport_database.bookings')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->where(function ($query) use ($reference) {
                $query->where('booking_code', $reference);

                if (ctype_digit($reference)) {
                    $query->orWhere('id', (int) $reference);
                }
            });

        $booking = $bookingQuery->first();

        if (!$booking) {
            return view('manage-booking', [
                'lookupError' => 'No booking found for the provided email and booking reference.',
                'lookupInput' => [
                    'booking_email' => $validated['booking_email'],
                    'booking_reference' => $validated['booking_reference'],
                ],
            ]);
        }

        return view('manage-booking', [
            'booking' => $booking,
            'lookupInput' => [
                'booking_email' => $validated['booking_email'],
                'booking_reference' => $validated['booking_reference'],
            ],
        ]);
    }

    public function update(Request $request): View
    {
        $validator = Validator::make($request->all(), [
            'booking_id' => ['required', 'integer'],
            'booking_code' => ['required', 'string', 'max:255'],
            'booking_email_lookup' => ['required', 'email'],
            'passenger_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'alternate_phone' => ['nullable', 'string', 'max:255'],
            'passengers_count' => ['nullable', 'integer', 'min:1', 'max:20'],
            'luggage_count' => ['nullable', 'integer', 'min:0', 'max:50'],
            'pickup_date' => ['nullable', 'date'],
            'pickup_time' => ['nullable', 'date_format:H:i'],
            'flight_number' => ['nullable', 'string', 'max:255'],
            'meet_and_greet' => ['nullable', 'boolean'],
            'baby_seat' => ['nullable', 'boolean'],
            'baby_seat_age' => ['nullable', 'string', 'max:50'],
            'message_to_driver' => ['nullable', 'string'],
            'total_price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['nullable', 'string', 'max:5'],
        ]);

        if ($validator->fails()) {
            $bookingIdInput = (string) $request->input('booking_id', '');
            $booking = ctype_digit($bookingIdInput)
                ? DB::table('executiveairport_database.bookings')->where('id', (int) $bookingIdInput)->first()
                : null;

            return view('manage-booking', [
                'booking' => $booking,
                'lookupInput' => [
                    'booking_email' => (string) $request->input('booking_email_lookup', ''),
                    'booking_reference' => (string) $request->input('booking_code', ''),
                ],
                'bookingFormData' => $request->all(),
                'lookupError' => 'Please correct the highlighted values and try again.',
                'updateErrors' => $validator->errors()->all(),
            ]);
        }

        $validated = $validator->validated();

        $booking = DB::table('executiveairport_database.bookings')
            ->where('id', (int) $validated['booking_id'])
            ->where('booking_code', $validated['booking_code'])
            ->first();

        if (!$booking) {
            return view('manage-booking', [
                'lookupError' => 'Unable to update booking. Booking was not found.',
                'lookupInput' => [
                    'booking_email' => $validated['booking_email_lookup'],
                    'booking_reference' => $validated['booking_code'],
                ],
            ]);
        }

        $updatePayload = [
            'passenger_name' => $this->blankToNull($validated['passenger_name'] ?? null),
            'email' => $this->blankToNull($validated['email'] ?? null),
            'phone' => $this->blankToNull($validated['phone'] ?? null),
            'alternate_phone' => $this->blankToNull($validated['alternate_phone'] ?? null),
            'passengers_count' => $validated['passengers_count'] ?? $booking->passengers_count,
            'luggage_count' => $validated['luggage_count'] ?? $booking->luggage_count,
            'pickup_date' => $this->blankToNull($validated['pickup_date'] ?? null),
            'pickup_time' => $this->blankToNull($validated['pickup_time'] ?? null),
            'flight_number' => $this->blankToNull($validated['flight_number'] ?? null),
            'meet_and_greet' => (int) ($validated['meet_and_greet'] ?? 0),
            'baby_seat' => (int) ($validated['baby_seat'] ?? 0),
            'baby_seat_age' => $this->blankToNull($validated['baby_seat_age'] ?? null),
            'message_to_driver' => $this->blankToNull($validated['message_to_driver'] ?? null),
            'total_price' => $this->blankToNull($validated['total_price'] ?? null),
            'currency' => $this->blankToNull($validated['currency'] ?? null),
            'updated_at' => now(),
        ];

        $meta = $this->decodeBookingMeta($booking->meta ?? null);
        $changeSet = $this->buildFrontendBookingChangeSet($booking, $updatePayload);
        if (!empty($changeSet)) {
            $existingLogs = is_array($meta['change_logs'] ?? null) ? $meta['change_logs'] : [];

            $actorName = trim((string) ($validated['passenger_name'] ?? $booking->passenger_name ?? 'Customer'));
            if ($actorName === '') {
                $actorName = 'Customer';
            }
            $actorEmail = strtolower(trim((string) ($validated['booking_email_lookup'] ?? $booking->email ?? '')));
            $displayName = $actorEmail !== ''
                ? ($actorName . ' (Frontend: ' . $actorEmail . ')')
                : ($actorName . ' (Frontend)');

            $existingLogs[] = [
                'at' => now()->toDateTimeString(),
                'by' => [
                    'id' => null,
                    'name' => $displayName,
                ],
                'changes' => $changeSet,
            ];

            if (count($existingLogs) > 200) {
                $existingLogs = array_slice($existingLogs, -200);
            }

            $meta['change_logs'] = $existingLogs;
            $updatePayload['meta'] = json_encode($meta, JSON_UNESCAPED_UNICODE);
        }

        DB::table('executiveairport_database.bookings')
            ->where('id', (int) $validated['booking_id'])
            ->update($updatePayload);

        $updatedBooking = DB::table('executiveairport_database.bookings')
            ->where('id', (int) $validated['booking_id'])
            ->first();

        return view('manage-booking', [
            'booking' => $updatedBooking,
            'lookupInput' => [
                'booking_email' => $validated['booking_email_lookup'],
                'booking_reference' => $validated['booking_code'],
            ],
            'lookupSuccess' => 'Booking details updated successfully.',
        ]);
    }

    private function blankToNull(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    private function notifyAdminsForNewBooking(string $bookingCode, string $passengerName, string $pickup, string $dropoff): void
    {
        try {
            // Resolve admin-capable users via roles/permissions when available.
            // Fallback to all role-assigned users if schema/data differs across environments.
            try {
                $adminIds = DB::table('executiveairport_database.user_roles as user_roles')
                    ->join('executiveairport_database.roles as roles', 'roles.id', '=', 'user_roles.role_id')
                    ->leftJoin('executiveairport_database.role_permissions as role_permissions', 'role_permissions.role_id', '=', 'roles.id')
                    ->where(function ($query) {
                        $query->where('roles.name', 'Super Admin')
                            ->orWhereNotNull('role_permissions.permission_id');
                    })
                    ->distinct()
                    ->pluck('user_roles.user_id');
            } catch (\Throwable $roleQueryError) {
                Log::warning('Admin recipient role-query failed; falling back to user_roles only', [
                    'booking_code' => $bookingCode,
                    'error' => $roleQueryError->getMessage(),
                ]);
                $adminIds = collect();
            }

            if ($adminIds->isEmpty()) {
                $adminIds = DB::table('executiveairport_database.user_roles')
                    ->distinct()
                    ->pluck('user_id');
            }

            if ($adminIds->isEmpty()) {
                return;
            }

            $title = 'New Booking Received';
            $message = sprintf(
                'New booking #%s has been created for %s from %s to %s.',
                $bookingCode !== '' ? $bookingCode : 'N/A',
                trim($passengerName) !== '' ? $passengerName : 'a passenger',
                trim($pickup) !== '' ? $pickup : 'an unknown pickup location',
                trim($dropoff) !== '' ? $dropoff : 'an unknown dropoff location'
            );

            $now = now();
            $recentSince = $now->copy()->subSeconds(30);

            foreach ($adminIds as $adminId) {
                $exists = DB::table('executiveairport_database.user_notifications')
                    ->where('user_id', (int) $adminId)
                    ->where('title', $title)
                    ->where('message', $message)
                    ->where('created_at', '>=', $recentSince)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('executiveairport_database.user_notifications')->insert([
                    'user_id' => (int) $adminId,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => 0,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not create admin notifications for frontend booking', [
                'booking_code' => $bookingCode,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function notifyAdminsForCardPayment(string $primaryBookingCode, ?string $returnBookingCode, ?string $paymentId, ?string $passengerName): void
    {
        try {
            try {
                $adminIds = DB::table('executiveairport_database.user_roles as user_roles')
                    ->join('executiveairport_database.roles as roles', 'roles.id', '=', 'user_roles.role_id')
                    ->leftJoin('executiveairport_database.role_permissions as role_permissions', 'role_permissions.role_id', '=', 'roles.id')
                    ->where(function ($query) {
                        $query->where('roles.name', 'Super Admin')
                            ->orWhereNotNull('role_permissions.permission_id');
                    })
                    ->distinct()
                    ->pluck('user_roles.user_id');
            } catch (\Throwable $roleQueryError) {
                Log::warning('Admin recipient role-query failed for payment notification; falling back to user_roles only', [
                    'booking_code' => $primaryBookingCode,
                    'error' => $roleQueryError->getMessage(),
                ]);
                $adminIds = collect();
            }

            if ($adminIds->isEmpty()) {
                $adminIds = DB::table('executiveairport_database.user_roles')
                    ->distinct()
                    ->pluck('user_id');
            }

            if ($adminIds->isEmpty()) {
                return;
            }

            $title = 'Customer Payment Received';
            $refs = array_values(array_filter([
                trim((string) $primaryBookingCode),
                trim((string) ($returnBookingCode ?? '')),
            ]));
            $bookingRefText = empty($refs) ? 'N/A' : implode(', ', $refs);
            $customerText = trim((string) ($passengerName ?? ''));

            $message = sprintf(
                'Card payment is completed for booking #%s%s%s.',
                $bookingRefText,
                $customerText !== '' ? ' by ' . $customerText : '',
                ($paymentId !== null && trim((string) $paymentId) !== '') ? ' (Payment ID: ' . trim((string) $paymentId) . ')' : ''
            );

            $now = now();
            $recentSince = $now->copy()->subSeconds(60);

            foreach ($adminIds as $adminId) {
                $exists = DB::table('executiveairport_database.user_notifications')
                    ->where('user_id', (int) $adminId)
                    ->where('title', $title)
                    ->where('message', $message)
                    ->where('created_at', '>=', $recentSince)
                    ->exists();

                if ($exists) {
                    continue;
                }

                DB::table('executiveairport_database.user_notifications')->insert([
                    'user_id' => (int) $adminId,
                    'title' => $title,
                    'message' => $message,
                    'is_read' => 0,
                    'read_at' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Could not create admin payment notifications for frontend booking', [
                'booking_code' => $primaryBookingCode,
                'payment_id' => $paymentId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function decodeBookingMeta(mixed $meta): array
    {
        if (is_array($meta)) {
            return $meta;
        }

        if (is_string($meta) && trim($meta) !== '') {
            $decoded = json_decode($meta, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function buildFrontendBookingChangeSet(object $booking, array $updatePayload): array
    {
        $trackedFields = [
            'passenger_name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'alternate_phone' => 'Alt Phone',
            'passengers_count' => 'Passengers',
            'luggage_count' => 'Luggage',
            'pickup_date' => 'Pickup Date',
            'pickup_time' => 'Pickup Time',
            'flight_number' => 'Flight Number',
            'meet_and_greet' => 'Meet & Greet',
            'baby_seat' => 'Baby Seat',
            'baby_seat_age' => 'Baby Seat Age',
            'message_to_driver' => 'Note To Driver',
            'total_price' => 'Booking Price',
            'currency' => 'Currency',
        ];

        $changes = [];

        foreach ($trackedFields as $field => $label) {
            if (!array_key_exists($field, $updatePayload)) {
                continue;
            }

            $old = $this->normalizeFrontendAuditValue($field, $booking->{$field} ?? null);
            $new = $this->normalizeFrontendAuditValue($field, $updatePayload[$field] ?? null);

            if ($this->frontendAuditValuesEqual($old, $new)) {
                continue;
            }

            $changes[] = [
                'field' => $label,
                'old' => $this->formatFrontendAuditValue($old),
                'new' => $this->formatFrontendAuditValue($new),
            ];
        }

        return $changes;
    }

    private function normalizeFrontendAuditValue(string $field, mixed $value): mixed
    {
        if (is_string($value)) {
            $value = trim($value);
            if ($value === '') {
                $value = null;
            }
        }

        if (in_array($field, ['meet_and_greet', 'baby_seat'], true)) {
            return $this->toFrontendAuditBool($value);
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

        if ($field === 'pickup_time') {
            if ($value === null) {
                return null;
            }

            $raw = (string) $value;
            if (preg_match('/^\d{2}:\d{2}/', $raw) === 1) {
                return substr($raw, 0, 5);
            }

            try {
                return \Carbon\Carbon::parse($raw)->format('H:i');
            } catch (\Throwable $e) {
                return $raw;
            }
        }

        if (in_array($field, ['total_price'], true)) {
            if ($value === null) {
                return null;
            }
            return round((float) $value, 2);
        }

        if (in_array($field, ['passengers_count', 'luggage_count'], true)) {
            if ($value === null) {
                return null;
            }
            return (int) $value;
        }

        return $value;
    }

    private function toFrontendAuditBool(mixed $value): bool
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

    private function frontendAuditValuesEqual(mixed $left, mixed $right): bool
    {
        if (is_bool($left) && is_bool($right)) {
            return $left === $right;
        }

        if (is_numeric($left) && is_numeric($right)) {
            return (float) $left === (float) $right;
        }

        return (string) ($left ?? '') === (string) ($right ?? '');
    }

    private function formatFrontendAuditValue(mixed $value): string
    {
        if (is_bool($value)) {
            return $value ? 'Yes' : 'No';
        }

        if ($value === null || $value === '') {
            return '-';
        }

        if (is_float($value)) {
            $formatted = number_format($value, 2, '.', '');
            return rtrim(rtrim($formatted, '0'), '.');
        }

        return (string) $value;
    }

    private function generateBookingCode(): string
    {
        $prefix = $this->bookingReferencePrefix();
        $maxAttempts = 30;

        for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
            $digits = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $candidate = $prefix . $digits;

            $exists = DB::table('executiveairport_database.bookings')
                ->where('booking_code', $candidate)
                ->exists();

            if (!$exists) {
                return $candidate;
            }
        }

        throw new \RuntimeException('Unable to generate a unique booking code after multiple attempts.');
    }

    private function bookingReferencePrefix(): string
    {
        $raw = strtoupper((string) config('app.booking_reference_prefix', 'CD'));
        $lettersOnly = preg_replace('/[^A-Z]/', '', $raw) ?? '';

        if (strlen($lettersOnly) < 2) {
            return 'CD';
        }

        return substr($lettersOnly, 0, 2);
    }

    private function resolveNewStatusId(): int
    {
        $tables = ['executiveairport_database.booking_statuses', 'booking_statuses'];
        $errors = [];

        foreach ($tables as $table) {
            try {
                $statusId = DB::table($table)
                    ->whereRaw('LOWER(name) = ?', ['new'])
                    ->value('id');

                if (is_numeric($statusId)) {
                    return (int) $statusId;
                }

                $fallbackStatusId = DB::table($table)
                    ->orderBy('id')
                    ->value('id');

                if (is_numeric($fallbackStatusId)) {
                    Log::warning('Booking status "new" not found; using first available status id.', [
                        'table' => $table,
                        'fallback_status_id' => (int) $fallbackStatusId,
                    ]);

                    return (int) $fallbackStatusId;
                }
            } catch (\Throwable $e) {
                $errors[] = [
                    'table' => $table,
                    'error' => $e->getMessage(),
                ];

                Log::warning('Failed reading booking status table', [
                    'table' => $table,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        try {
            $fallbackFromBookings = DB::table('executiveairport_database.bookings')
                ->whereNotNull('status_id')
                ->orderBy('id')
                ->value('status_id');

            if (is_numeric($fallbackFromBookings)) {
                Log::warning('Booking status table lookup failed; using status_id from existing bookings.', [
                    'fallback_status_id' => (int) $fallbackFromBookings,
                ]);

                return (int) $fallbackFromBookings;
            }
        } catch (\Throwable $e) {
            $errors[] = [
                'table' => 'executiveairport_database.bookings',
                'error' => $e->getMessage(),
            ];

            Log::warning('Failed reading fallback status_id from bookings table', [
                'error' => $e->getMessage(),
            ]);
        }

        Log::error('Booking status resolution exhausted all fallbacks', [
            'errors' => $errors,
        ]);

        throw new \RuntimeException('Booking status "new" not found.');
    }

    private function sendBookingEmails(int $bookingId, string $bookingCode, array $validated, ?int $returnBookingId = null, ?string $returnBookingCode = null): void
    {
        $adminEmail = 'probot.pakistan@gmail.com';
        $userEmail = strtolower(trim((string) ($validated['email'] ?? '')));

        $passengerName = (string) ($validated['passenger_name'] ?? '');
        $phone = (string) ($validated['phone'] ?? '');
        $pickup = (string) ($validated['pickup'] ?? '');
        $dropoff = (string) ($validated['dropoff'] ?? '');
        $pickupDate = (string) ($validated['pickup_date'] ?? '');
        $pickupTime = (string) ($validated['pickup_time'] ?? '');
        $paymentType = strtoupper((string) ($validated['payment_type'] ?? ''));
        $vehicleType = (string) ($validated['vehicle_type'] ?? '');
        $tripType = strtolower((string) ($validated['trip_type'] ?? 'one-way'));
        $isReturnTrip = $tripType === 'return';

        $baseJourneyPrice = array_key_exists('price', $validated) && $validated['price'] !== null
            ? (float) $validated['price']
            : null;

        $outboundMeetAndGreet = (bool) ($validated['meet_and_greet'] ?? false);
        $returnMeetAndGreet = $isReturnTrip ? (bool) ($validated['return_meet_and_greet'] ?? false) : false;
        $meetAndGreetTotal = ($outboundMeetAndGreet ? 20.0 : 0.0) + ($returnMeetAndGreet ? 20.0 : 0.0);

        $totalJourneyPrice = $baseJourneyPrice !== null
            ? round($baseJourneyPrice + $meetAndGreetTotal, 2)
            : null;

        $basePriceText = $baseJourneyPrice !== null ? number_format($baseJourneyPrice, 2) : 'N/A';
        $meetAndGreetText = number_format($meetAndGreetTotal, 2);
        $price = $totalJourneyPrice !== null ? number_format($totalJourneyPrice, 2) : 'N/A';

        $adminBody = "A new booking has been created from the frontend.\n\n"
            . "Booking ID: {$bookingId}\n"
            . "Booking Code: {$bookingCode}\n"
            . "Passenger: {$passengerName}\n"
            . "Email: {$userEmail}\n"
            . "Phone: {$phone}\n"
            . "Pickup: {$pickup}\n"
            . "Dropoff: {$dropoff}\n"
            . "Pickup Date: {$pickupDate}\n"
            . "Pickup Time: {$pickupTime}\n"
            . "Payment Type: {$paymentType}\n"
            . "Vehicle Type: " . ($vehicleType !== '' ? $vehicleType : 'N/A') . "\n"
            . "Base Fare (GBP): {$basePriceText}\n"
            . "Meet & Greet (GBP): {$meetAndGreetText}\n"
            . "Total Price (GBP): {$price}\n";

        if ($returnBookingId !== null && $returnBookingCode !== null) {
            $adminBody .= "\nReturn Booking ID: {$returnBookingId}\n"
                . "Return Booking Code: {$returnBookingCode}\n";
        }

        $userBody = "Dear {$passengerName},\n\n"
            . "Thank you for your booking.\n"
            . "Your booking reference is {$bookingCode}.\n\n"
            . "Pickup: {$pickup}\n"
            . "Dropoff: {$dropoff}\n"
            . "Pickup Date: {$pickupDate}\n"
            . "Pickup Time: {$pickupTime}\n"
            . "Payment Type: {$paymentType}\n"
            . "Base Fare (GBP): {$basePriceText}\n"
            . "Meet & Greet (GBP): {$meetAndGreetText}\n"
            . "Total Price (GBP): {$price}\n\n"
            . "If you need any changes, please contact support and share your booking reference.\n";

        if ($returnBookingCode !== null) {
            $userBody .= "Return booking reference: {$returnBookingCode}.\n";
        }

        try {
            Mail::raw($adminBody, function ($message) use ($adminEmail, $bookingCode): void {
                $message->to($adminEmail)->subject('New Booking Received - ' . $bookingCode);
            });
        } catch (\Throwable $e) {
            Log::error('Admin booking email failed', [
                'booking_code' => $bookingCode,
                'error' => $e->getMessage(),
            ]);
        }

        if ($userEmail === '') {
            return;
        }

        try {
            Mail::raw($userBody, function ($message) use ($userEmail, $bookingCode): void {
                $message
                    ->to($userEmail)
                    ->subject('Booking Confirmation - ' . $bookingCode)
                    ->replyTo('probot.pakistan@gmail.com', 'A1 Airport Cars');
            });
        } catch (\Throwable $e) {
            Log::error('User booking email failed', [
                'booking_code' => $bookingCode,
                'user_email' => $userEmail,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function normalizeDateInput(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);
        if ($value === '') {
            return $value;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            return $value;
        }

        if (preg_match('/^(\d{2})[\/\-](\d{2})[\/\-](\d{4})$/', $value, $matches) === 1) {
            return $matches[3] . '-' . $matches[2] . '-' . $matches[1];
        }

        return $value;
    }

    private function combineDateAndTime(mixed $date, mixed $time): ?string
    {
        if (!is_string($date) || !is_string($time)) {
            return null;
        }

        $date = trim($date);
        $time = trim($time);

        if ($date === '' || $time === '') {
            return null;
        }

        if (preg_match('/^\d{2}:\d{2}$/', $time) !== 1) {
            return null;
        }

        return $date . ' ' . $time . ':00';
    }

    private function applyMeetAndGreetCharge(?float $basePrice, bool $hasMeetAndGreet): ?float
    {
        if ($basePrice === null) {
            return $hasMeetAndGreet ? 20.0 : null;
        }

        $total = $basePrice + ($hasMeetAndGreet ? 20.0 : 0.0);

        return round($total, 2);
    }

    private function createStripeCheckoutSession(
        array $validated,
        int $primaryBookingId,
        string $primaryBookingCode,
        ?int $returnBookingId = null,
        ?string $returnBookingCode = null
    ): string {
        $secretKey = (string) config('services.stripe.secret', '');
        if ($secretKey === '') {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $successUrl = route('booking.stripe.success', [], true) . '?session_id={CHECKOUT_SESSION_ID}';
        $cancelUrl = route('booking.stripe.cancel', ['booking_id' => $primaryBookingId], true);

        $requestPayload = [
            'mode' => 'payment',
            'success_url' => $successUrl,
            'cancel_url' => $cancelUrl,
            'customer_email' => strtolower(trim((string) ($validated['email'] ?? ''))),
            'client_reference_id' => $primaryBookingCode,
            'metadata[primary_booking_id]' => (string) $primaryBookingId,
            'metadata[booking_code]' => $primaryBookingCode,
            'metadata[trip_type]' => (string) ($validated['trip_type'] ?? 'one-way'),
            'metadata[return_booking_id]' => (string) ($returnBookingId ?? ''),
            'metadata[return_booking_code]' => (string) ($returnBookingCode ?? ''),
            'metadata[passenger_name]' => (string) ($validated['passenger_name'] ?? ''),
            'metadata[customer_email]' => strtolower(trim((string) ($validated['email'] ?? ''))),
        ];

        $baseTotal = array_key_exists('price', $validated) && $validated['price'] !== null
            ? (float) $validated['price']
            : 0.0;
        $tripType = (string) ($validated['trip_type'] ?? 'one-way');
        $hasReturn = $tripType === 'return' && is_string($returnBookingCode) && trim($returnBookingCode) !== '';

        if ($hasReturn) {
            $outboundBase = round($baseTotal / 2, 2);
            $returnBase = round($baseTotal - $outboundBase, 2);

            $outboundTotal = round($outboundBase + ((bool) ($validated['meet_and_greet'] ?? false) ? 20.0 : 0.0), 2);
            $returnTotal = round($returnBase + ((bool) ($validated['return_meet_and_greet'] ?? false) ? 20.0 : 0.0), 2);

            $outboundAmount = (int) round($outboundTotal * 100);
            $returnAmount = (int) round($returnTotal * 100);

            if ($outboundAmount > 0 && $returnAmount > 0) {
                $requestPayload['line_items[0][quantity]'] = 1;
                $requestPayload['line_items[0][price_data][currency]'] = 'gbp';
                $requestPayload['line_items[0][price_data][unit_amount]'] = $outboundAmount;
                $requestPayload['line_items[0][price_data][product_data][name]'] = 'Booking ' . $primaryBookingCode;
                $requestPayload['line_items[0][price_data][product_data][description]'] = 'Outbound journey';

                $requestPayload['line_items[1][quantity]'] = 1;
                $requestPayload['line_items[1][price_data][currency]'] = 'gbp';
                $requestPayload['line_items[1][price_data][unit_amount]'] = $returnAmount;
                $requestPayload['line_items[1][price_data][product_data][name]'] = 'Booking ' . trim((string) $returnBookingCode);
                $requestPayload['line_items[1][price_data][product_data][description]'] = 'Return journey';
            }
        }

        if (!isset($requestPayload['line_items[0][price_data][unit_amount]'])) {
            $total = $this->calculateStripeTotalAmount($validated);
            if ($total <= 0) {
                throw new \RuntimeException('Stripe amount must be greater than zero.');
            }

            $unitAmount = (int) round($total * 100);
            $requestPayload['line_items[0][quantity]'] = 1;
            $requestPayload['line_items[0][price_data][currency]'] = 'gbp';
            $requestPayload['line_items[0][price_data][unit_amount]'] = $unitAmount;
            $requestPayload['line_items[0][price_data][product_data][name]'] = 'Booking ' . $primaryBookingCode;
            $requestPayload['line_items[0][price_data][product_data][description]'] = 'Airport Transfer Booking';
        }

        $response = Http::asForm()
            ->withBasicAuth($secretKey, '')
            ->post('https://api.stripe.com/v1/checkout/sessions', $requestPayload);

        if (!$response->successful()) {
            throw new \RuntimeException('Stripe session request failed: ' . $response->status() . ' ' . $response->body());
        }

        $sessionUrl = (string) $response->json('url', '');
        if ($sessionUrl === '') {
            throw new \RuntimeException('Stripe did not return a checkout URL.');
        }

        return $sessionUrl;
    }

    private function fetchStripeCheckoutSession(string $sessionId): array
    {
        $secretKey = (string) config('services.stripe.secret', '');
        if ($secretKey === '') {
            throw new \RuntimeException('Stripe secret key is not configured.');
        }

        $response = Http::withBasicAuth($secretKey, '')
            ->get('https://api.stripe.com/v1/checkout/sessions/' . $sessionId, [
                'expand' => ['payment_intent'],
            ]);

        if (!$response->successful()) {
            throw new \RuntimeException('Stripe session fetch failed: ' . $response->status() . ' ' . $response->body());
        }

        $payload = $response->json();

        return is_array($payload) ? $payload : [];
    }

    private function calculateStripeTotalAmount(array $validated): float
    {
        $basePrice = array_key_exists('price', $validated) && $validated['price'] !== null
            ? (float) $validated['price']
            : 0.0;

        $meetAndGreetTotal = ((bool) ($validated['meet_and_greet'] ?? false) ? 20.0 : 0.0)
            + ((bool) ($validated['return_meet_and_greet'] ?? false) ? 20.0 : 0.0);

        return round($basePrice + $meetAndGreetTotal, 2);
    }
}
