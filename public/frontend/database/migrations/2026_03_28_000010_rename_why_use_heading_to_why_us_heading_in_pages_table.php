<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pages', 'why_us_heading')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('why_us_heading')->nullable()->after('why_us_title');
            });
        }

        if (Schema::hasColumn('pages', 'why_use_heading') && Schema::hasColumn('pages', 'why_us_heading')) {
            $pages = DB::table('pages')->select('id', 'why_use_heading', 'why_us_heading')->get();

            foreach ($pages as $page) {
                $oldValue = is_string($page->why_use_heading) ? trim($page->why_use_heading) : '';
                $newValue = is_string($page->why_us_heading) ? trim($page->why_us_heading) : '';

                if ($newValue === '' && $oldValue !== '') {
                    DB::table('pages')
                        ->where('id', $page->id)
                        ->update(['why_us_heading' => $oldValue]);
                }
            }

            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('why_use_heading');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasColumn('pages', 'why_use_heading')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('why_use_heading')->nullable()->after('why_us_title');
            });
        }

        if (Schema::hasColumn('pages', 'why_use_heading') && Schema::hasColumn('pages', 'why_us_heading')) {
            $pages = DB::table('pages')->select('id', 'why_use_heading', 'why_us_heading')->get();

            foreach ($pages as $page) {
                $oldValue = is_string($page->why_use_heading) ? trim($page->why_use_heading) : '';
                $newValue = is_string($page->why_us_heading) ? trim($page->why_us_heading) : '';

                if ($oldValue === '' && $newValue !== '') {
                    DB::table('pages')
                        ->where('id', $page->id)
                        ->update(['why_use_heading' => $newValue]);
                }
            }

            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('why_us_heading');
            });
        }
    }
};
