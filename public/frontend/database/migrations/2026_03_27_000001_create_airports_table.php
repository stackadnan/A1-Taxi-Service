<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('head_title')->nullable();
            $table->string('quote_title')->nullable();
            $table->string('quote_subtitle')->nullable();
            $table->text('quote_description')->nullable();
            $table->string('why_us_title')->nullable();
            $table->string('why_us_heading')->nullable();
            $table->string('main_title');
            $table->text('main_description');
            $table->string('left_title');
            $table->text('left_description');
            $table->string('right_title');
            $table->text('right_description');
            $table->string('bottom_title');
            $table->text('bottom_description');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
