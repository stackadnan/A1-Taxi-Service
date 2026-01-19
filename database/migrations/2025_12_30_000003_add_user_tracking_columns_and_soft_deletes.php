<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'username')) {
                $table->string('username')->nullable()->after('email');
            }
            if (! Schema::hasColumn('users', 'idle_timeout')) {
                $table->unsignedInteger('idle_timeout')->nullable()->after('username');
            }
            if (! Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('idle_timeout');
            }
            if (! Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable()->after('last_login_at');
            }
            if (! Schema::hasColumn('users', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'username')) $table->dropColumn('username');
            if (Schema::hasColumn('users', 'idle_timeout')) $table->dropColumn('idle_timeout');
            if (Schema::hasColumn('users', 'last_login_at')) $table->dropColumn('last_login_at');
            if (Schema::hasColumn('users', 'last_login_ip')) $table->dropColumn('last_login_ip');
            if (Schema::hasColumn('users', 'deleted_at')) $table->dropSoftDeletes();
        });
    }
};