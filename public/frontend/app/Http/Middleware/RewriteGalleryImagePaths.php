<?php

namespace App\Http\Middleware;

use App\Models\Gallery;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RewriteGalleryImagePaths
{
    /**
     * @var array<string, string>|null
     */
    private static ?array $replacementMap = null;

    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (!$request->isMethod('GET') && !$request->isMethod('HEAD')) {
            return $response;
        }

        $contentType = (string) $response->headers->get('Content-Type', '');
        if (!str_contains($contentType, 'text/html')) {
            return $response;
        }

        $content = $response->getContent();
        if (!is_string($content) || $content === '') {
            return $response;
        }

        $map = $this->getReplacementMap();
        if (empty($map)) {
            return $response;
        }

        $updated = strtr($content, $map);
        if ($updated !== $content) {
            $response->setContent($updated);
        }

        return $response;
    }

    /**
     * @return array<string, string>
     */
    private function getReplacementMap(): array
    {
        if (self::$replacementMap !== null) {
            return self::$replacementMap;
        }

        if (!Schema::hasTable('gallery')) {
            self::$replacementMap = [];

            return self::$replacementMap;
        }

        $rows = Gallery::query()
            ->where('is_active', true)
            ->whereNotNull('source_path')
            ->get(['source_path', 'short_url', 'image_path']);

        $replacements = [];

        foreach ($rows as $row) {
            $source = $this->normalizePath(is_string($row->source_path) ? $row->source_path : null);
            $shortUrl = $this->normalizePath(is_string($row->short_url) ? $row->short_url : null);
            $imagePath = $this->normalizePath(is_string($row->image_path) ? $row->image_path : null);
            $target = $shortUrl ?? $imagePath;

            if ($source === null || $target === null || $source === $target) {
                continue;
            }

            $replacements[$source] = $target;
            $replacements['/'.$source] = '/'.$target;
            $replacements[str_replace('/', '\\/', $source)] = str_replace('/', '\\/', $target);
            $replacements[str_replace('/', '\\/', '/'.$source)] = str_replace('/', '\\/', '/'.$target);
        }

        self::$replacementMap = $replacements;

        return self::$replacementMap;
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

        return $path !== '' ? $path : null;
    }
}
