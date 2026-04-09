<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CardBlog extends Model
{
    protected $table = 'card_blog';

    protected $fillable = [
        'title',
        'author',
        'body',
        'image',
        'post_date',
        'comments',
        'link',
        'order',
    ];

    protected $casts = [
        'post_date' => 'date',
        'comments' => 'integer',
        'order' => 'integer',
    ];
}
