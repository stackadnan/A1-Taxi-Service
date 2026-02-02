<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
use App\Models\Driver;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

    protected static function booted()
    {
        static::updating(function($booking){
            try {
                if ($booking->isDirty('driver_id')) {
                    $old = $booking->getOriginal('driver_id');
                    $new = $booking->driver_id;
                    $driver = $new ? Driver::find($new) : null;
                    $pickupAt = null;
                    if ($booking->pickup_date && $booking->pickup_time) {
                        $pickupAt = Carbon::parse($booking->pickup_date->format('Y-m-d') . ' ' . $booking->pickup_time);
                    } elseif ($booking->scheduled_at) {
                        $pickupAt = Carbon::parse($booking->scheduled_at);
                    }

                    $context = [
                        'booking_id' => $booking->id ?? null,
                        'changed_by_user_id' => Auth::id() ?? null,
                        'old_driver_id' => $old,
                        'new_driver_id' => $new,
                        'new_driver_status' => $driver ? $driver->status : null,
                        'pickup_at' => $pickupAt ? $pickupAt->toDateTimeString() : null,
                        'unavailable_from' => $driver && $driver->unavailable_from ? (string)$driver->unavailable_from : null,
                        'unavailable_to' => $driver && $driver->unavailable_to ? (string)$driver->unavailable_to : null,
                        'stack' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8))->map(function($f){ return ($f['function'] ?? '') . '@' . ($f['file'] ?? '') . ':' . ($f['line'] ?? ''); })->take(6)->toArray(),
                    ];

                    Log::warning('Booking updating: driver_id changed', $context);
                }
            } catch (\Exception $e) {
                Log::error('Booking updating: failed to log driver change', ['error' => $e->getMessage()]);
            }
        });

        static::saving(function($booking){
            try {
                // Only check when driver assignment changed to a non-null driver
                if ($booking->isDirty('driver_id') && $booking->driver_id) {
                    $driver = Driver::find($booking->driver_id);
                    if (!$driver) return;

                    // Determine pickup datetime (prioritize explicit pickup_date + pickup_time, fallback to scheduled_at)
                    $pickupAt = null;
                    if ($booking->pickup_date && $booking->pickup_time) {
                        $pickupAt = Carbon::parse($booking->pickup_date->format('Y-m-d') . ' ' . $booking->pickup_time);
                    } elseif ($booking->scheduled_at) {
                        $pickupAt = Carbon::parse($booking->scheduled_at);
                    }

                    $from = $driver->unavailable_from ? Carbon::parse($driver->unavailable_from) : null;
                    $to = $driver->unavailable_to ? Carbon::parse($driver->unavailable_to) : null;

                    $isInactive = ($driver->status === 'inactive');
                    $hasWindow = ($from && $to);
                    $inRange = ($hasWindow && $pickupAt && $pickupAt->betweenIncluded($from, $to));

                    // if override flag is set in meta, allow assignment
                    $meta = is_array($booking->meta) ? $booking->meta : [];
                    $override = isset($meta['assigned_despite_unavailability']) && $meta['assigned_despite_unavailability'];

                    // Block only when:
                    // - driver is inactive without a specific unavailability window (global inactive), OR
                    // - there is an unavailability window and the pickup falls within it.
                    $shouldBlock = ((!$hasWindow && $isInactive) || ($hasWindow && $inRange));

                    if ($shouldBlock && !$override) {
                        // log full diagnostic before blocking, include reason
                        Log::warning('Booking save blocked: driver unavailable', [
                            'booking_id' => $booking->id ?? null,
                            'driver_id' => $driver->id,
                            'driver_status' => $driver->status,
                            'pickup_at' => $pickupAt ? $pickupAt->toDateTimeString() : null,
                            'unavailable_from' => $from ? $from->toDateTimeString() : null,
                            'unavailable_to' => $to ? $to->toDateTimeString() : null,
                            'override_flag' => $override,
                            'blocked_reason' => (!$hasWindow && $isInactive) ? 'inactive' : 'unavailable_window',
                            'changed_by_user_id' => Auth::id() ?? null,
                            'stack' => collect(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 8))->map(function($f){ return ($f['function'] ?? '') . '@' . ($f['file'] ?? '') . ':' . ($f['line'] ?? ''); })->take(6)->toArray(),
                        ]);

                        // prevent save by throwing exception, controller will catch and handle
                        throw new \Exception('Driver is unavailable for the booking pickup time');
                    }
                }
            } catch (\Exception $e) {
                // Re-throw to allow controllers to handle
                throw $e;
            }
        });
    }

    protected $fillable = [
        'booking_code','user_id','status_id','payment_id','passenger_name','phone','alternate_phone','email','passengers_count','luggage_count','pickup_address','dropoff_address','pickup_date','pickup_time','scheduled_at','flight_number','flight_arrival_time','meet_and_greet','baby_seat','baby_seat_age','vehicle_type','total_price','driver_price','driver_id','driver_name','return_booking','return_booking_id','message_to_driver','message_to_admin','source_url','source_ip','created_by_user_id','handled_by_user_id','estimated_distance_km','estimated_duration_minutes','meta','currency'
    ];

    protected $casts = [
        'pickup_date' => 'date',
        'scheduled_at' => 'datetime',
        'meta' => 'array'
    ];

    public function status()
    {
        return $this->belongsTo(BookingStatus::class, 'status_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
