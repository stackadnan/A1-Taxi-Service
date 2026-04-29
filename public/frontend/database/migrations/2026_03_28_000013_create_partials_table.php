<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('partials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->boolean('head')->default(true);
            $table->boolean('preloader')->default(true);
            $table->boolean('scroll_up')->default(true);
            $table->boolean('offcanvas')->default(true);
            $table->boolean('header')->default(true);
            $table->boolean('breadcrumb')->default(true);
            $table->boolean('quotes')->default(true);
            $table->boolean('testimonials')->default(true);
            $table->boolean('why_us')->default(true);
            $table->boolean('card_fleet')->default(true);
            $table->boolean('steps')->default(true);
            $table->boolean('card_blog')->default(true);
            $table->boolean('faq')->default(true);
            $table->boolean('footer')->default(true);
            $table->boolean('script')->default(true);
            $table->timestamps();

            $table->unique('page_id');
            $table->index('page_id');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });

        if (Schema::hasTable('pages')) {
            $pageIds = DB::table('pages')->pluck('id');

            $rows = [];
            foreach ($pageIds as $pageId) {
                $rows[] = [
                    'page_id' => $pageId,
                    'head' => true,
                    'preloader' => true,
                    'scroll_up' => true,
                    'offcanvas' => true,
                    'header' => true,
                    'breadcrumb' => true,
                    'quotes' => true,
                    'testimonials' => true,
                    'why_us' => true,
                    'card_fleet' => true,
                    'steps' => true,
                    'card_blog' => true,
                    'faq' => true,
                    'footer' => true,
                    'script' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            if (!empty($rows)) {
                DB::table('partials')->insert($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partials');
    }
};
