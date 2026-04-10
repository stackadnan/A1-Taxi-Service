<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
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

        if (!Schema::hasColumn('gallery', 'source_path')) {
            Schema::table('gallery', function (Blueprint $table) {
                $table->string('source_path')->nullable()->after('id');
                $table->index('source_path');
            });
        }

        DB::statement("UPDATE gallery SET source_path = image_path WHERE source_path IS NULL OR source_path = ''");

        $this->repairSingleManualPathChange();
    }

    public function down(): void
    {
        if (!Schema::hasTable('gallery') || !Schema::hasColumn('gallery', 'source_path')) {
            return;
        }

        Schema::table('gallery', function (Blueprint $table) {
            $table->dropIndex(['source_path']);
            $table->dropColumn('source_path');
        });
    }

    private function repairSingleManualPathChange(): void
    {
        $viewPaths = $this->collectViewImagePaths();
        if (empty($viewPaths)) {
            return;
        }

        $rows = DB::table('gallery')
            ->where('is_active', true)
            ->get(['id', 'source_path', 'image_path']);

        $knownSources = [];
        foreach ($rows as $row) {
            $sourcePath = $this->normalizeImagePath((string) ($row->source_path ?? ''));
            if ($sourcePath !== null) {
                $knownSources[$sourcePath] = true;
            }
        }

        $missing = [];
        foreach ($viewPaths as $viewPath) {
            if (!isset($knownSources[$viewPath])) {
                $missing[] = $viewPath;
            }
        }

        $extras = [];
        foreach ($rows as $row) {
            $sourcePath = $this->normalizeImagePath((string) ($row->source_path ?? ''));
            $renderPath = $this->normalizeImagePath((string) ($row->image_path ?? ''));

            if ($sourcePath === null) {
                continue;
            }

            $sourceInViews = in_array($sourcePath, $viewPaths, true);
            $renderInViews = $renderPath !== null && in_array($renderPath, $viewPaths, true);

            if (!$sourceInViews && !$renderInViews) {
                $extras[] = [
                    'id' => (int) $row->id,
                    'source_path' => $sourcePath,
                ];
            }
        }

        // Safe auto-repair for the common case: one original path was replaced manually.
        if (count($missing) === 1 && count($extras) === 1) {
            DB::table('gallery')
                ->where('id', $extras[0]['id'])
                ->update([
                    'source_path' => $missing[0],
                    'updated_at' => now(),
                ]);
        }
    }

    private function collectViewImagePaths(): array
    {
        $viewsPath = resource_path('views');
        if (!File::exists($viewsPath)) {
            return [];
        }

        $paths = [];
        foreach (File::allFiles($viewsPath) as $file) {
            $content = File::get($file->getPathname());
            if (!is_string($content) || $content === '') {
                continue;
            }

            preg_match_all('/<img\\b[^>]*\\bsrc\\s*=\\s*["\']([^"\']+)["\'][^>]*>/i', $content, $imgMatches);
            foreach (($imgMatches[1] ?? []) as $rawPath) {
                $normalized = $this->normalizeImagePath((string) $rawPath);
                if ($normalized !== null) {
                    $paths[$normalized] = true;
                }
            }

            preg_match_all('/background-image\\s*:\\s*url\\(\\s*["\']?([^"\')]+)["\']?\\s*\\)/i', $content, $bgMatches);
            foreach (($bgMatches[1] ?? []) as $rawPath) {
                $normalized = $this->normalizeImagePath((string) $rawPath);
                if ($normalized !== null) {
                    $paths[$normalized] = true;
                }
            }
        }

        return array_keys($paths);
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

        if (str_contains($path, '{{') || str_contains($path, '@')) {
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
