<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gallery') || !Schema::hasColumn('gallery', 'short_url')) {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS gallery_set_short_url_after_insert');
        DB::unprepared('DROP TRIGGER IF EXISTS gallery_set_short_url_before_insert');

        DB::unprepared(
            "CREATE TRIGGER gallery_set_short_url_before_insert\n".
            "BEFORE INSERT ON gallery\n".
            "FOR EACH ROW\n".
            "SET NEW.short_url = CASE\n".
            "  WHEN NEW.short_url IS NULL OR NEW.short_url = '' THEN\n".
            "    CONCAT('i/', IF(NEW.id IS NULL OR NEW.id = 0, (SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'gallery'), NEW.id))\n".
            "  ELSE NEW.short_url\n".
            "END"
        );
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS gallery_set_short_url_before_insert');
    }
};
