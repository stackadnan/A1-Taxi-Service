<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    use HasFactory;

    protected $table = 'airports';

    protected $fillable = [
        'slug',
        'name',
        'head_title',
        'main_title',
        'main_description',
        'left_title',
        'left_description',
        'right_title',
        'right_description',
        'bottom_title',
        'bottom_description',
    ];
}
