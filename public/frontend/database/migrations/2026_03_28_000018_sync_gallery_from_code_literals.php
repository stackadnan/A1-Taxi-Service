<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('gallery')) {
            return;
        }

        $paths = $this->collectAssetImagePaths();
        if (empty($paths)) {
            return;
        }

        $hasSourcePath = Schema::hasColumn('gallery', 'source_path');
        $today = now()->toDateString();
        $now = now();
        $rows = [];

        foreach ($paths as $path) {
            $row = [
                'image_path' => $path,
                'alt' => null,
                'image_date' => $today,
                'meta' => json_encode([
                    'source' => 'code-literal-scan',
                    'type' => 'img',
                ]),
                'is_active' => true,
                'updated_at' => $now,
                'created_at' => $now,
            ];

            if ($hasSourcePath) {
                $row['source_path'] = $path;
            }

            $rows[] = $row;
        }

        if ($hasSourcePath) {
            DB::table('gallery')->upsert(
                $rows,
                ['image_path'],
                ['source_path', 'is_active', 'updated_at']
            );

            DB::statement("UPDATE gallery SET source_path = image_path WHERE source_path IS NULL OR source_path = ''");

            return;
        }

        DB::table('gallery')->upsert(
            $rows,
            ['image_path'],
            ['is_active', 'updated_at']
        );
    }

    public function down(): void
    {
        // No destructive rollback because these rows may have been edited by users.
    }

    private function collectAssetImagePaths(): array
    {
        $scanRoots = [
            resource_path('views'),
            app_path(),
        ];

        $found = [];

        foreach ($scanRoots as $root) {
            if (!File::exists($root)) {
                continue;
            }

            foreach (File::allFiles($root) as $file) {
                $pathName = $file->getPathname();
                if (!str_ends_with($pathName, '.php') && !str_ends_with($pathName, '.blade.php')) {
                    continue;
                }

                $content = File::get($pathName);
                if (!is_string($content) || $content === '') {
                    continue;
                }

                preg_match_all('/assets\\/img\\/[A-Za-z0-9_\\/\.\-]+/i', $content, $matches);
                foreach (($matches[0] ?? []) as $raw) {
                    $normalized = $this->normalizeImagePath((string) $raw);
                    if ($normalized !== null) {
                        $found[$normalized] = true;
                    }
                }
            }
        }

        return array_keys($found);
    }

    private function normalizeImagePath(string $rawPath): ?string
    {
        $path = trim($rawPath);
        if ($path === '') {
            return null;
        }

        $lower = strtolower($path);
        if (str_starts_with($lower, 'http://') || str_starts_with($lower, 'https://') || str_starts_with($lower, 'data:') || str_starts_with($path, '#')) {
            return null;
        }

        $path = str_replace('\\\\', '/', $path);
        $path = preg_replace('/[?#].*$/', '', $path) ?? $path;
        $path = preg_replace('/^\.\//', '', $path) ?? $path;

        $assetsPos = stripos($path, '/assets/');
        if ($assetsPos !== false) {
            $path = substr($path, $assetsPos + 1);
        }

        $path = ltrim($path, '/');
        if ($path === '') {
            return null;
        }

        return $path;
    }
};
