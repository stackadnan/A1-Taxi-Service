<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seo', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('page_id')->nullable();
            $table->string('route_path')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->text('canonical')->nullable();
            $table->longText('schema_script')->nullable();
            $table->string('robots')->nullable();
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();
            $table->date('date')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('page_id');
            $table->index('route_path');
            $table->index(['page_id', 'date']);

            $table->foreign('page_id')
                ->references('id')
                ->on('pages')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seo');
    }
};
