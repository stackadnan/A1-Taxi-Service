<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pricing_addon_charges', function (Blueprint $table) {
            $table->decimal('pickup_price', 10, 2)->default(0)->after('charge_value');
            $table->decimal('dropoff_price', 10, 2)->default(0)->after('pickup_price');
        });
    }

    public function down(): void
    {
        Schema::table('pricing_addon_charges', function (Blueprint $table) {
            $table->dropColumn(['pickup_price', 'dropoff_price']);
        });
    }
};
