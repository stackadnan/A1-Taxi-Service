<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pricing_postcode_charges', 'auto_generated')) {
            Schema::table('pricing_postcode_charges', function (Blueprint $table) {
                $table->boolean('auto_generated')->default(false)->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pricing_postcode_charges', 'auto_generated')) {
            Schema::table('pricing_postcode_charges', function (Blueprint $table) {
                $table->dropColumn('auto_generated');
            });
        }
    }
};