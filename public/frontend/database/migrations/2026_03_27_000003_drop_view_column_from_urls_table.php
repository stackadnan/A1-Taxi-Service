<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('urls') && Schema::hasColumn('urls', 'view')) {
            Schema::table('urls', function (Blueprint $table) {
                $table->dropColumn('view');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('urls') && !Schema::hasColumn('urls', 'view')) {
            Schema::table('urls', function (Blueprint $table) {
                $table->string('view')->default('airport')->after('slug');
            });
        }
    }
};
