<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverBroadcast extends Model
{
    use HasFactory;

    protected $table = 'driver_broadcasts';

    protected $fillable = [
        'title',
        'message',
        'broadcast_type',
        'status',
        'scheduled_at',
        'created_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
