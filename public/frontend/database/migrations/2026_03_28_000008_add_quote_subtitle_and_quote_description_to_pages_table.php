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
            if (!Schema::hasColumn('pages', 'quote_subtitle')) {
                $table->string('quote_subtitle')->nullable()->after('quote_title');
            }

            if (!Schema::hasColumn('pages', 'quote_description')) {
                $table->text('quote_description')->nullable()->after('quote_subtitle');
            }
        });

        if (Schema::hasColumn('pages', 'quote_subtitle') && Schema::hasColumn('pages', 'quote_description')) {
            $pages = DB::table('pages')->select('id', 'name')->get();

            foreach ($pages as $page) {
                $name = trim((string) $page->name);
                if ($name === '') {
                    $name = 'UK';
                }

                $quoteSubtitle = str_ends_with(strtolower($name), 'airport')
                    ? "{$name} Pickups and Drop-offs"
                    : "{$name} Airport Transfer Service";

                $quoteDescription = "Book professional {$name} airport taxi transfers to and from all major UK airports. We provide punctual drivers, fixed fares, and comfortable vehicles for every journey.";

                DB::table('pages')
                    ->where('id', $page->id)
                    ->update([
                        'quote_subtitle' => $quoteSubtitle,
                        'quote_description' => $quoteDescription,
                    ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'quote_description')) {
                $table->dropColumn('quote_description');
            }

            if (Schema::hasColumn('pages', 'quote_subtitle')) {
                $table->dropColumn('quote_subtitle');
            }
        });
    }
};
