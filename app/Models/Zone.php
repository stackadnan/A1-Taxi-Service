<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Zone extends Model
{
    use HasFactory;
    protected $table = 'zones';

    protected $fillable = [
        'zone_name', 'latitude', 'longitude', 'code', 'status', 'meta'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'meta' => 'array'
    ];

    public function pricingFrom()
    {
        return $this->hasMany(PricingZone::class, 'from_zone_id');
    }

    public function pricingTo()
    {
        return $this->hasMany(PricingZone::class, 'to_zone_id');
    }
}
