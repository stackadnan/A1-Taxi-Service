<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverNotification extends Model
{
    use HasFactory;

    protected $table = 'driver_notifications';

    protected $fillable = [
        'driver_id', 'title', 'message', 'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
