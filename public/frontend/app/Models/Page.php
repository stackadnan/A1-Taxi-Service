<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $table = 'pages';

    protected $fillable = [
        'name',
        'head_title',
        'quote_title',
        'quote_subtitle',
        'quote_description',
        'why_us_title',
        'why_us_heading',
        'why_use_heading',
        'main_title',
        'main_description',
        'left_title',
        'left_description',
        'right_title',
        'right_description',
        'bottom_title',
        'bottom_description',
    ];

    public function seo()
    {
        return $this->hasMany(Seo::class, 'page_id');
    }
}
