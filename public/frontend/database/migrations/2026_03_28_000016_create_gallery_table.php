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
        Schema::create('gallery', function (Blueprint $table) {
            $table->id();
            $table->string('image_path')->unique();
            $table->string('alt')->nullable();
            $table->date('image_date')->nullable();
            $table->json('meta')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
            $table->index('image_date');
        });

        $this->syncFromBladeViews();
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery');
    }

    private function syncFromBladeViews(): void
    {
        $viewsPath = resource_path('views');
        if (!File::exists($viewsPath)) {
            return;
        }

        $files = File::allFiles($viewsPath);
        $byPath = [];

        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            if (!is_string($content) || $content === '') {
                continue;
            }

            preg_match_all('/<img\\b[^>]*\\bsrc\\s*=\\s*["\']([^"\']+)["\'][^>]*>/i', $content, $imgMatches, PREG_SET_ORDER);
            foreach ($imgMatches as $match) {
                $tag = $match[0] ?? '';
                $rawPath = $match[1] ?? '';
                $path = $this->normalizeImagePath($rawPath);
                if ($path === null) {
                    continue;
                }

                $alt = null;
                if (preg_match('/\\balt\\s*=\\s*["\']([^"\']*)["\']/i', $tag, $altMatch)) {
                    $alt = trim((string) ($altMatch[1] ?? ''));
                }

                if (!array_key_exists($path, $byPath)) {
                    $byPath[$path] = [
                        'alt' => $alt !== '' ? $alt : null,
                        'type' => 'img',
                    ];
                } elseif (($byPath[$path]['alt'] ?? null) === null && $alt !== '') {
                    $byPath[$path]['alt'] = $alt;
                }
            }

            preg_match_all('/background-image\\s*:\\s*url\\(\\s*["\']?([^"\')]+)["\']?\\s*\\)/i', $content, $bgMatches);
            foreach (($bgMatches[1] ?? []) as $rawPath) {
                $path = $this->normalizeImagePath((string) $rawPath);
                if ($path === null) {
                    continue;
                }

                if (!array_key_exists($path, $byPath)) {
                    $byPath[$path] = [
                        'alt' => null,
                        'type' => 'background',
                    ];
                }
            }
        }

        if (empty($byPath)) {
            return;
        }

        $today = now()->toDateString();
        $now = now();
        $rows = [];

        foreach ($byPath as $path => $info) {
            $rows[] = [
                'image_path' => $path,
                'alt' => $info['alt'] ?? null,
                'image_date' => $today,
                'meta' => json_encode([
                    'source' => 'view-scan',
                    'type' => $info['type'] ?? 'img',
                ]),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        DB::table('gallery')->upsert(
            $rows,
            ['image_path'],
            ['alt', 'image_date', 'meta', 'is_active', 'updated_at']
        );
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
