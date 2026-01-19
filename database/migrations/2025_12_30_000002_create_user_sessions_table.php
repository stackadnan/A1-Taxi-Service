<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('session_id')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->integer('last_activity_at')->nullable();
            $table->timestamp('expires_at')->nullable();
        });

        // Migrate from existing sessions table (Laravel default)
        if (Schema::hasTable('sessions')) {
            $rows = DB::table('sessions')->get();
            foreach ($rows as $r) {
                DB::table('user_sessions')->insert([
                    'user_id' => $r->user_id ?? null,
                    'session_id' => $r->id,
                    'ip_address' => $r->ip_address ?? null,
                    'last_activity_at' => $r->last_activity ?? null,
                    'expires_at' => null,
                ]);
            }

            Schema::dropIfExists('sessions');
        }
    }

    public function down(): void
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // try to move back basic fields
        $rows = DB::table('user_sessions')->get();
        foreach ($rows as $r) {
            DB::table('sessions')->insert([
                'id' => $r->session_id ?? (string) Str::uuid(),
                'user_id' => $r->user_id ?? null,
                'ip_address' => $r->ip_address ?? null,
                'user_agent' => null,
                'payload' => '',
                'last_activity' => $r->last_activity_at ?? 0,
            ]);
        }

        Schema::dropIfExists('user_sessions');
    }
};