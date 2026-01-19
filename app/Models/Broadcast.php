<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Broadcast extends Model
{
    use HasFactory;

    protected $table = 'broadcasts';

    protected $fillable = ['created_by', 'title', 'message', 'channel', 'scheduled_at', 'sent_at', 'target'];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'target' => 'array'
    ];
}
