<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverLocation extends Model
{
    protected $fillable = [
        'driver_id',
        'latitude',
        'longitude',
        'accuracy'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:3'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class);
    }
}