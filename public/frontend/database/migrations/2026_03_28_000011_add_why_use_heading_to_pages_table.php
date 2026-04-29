<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('pages', 'why_use_heading')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->string('why_use_heading')->nullable()->after('why_us_heading');
            });
        }

        if (Schema::hasColumn('pages', 'why_use_heading')) {
            $pages = DB::table('pages')->select('id', 'why_use_heading')->get();

            foreach ($pages as $page) {
                $existing = is_string($page->why_use_heading) ? trim($page->why_use_heading) : '';
                if ($existing !== '') {
                    continue;
                }
                DB::table('pages')
                    ->where('id', $page->id)
                    ->update(['why_use_heading' => 'Why You Should Use A1 Airport Cars']);
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pages', 'why_use_heading')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('why_use_heading');
            });
        }
    }
};
