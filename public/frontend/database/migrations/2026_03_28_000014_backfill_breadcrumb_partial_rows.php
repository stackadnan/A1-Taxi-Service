<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partials') || !Schema::hasTable('pages')) {
            return;
        }

        if (Schema::hasColumn('partials', 'partial_name')) {
            $pageIds = DB::table('pages')->pluck('id');

            foreach ($pageIds as $pageId) {
                $exists = DB::table('partials')
                    ->where('page_id', $pageId)
                    ->where('partial_name', 'breadcrumb')
                    ->exists();

                if (!$exists) {
                    DB::table('partials')->insert([
                        'page_id' => $pageId,
                        'partial_name' => 'breadcrumb',
                        'is_enabled' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            return;
        }

        if (!Schema::hasColumn('partials', 'breadcrumb')) {
            return;
        }

        $pageIds = DB::table('pages')->pluck('id');

        foreach ($pageIds as $pageId) {
            $exists = DB::table('partials')
                ->where('page_id', $pageId)
                ->exists();

            if (!$exists) {
                DB::table('partials')->insert([
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
                ]);
            } else {
                DB::table('partials')
                    ->where('page_id', $pageId)
                    ->update(['breadcrumb' => true, 'updated_at' => now()]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('partials')) {
            return;
        }

        if (Schema::hasColumn('partials', 'partial_name')) {
            DB::table('partials')
                ->where('partial_name', 'breadcrumb')
                ->delete();

            return;
        }

        if (Schema::hasColumn('partials', 'breadcrumb')) {
            DB::table('partials')->update([
                'breadcrumb' => false,
                'updated_at' => now(),
            ]);
        }
    }
};
