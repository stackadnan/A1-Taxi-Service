<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'license_number', 'vehicle_make', 'vehicle_model', 'vehicle_plate', 'car_type', 'car_color', 'coverage_area', 'badge_number', 'council_id', 'time_slot', 'status', 'rating', 'last_active_at', 'last_assigned_at', 'total_bookings', 'total_assigned', 'total_completed', 'total_cancelled', 'password'
    ];

    /**
     * Hide sensitive attributes
     */
    protected $hidden = [
        'password'
    ];

    protected $casts = [
        'last_active_at' => 'datetime',
        'last_assigned_at' => 'datetime',
        'rating' => 'decimal:2'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
