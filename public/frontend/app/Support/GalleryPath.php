<?php

namespace App\Support;

use App\Models\Gallery;
use Illuminate\Support\Facades\Schema;

class GalleryPath
{
    /**
     * @var array<string, string>|null
     */
    private static ?array $pathMap = null;

    public static function path(?string $sourcePath): string
    {
        $normalized = self::normalize($sourcePath);
        if ($normalized === null) {
            return '';
        }

        $map = self::map();

        if (preg_match('/^i\/[0-9]+$/', $normalized) === 1) {
            return $normalized;
        }

        return $map[$normalized] ?? '';
    }

    public static function asset(?string $sourcePath): string
    {
        $path = self::path($sourcePath);

        return $path === '' ? '' : asset($path);
    }

    /**
     * @return array<string, string>
     */
    private static function map(): array
    {
        if (self::$pathMap !== null) {
            return self::$pathMap;
        }

        if (!Schema::hasTable('gallery')) {
            self::$pathMap = [];

            return self::$pathMap;
        }

        $rows = Gallery::query()
            ->where('is_active', true)
            ->get(['source_path', 'short_url', 'image_path']);

        $map = [];

        foreach ($rows as $row) {
            $source = self::normalize(is_string($row->source_path) ? $row->source_path : null);
            $shortUrl = self::normalize(is_string($row->short_url) ? $row->short_url : null);
            $imagePath = self::normalize(is_string($row->image_path) ? $row->image_path : null);
            $target = $shortUrl ?? $imagePath;

            if ($source === null || $target === null) {
                continue;
            }

            $map[$source] = $target;
        }

        self::$pathMap = $map;

        return self::$pathMap;
    }

    private static function normalize(?string $path): ?string
    {
        if (!is_string($path)) {
            return null;
        }

        $path = trim($path);
        if ($path === '') {
            return null;
        }

        $path = preg_replace('/[?#].*$/', '', $path) ?? $path;
        $path = str_replace('\\\\', '/', $path);

        $assetsPos = stripos($path, '/assets/');
        if ($assetsPos !== false) {
            $path = substr($path, $assetsPos + 1);
        }

        $path = ltrim($path, '/');

        return $path === '' ? null : $path;
    }
}
