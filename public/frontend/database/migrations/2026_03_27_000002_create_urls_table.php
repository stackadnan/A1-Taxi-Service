<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('group_slug');
            $table->string('slug');
            $table->date('date')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['group_slug', 'slug']);
            $table->index('group_slug');
            $table->index('slug');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('urls');
    }
};
