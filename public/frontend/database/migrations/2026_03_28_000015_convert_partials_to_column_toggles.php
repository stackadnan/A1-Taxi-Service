<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function defaultRow(int $pageId): array
    {
        return [
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

    public function up(): void
    {
        if (!Schema::hasTable('partials') || !Schema::hasTable('pages')) {
            return;
        }

        if (!Schema::hasColumn('partials', 'partial_name')) {
            $pageIds = DB::table('pages')->pluck('id');
            foreach ($pageIds as $pageId) {
                $exists = DB::table('partials')->where('page_id', $pageId)->exists();
                if (!$exists) {
                    DB::table('partials')->insert($this->defaultRow((int) $pageId));
                }
            }

            return;
        }

        Schema::create('partials_new', function (Blueprint $table) {
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

        $columnMap = [
            'head' => 'head',
            'preloader' => 'preloader',
            'scroll-up' => 'scroll_up',
            'offcanvas' => 'offcanvas',
            'header' => 'header',
            'breadcrumb' => 'breadcrumb',
            'quotes' => 'quotes',
            'testimonials' => 'testimonials',
            'why-us' => 'why_us',
            'card-fleet' => 'card_fleet',
            'steps' => 'steps',
            'card-blog' => 'card_blog',
            'faq' => 'faq',
            'footer' => 'footer',
            'script' => 'script',
        ];

        $pageIds = DB::table('pages')->pluck('id');

        foreach ($pageIds as $pageId) {
            $row = $this->defaultRow((int) $pageId);

            $partialRows = DB::table('partials')
                ->where('page_id', $pageId)
                ->get(['partial_name', 'is_enabled']);

            foreach ($partialRows as $partialRow) {
                $name = strtolower(trim((string) $partialRow->partial_name));
                if (!array_key_exists($name, $columnMap)) {
                    continue;
                }

                $targetColumn = $columnMap[$name];
                $row[$targetColumn] = (bool) $partialRow->is_enabled;
            }

            DB::table('partials_new')->insert($row);
        }

        Schema::drop('partials');
        Schema::rename('partials_new', 'partials');
    }

    public function down(): void
    {
        if (!Schema::hasTable('partials') || !Schema::hasTable('pages')) {
            return;
        }

        if (Schema::hasColumn('partials', 'partial_name')) {
            return;
        }

        Schema::create('partials_old', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id');
            $table->string('partial_name');
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['page_id', 'partial_name']);
            $table->index('page_id');
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('cascade');
        });

        $reverseMap = [
            'head' => 'head',
            'preloader' => 'preloader',
            'scroll_up' => 'scroll-up',
            'offcanvas' => 'offcanvas',
            'header' => 'header',
            'breadcrumb' => 'breadcrumb',
            'quotes' => 'quotes',
            'testimonials' => 'testimonials',
            'why_us' => 'why-us',
            'card_fleet' => 'card-fleet',
            'steps' => 'steps',
            'card_blog' => 'card-blog',
            'faq' => 'faq',
            'footer' => 'footer',
            'script' => 'script',
        ];

        $rows = DB::table('partials')->get();

        foreach ($rows as $row) {
            foreach ($reverseMap as $column => $partialName) {
                DB::table('partials_old')->insert([
                    'page_id' => $row->page_id,
                    'partial_name' => $partialName,
                    'is_enabled' => (bool) $row->{$column},
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Schema::drop('partials');
        Schema::rename('partials_old', 'partials');
    }
};
