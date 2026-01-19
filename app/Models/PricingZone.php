<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PricingZone extends Model
{
    use HasFactory;
    protected $table = 'pricing_zones';

    protected $fillable = [
        'from_zone_id','to_zone_id',
        'saloon_price','business_price','mpv6_price','mpv8_price',
        'pricing_mode','status'
    ];

    protected $casts = [
        'saloon_price' => 'decimal:2',
        'business_price' => 'decimal:2',
        'mpv6_price' => 'decimal:2',
        'mpv8_price' => 'decimal:2'
    ];

    public function fromZone()
    {
        return $this->belongsTo(Zone::class, 'from_zone_id');
    }

    public function toZone()
    {
        return $this->belongsTo(Zone::class, 'to_zone_id');
    }
}
