<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('breadcrumbs', function (Blueprint $table) {
            $table->id();
            $table->string('page_key')->unique();
            $table->string('img')->nullable();
            $table->string('title')->default('Home');
            $table->string('title2')->default('');
            $table->string('subtitle')->nullable();
            $table->integer('order')->default(0)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('breadcrumbs');
    }
};
