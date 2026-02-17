<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('pricing_zones', 'pricing_mode')) {
            Schema::table('pricing_zones', function (Blueprint $table) {
                // drop the column if present
                $table->dropColumn('pricing_mode');
            });
        }
    }

    public function down(): void
    {
        Schema::table('pricing_zones', function (Blueprint $table) {
            $table->string('pricing_mode')->nullable();
        });
    }
};
