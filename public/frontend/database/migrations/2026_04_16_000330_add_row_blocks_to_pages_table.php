<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table) {
            if (!Schema::hasColumn('pages', 'row_blocks')) {
                $table->json('row_blocks')->nullable()->after('three_column');
            }
        });

        $pages = DB::table('pages')
            ->select(['id', 'number_of_rows', 'one_column', 'two_column', 'three_column'])
            ->get();

        foreach ($pages as $page) {
            $rowPatternRaw = is_string($page->number_of_rows) ? trim($page->number_of_rows) : '';
            if ($rowPatternRaw === '') {
                $rowPatternRaw = '1';
            }

            $tokens = preg_split('/\s*,\s*|\s+/', $rowPatternRaw) ?: [];
            $rowPattern = [];
            foreach ($tokens as $token) {
                if (in_array($token, ['1', '2', '3'], true)) {
                    $rowPattern[] = (int) $token;
                }
            }

            if ($rowPattern === []) {
                $rowPattern = [1];
            }

            $templateMap = [
                1 => is_string($page->one_column) ? trim($page->one_column) : '',
                2 => is_string($page->two_column) ? trim($page->two_column) : '',
                3 => is_string($page->three_column) ? trim($page->three_column) : '',
            ];

            $rowBlocks = [];
            foreach ($rowPattern as $layout) {
                $rowBlocks[] = [
                    'layout' => $layout,
                    'html' => $templateMap[$layout] ?? '',
                ];
            }

            DB::table('pages')
                ->where('id', $page->id)
                ->update([
                    'row_blocks' => json_encode($rowBlocks, JSON_UNESCAPED_UNICODE),
                ]);
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pages', 'row_blocks')) {
            Schema::table('pages', function (Blueprint $table) {
                $table->dropColumn('row_blocks');
            });
        }
    }
};
