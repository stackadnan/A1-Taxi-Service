<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('token');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('used_at')->nullable();
            $table->timestamp('created_at')->nullable();
        });

        // Migrate data from old password_reset_tokens (if present)
        if (Schema::hasTable('password_reset_tokens')) {
            $rows = DB::table('password_reset_tokens')->get();
            foreach ($rows as $r) {
                $user = DB::table('users')->where('email', $r->email)->first();
                DB::table('password_resets')->insert([
                    'user_id' => $user->id ?? null,
                    'token' => $r->token,
                    'created_at' => $r->created_at ?? now(),
                    'expires_at' => null,
                    'used_at' => null,
                ]);
            }

            Schema::dropIfExists('password_reset_tokens');
        }
    }

    public function down(): void
    {
        // recreate old table (best-effort)
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // move back data (try to map user_id -> email when possible)
        $rows = DB::table('password_resets')->get();
        foreach ($rows as $r) {
            $email = null;
            if ($r->user_id) {
                $u = DB::table('users')->where('id', $r->user_id)->first();
                $email = $u->email ?? null;
            }
            if ($email) {
                DB::table('password_reset_tokens')->insert([
                    'email' => $email,
                    'token' => $r->token,
                    'created_at' => $r->created_at ?? now(),
                ]);
            }
        }

        Schema::dropIfExists('password_resets');
    }
};