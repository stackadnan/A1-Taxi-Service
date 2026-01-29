<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnavailableToDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (!Schema::hasColumn('drivers', 'unavailable_from')) {
                $table->timestamp('unavailable_from')->nullable()->after('status');
            }
            if (!Schema::hasColumn('drivers', 'unavailable_to')) {
                $table->timestamp('unavailable_to')->nullable()->after('unavailable_from');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            if (Schema::hasColumn('drivers', 'unavailable_to')) {
                $table->dropColumn('unavailable_to');
            }
            if (Schema::hasColumn('drivers', 'unavailable_from')) {
                $table->dropColumn('unavailable_from');
            }
        });
    }
}
