<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DriverInvoice extends Model
{
    use HasFactory;

    protected $table = 'driver_invoices';

    protected $fillable = [
        'driver_id',
        'created_by_user_id',
        'invoice_number',
        'invoice_date',
        'start_date',
        'end_date',
        'status',
        'jobs_count',
        'total_amount',
        'total_driver_fare',
        'line_items',
        'pdf_path',
        'sent_to_email',
        'sent_at',
        'meta',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'start_date' => 'date',
        'end_date' => 'date',
        'jobs_count' => 'integer',
        'total_amount' => 'decimal:2',
        'total_driver_fare' => 'decimal:2',
        'line_items' => 'array',
        'sent_at' => 'datetime',
        'meta' => 'array',
    ];

    public function driver()
    {
        return $this->belongsTo(Driver::class, 'driver_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }
}
