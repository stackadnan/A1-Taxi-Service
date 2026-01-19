<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained('feedback_rules')->cascadeOnDelete();
            $table->string('email_subject')->nullable();
            $table->text('email_body')->nullable();
            $table->text('sms_body')->nullable();
            $table->timestamps();

            $table->index(['rule_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback_messages');
    }
};