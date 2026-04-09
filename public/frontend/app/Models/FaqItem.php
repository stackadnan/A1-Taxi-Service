<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $table = 'faq_items';

    protected $fillable = [
        'page_id',
        'question',
        'answer',
        'order',
    ];

    protected $casts = [
        'page_id' => 'integer',
        'order' => 'integer',
    ];
}
