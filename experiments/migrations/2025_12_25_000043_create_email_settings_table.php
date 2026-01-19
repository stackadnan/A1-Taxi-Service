<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_settings', function (Blueprint $table) {
            $table->id();
            $table->string('provider')->nullable();
            $table->string('smtp_host')->nullable();
            $table->unsignedInteger('smtp_port')->nullable();
            $table->string('username')->nullable();
            $table->string('from_email')->nullable();
            $table->string('encryption')->nullable();
            $table->string('status')->default('active');
            $table->timestamps();

            $table->index(['provider']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_settings');
    }
};