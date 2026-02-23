<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\ExpoPushNotificationService;

class DriverNotification extends Model
{
    use HasFactory;

    protected $table = 'driver_notifications';

    protected $fillable = [
        'driver_id', 'title', 'message', 'is_read', 'read_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime'
    ];

    /**
     * Auto-send push notification whenever a DriverNotification is created
     */
    protected static function booted(): void
    {
        static::created(function (DriverNotification $notification) {
            try {
                ExpoPushNotificationService::sendForNotification($notification);
            } catch (\Exception $e) {
                \Log::warning('DriverNotification: push notification failed', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });
    }

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }
}
