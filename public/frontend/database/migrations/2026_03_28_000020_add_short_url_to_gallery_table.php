<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gallery')) {
            return;
        }

        if (!Schema::hasColumn('gallery', 'short_url')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->string('short_url', 32)->nullable()->after('image_path');
            });
        }

        DB::statement("UPDATE gallery SET short_url = CONCAT('i/', id) WHERE short_url IS NULL OR short_url = ''");

        $indexExists = DB::selectOne("SHOW INDEX FROM gallery WHERE Key_name = 'gallery_short_url_unique'");
        if (!$indexExists) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->unique('short_url', 'gallery_short_url_unique');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('gallery')) {
            return;
        }

        if (Schema::hasColumn('gallery', 'short_url')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->dropUnique('gallery_short_url_unique');
                $table->dropColumn('short_url');
            });
        }
    }
};
