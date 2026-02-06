<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Driver extends Authenticatable
{
    use HasFactory;

    protected $table = 'drivers';

    protected $fillable = [
        'user_id', 'name', 'phone', 'email', 'license_number', 'vehicle_make', 'vehicle_model', 'vehicle_plate', 'car_type', 'car_color', 'coverage_area', 'badge_number', 'council_id', 'time_slot', 'status', 'rating', 'last_active_at', 'last_assigned_at', 'total_bookings', 'total_assigned', 'total_completed', 'total_cancelled', 'password',
        // Driver Documents
        'driving_license', 'driving_license_expiry', 'private_hire_drivers_license', 'private_hire_drivers_license_expiry', 'private_hire_vehicle_insurance', 'private_hire_vehicle_insurance_expiry', 'private_hire_vehicle_license', 'private_hire_vehicle_license_expiry', 'private_hire_vehicle_mot', 'private_hire_vehicle_mot_expiry',
        // Driver Info
        'driver_lives', 'driver_address', 'working_hours', 'bank_name', 'account_title', 'sort_code', 'account_number', 'driver_picture',
        // Vehicle Info
        'passenger_capacity', 'luggage_capacity', 'vehicle_license_number', 'vehicle_pictures',
        // Availability
        'unavailable_from', 'unavailable_to'
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
        'rating' => 'decimal:2',
        'driving_license_expiry' => 'date',
        'private_hire_drivers_license_expiry' => 'date',
        'private_hire_vehicle_insurance_expiry' => 'date',
        'private_hire_vehicle_license_expiry' => 'date',
        'private_hire_vehicle_mot_expiry' => 'date',
        'vehicle_pictures' => 'array',
        // Availability
        'unavailable_from' => 'datetime',
        'unavailable_to' => 'datetime',
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'driver_id');
    }

    public function locations()
    {
        return $this->hasMany(DriverLocation::class, 'driver_id');
    }

    public function currentLocation()
    {
        return $this->hasOne(DriverLocation::class, 'driver_id')->latest();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Automatically hash password when setting
     */
    public function setPasswordAttribute($value)
    {
        if (!$value) return;

        // If the value looks like an already-hashed password (bcrypt/argon), store as-is
        if (is_string($value) && (strpos($value, '$2y$') === 0 || strpos($value, '$argon') === 0)) {
            $this->attributes['password'] = $value;
            return;
        }

        $this->attributes['password'] = Hash::make($value);
    }

    /**
     * Scope to get active drivers
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Get new jobs (assigned but not started)
     */
    public function getNewJobsCount()
    {
        return $this->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'confirmed');
            })
            ->whereNull('meta->driver_response')
            ->count();
    }

    /**
     * Get accepted jobs count (confirmed status with accepted response, excluding POB)
     */
    public function getAcceptedJobsCount()
    {
        return $this->bookings()
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['confirmed', 'pob']);
            })
            ->where(function($q) {
                $q->where('meta->driver_response', 'accepted')
                  ->orWhereHas('status', function($sq) {
                      $sq->where('name', 'pob');
                  });
            })
            ->count();
    }

    /**
     * Get POB (Proof of Business) jobs count
     */
    public function getPobJobsCount()
    {
        return $this->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'pob');
            })
            ->count();
    }

    /**
     * Get completed jobs count
     */
    public function getCompletedJobsCount()
    {
        return $this->bookings()
            ->whereHas('status', function($q) {
                $q->whereIn('name', ['completed', 'pob']);
            })
            ->count();
    }

    /**
     * Get declined jobs count
     */
    public function getDeclinedJobsCount()
    {
        return $this->bookings()
            ->whereHas('status', function($q) {
                $q->where('name', 'confirmed');
            })
            ->where('meta->driver_response', 'declined')
            ->count();
    }

    /**
     * If unavailable_to is in the past, reactivate the driver and clear the window
     * Returns true if driver was reactivated
     */
    public function reactivateIfExpired()
    {
        try {
            if ($this->status === 'inactive' && $this->unavailable_to) {
                $now = now();
                if (\Carbon\Carbon::parse($this->unavailable_to)->lte($now)) {
                    $this->status = 'active';
                    $this->unavailable_from = null;
                    $this->unavailable_to = null;
                    $this->save();
                    return true;
                }
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to reactivateIfExpired: ' . $e->getMessage(), ['driver_id' => $this->id]);
        }
        return false;
    }
}

