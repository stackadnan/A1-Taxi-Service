<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (Schema::hasColumn('pages', 'slug_id')) {
                $table->dropColumn('slug_id');
            }

            if (Schema::hasColumn('pages', 'category')) {
                $table->dropColumn('category');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'slug_id')) {
                $table->unsignedBigInteger('slug_id')->nullable()->after('id');
                $table->index('slug_id');
            }

            if (!Schema::hasColumn('pages', 'category')) {
                $table->string('category')->default('airport')->after('name');
                $table->index('category');
            }
        });
    }
};
