<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('headers', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('top_email')->nullable();
            $table->string('top_address')->nullable();
            $table->json('top_links')->nullable();
            $table->json('social_links')->nullable();
            $table->string('logo_light')->nullable();
            $table->string('logo_dark')->nullable();
            $table->string('phone_label')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('button_text')->nullable();
            $table->string('button_link')->nullable();
            $table->json('airport_links')->nullable();
            $table->json('city_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('headers');
    }
};
