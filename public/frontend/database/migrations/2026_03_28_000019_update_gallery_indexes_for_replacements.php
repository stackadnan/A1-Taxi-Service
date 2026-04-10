<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gallery')) {
            return;
        }

        if ($this->indexExists('gallery', 'gallery_image_path_unique')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->dropUnique('gallery_image_path_unique');
            });
        }

        if (!$this->indexExists('gallery', 'gallery_image_path_index')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->index('image_path');
            });
        }

        if (Schema::hasColumn('gallery', 'source_path') && !$this->indexExists('gallery', 'gallery_source_path_unique')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->unique('source_path');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('gallery')) {
            return;
        }

        if ($this->indexExists('gallery', 'gallery_source_path_unique')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->dropUnique('gallery_source_path_unique');
            });
        }

        if ($this->indexExists('gallery', 'gallery_image_path_index')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->dropIndex('gallery_image_path_index');
            });
        }

        if (!$this->indexExists('gallery', 'gallery_image_path_unique')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->unique('image_path');
            });
        }
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select('SHOW INDEX FROM '.$table.' WHERE Key_name = ?', [$indexName]);

        return !empty($result);
    }
};
