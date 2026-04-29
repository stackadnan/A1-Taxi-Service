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
            if (!Schema::hasColumn('pages', 'number_of_rows')) {
                $table->string('number_of_rows', 100)->nullable()->after('why_use_heading');
            }
        });

        if (Schema::hasColumn('pages', 'content_layout_columns')) {
            DB::statement("UPDATE pages SET number_of_rows = CASE content_layout_columns WHEN 1 THEN '1' WHEN 2 THEN '2' WHEN 3 THEN '3' ELSE number_of_rows END");
        }

        DB::statement("UPDATE pages SET number_of_rows = '1' WHERE number_of_rows IS NULL OR TRIM(number_of_rows) = ''");

        if (Schema::hasColumn('pages', 'content_layout_columns')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('content_layout_columns');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'content_layout_columns')) {
                $table->unsignedTinyInteger('content_layout_columns')->nullable()->after('why_use_heading');
            }
        });

        if (Schema::hasColumn('pages', 'number_of_rows')) {
            DB::statement("UPDATE pages SET content_layout_columns = CASE SUBSTRING_INDEX(number_of_rows, ',', 1) WHEN '1' THEN 1 WHEN '2' THEN 2 WHEN '3' THEN 3 ELSE 1 END");

            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('number_of_rows');
            });
        }
    }
};
