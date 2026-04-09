<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Breadcrumb extends Model
{
    protected $table = 'breadcrumbs';

    protected $fillable = [
        'page_key',
        'img',
        'title',
        'title2',
        'subtitle',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}
