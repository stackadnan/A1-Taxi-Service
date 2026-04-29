<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use Illuminate\Http\Response;

class GalleryImageController extends Controller
{
    public function show(int $id): Response
    {
        $gallery = Gallery::query()
            ->where('id', $id)
            ->where('is_active', true)
            ->firstOrFail();

        $relativePath = $this->normalizePath($gallery->image_path);
        if ($relativePath === null) {
            abort(404);
        }

        $absolutePath = public_path($relativePath);
        if (!is_file($absolutePath)) {
            abort(404);
        }

        $mimeType = $this->detectMimeType($absolutePath);
        $content = file_get_contents($absolutePath);
        if ($content === false) {
            abort(404);
        }

        return response($content, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => (string) filesize($absolutePath),
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    private function normalizePath(?string $path): ?string
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
        if ($path === '' || str_contains($path, '..')) {
            return null;
        }

        return $path;
    }

    private function detectMimeType(string $absolutePath): string
    {
        $extension = strtolower((string) pathinfo($absolutePath, PATHINFO_EXTENSION));

        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'bmp' => 'image/bmp',
            'avif' => 'image/avif',
            default => 'application/octet-stream',
        };
    }
}
