<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'why_us_title')) {
                $table->string('why_us_title')->nullable()->after('quote_description');
            }

            if (!Schema::hasColumn('pages', 'why_us_heading')) {
                $table->string('why_us_heading')->nullable()->after('why_us_title');
            }
        });

        if (Schema::hasColumn('pages', 'why_us_title') && Schema::hasColumn('pages', 'why_us_heading')) {
            $pages = DB::table('pages')->select('id', 'name')->get();

            foreach ($pages as $page) {
                $name = trim((string) $page->name);
                if ($name === '') {
                    $name = 'Airport';
                }

                DB::table('pages')
                    ->where('id', $page->id)
                    ->update([
                        'why_us_title' => 'Why Choose Us',
                        'why_us_heading' => "Why Book {$name} Taxi with Us?",
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'why_us_heading')) {
                $table->dropColumn('why_us_heading');
            }

            if (Schema::hasColumn('pages', 'why_us_title')) {
                $table->dropColumn('why_us_title');
            }
        });
    }
};
