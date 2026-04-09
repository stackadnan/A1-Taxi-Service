<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintLostFound extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';
    public const STATUS_PENDING = 'pending';
    public const STATUS_RESOLVED = 'resolved';

    protected $table = 'complaint_lost_found_requests';

    protected $fillable = [
        'booking_id',
        'name',
        'email',
        'concern',
        'lost_found',
        'status',
        'source_ip',
        'source_url',
    ];
}
