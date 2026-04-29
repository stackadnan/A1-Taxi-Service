<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagePartial extends Model
{
    protected $table = 'partials';

    protected $fillable = [
        'page_id',
        'head',
        'preloader',
        'scroll_up',
        'offcanvas',
        'header',
        'breadcrumb',
        'quotes',
        'testimonials',
        'why_us',
        'card_fleet',
        'steps',
        'card_blog',
        'faq',
        'footer',
        'script',
    ];

    protected $casts = [
        'page_id' => 'integer',
        'head' => 'boolean',
        'preloader' => 'boolean',
        'scroll_up' => 'boolean',
        'offcanvas' => 'boolean',
        'header' => 'boolean',
        'breadcrumb' => 'boolean',
        'quotes' => 'boolean',
        'testimonials' => 'boolean',
        'why_us' => 'boolean',
        'card_fleet' => 'boolean',
        'steps' => 'boolean',
        'card_blog' => 'boolean',
        'faq' => 'boolean',
        'footer' => 'boolean',
        'script' => 'boolean',
    ];
}
