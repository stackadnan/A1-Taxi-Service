<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';

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
