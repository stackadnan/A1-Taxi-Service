<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SeoAllPagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $today = $now->toDateString();
        $baseUrl = rtrim((string) config('app.url', ''), '/');

        $pageMap = DB::table('pages')
            ->get(['id', 'name', 'head_title', 'quote_description'])
            ->keyBy('id');

        $this->seedDynamicRoutes($pageMap, $baseUrl, $today, $now);
        $this->seedStaticRoutes($baseUrl, $today, $now);
        $this->seedGlobalDefault($baseUrl, $today, $now);
    }

    private function seedDynamicRoutes($pageMap, string $baseUrl, string $today, $now): void
    {
        $urls = DB::table('urls')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['page_id', 'group_slug', 'slug']);

        foreach ($urls as $url) {
            if (!is_numeric($url->page_id)) {
                continue;
            }

            $pageId = (int) $url->page_id;
            $routePath = trim((string) $url->group_slug.'/'.(string) $url->slug, '/');

            $exists = DB::table('seo')
                ->where('route_path', $routePath)
                ->orWhere('page_id', $pageId)
                ->exists();

            if ($exists) {
                continue;
            }

            $page = $pageMap->get($pageId);
            $pageName = trim((string) ($page->name ?? 'A1 Airport Cars'));
            if ($pageName === '') {
                $pageName = 'A1 Airport Cars';
            }

            $title = trim((string) ($page->head_title ?? ''));
            if ($title === '') {
                $title = $pageName.' Transfers | A1 Airport Cars';
            }

            $description = trim((string) ($page->quote_description ?? ''));
            if ($description === '') {
                $description = 'Book reliable '.$pageName.' transfers with fixed fares, professional drivers, and 24/7 support.';
            }

            DB::table('seo')->insert([
                'page_id' => $pageId,
                'route_path' => $routePath,
                'meta_title' => $title,
                'meta_description' => $description,
                'meta_keywords' => $pageName.', airport transfers, city transfers, taxi service, A1 Airport Cars',
                'canonical' => $this->canonical($baseUrl, $routePath),
                'schema_script' => $this->schemaService($baseUrl, $pageName),
                'robots' => 'index,follow',
                'og_title' => $title,
                'og_description' => $description,
                'date' => $today,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedStaticRoutes(string $baseUrl, string $today, $now): void
    {
        $configViews = config('pages.views', []);
        $slugs = array_keys(is_array($configViews) ? $configViews : []);
        $slugs[] = '';

        $seen = [];

        foreach ($slugs as $slug) {
            $routePath = trim((string) $slug, '/');
            if (isset($seen[$routePath])) {
                continue;
            }
            $seen[$routePath] = true;

            $exists = DB::table('seo')
                ->where('route_path', $routePath)
                ->exists();

            if ($exists) {
                continue;
            }

            $pageLabel = $routePath === ''
                ? 'Home'
                : Str::of($routePath)->replace('-', ' ')->title()->toString();

            $metaTitle = $routePath === ''
                ? 'A1 Airport Cars - Best option for Premium airport transfer services'
                : $pageLabel.' | A1 Airport Cars';

            $metaDescription = $routePath === ''
                ? 'A1 Airport Cars - Best option for Premium airport transfer services'
                : 'Book '.$pageLabel.' with fixed fares, professional drivers, and 24/7 support.';

            DB::table('seo')->insert([
                'page_id' => null,
                'route_path' => $routePath,
                'meta_title' => $metaTitle,
                'meta_description' => $metaDescription,
                'meta_keywords' => strtolower($pageLabel).', airport transfers, taxi service, A1 Airport Cars',
                'canonical' => $this->canonical($baseUrl, $routePath),
                'schema_script' => $this->schemaWebPage($baseUrl, $pageLabel, $routePath),
                'robots' => 'index,follow',
                'og_title' => $metaTitle,
                'og_description' => $metaDescription,
                'date' => $today,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function seedGlobalDefault(string $baseUrl, string $today, $now): void
    {
        $exists = DB::table('seo')
            ->whereNull('page_id')
            ->whereNull('route_path')
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('seo')->insert([
            'page_id' => null,
            'route_path' => null,
            'meta_title' => 'A1 Airport Cars - Best option for Premium airport transfer services',
            'meta_description' => 'A1 Airport Cars - Best option for Premium airport transfer services',
            'meta_keywords' => 'airport transfers, city transfers, taxi service, A1 Airport Cars',
            'canonical' => $this->canonical($baseUrl, ''),
            'schema_script' => $this->schemaWebPage($baseUrl, 'A1 Airport Cars', ''),
            'robots' => 'index,follow',
            'og_title' => 'A1 Airport Cars - Best option for Premium airport transfer services',
            'og_description' => 'A1 Airport Cars - Best option for Premium airport transfer services',
            'date' => $today,
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    private function canonical(string $baseUrl, string $routePath): string
    {
        if ($baseUrl === '') {
            return $routePath === '' ? '/' : '/'.$routePath;
        }

        return $routePath === '' ? $baseUrl.'/' : $baseUrl.'/'.$routePath;
    }

    private function schemaService(string $baseUrl, string $pageName): string
    {
        return (string) json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'name' => $pageName.' Transfers',
            'provider' => [
                '@type' => 'Organization',
                'name' => 'A1 Airport Cars',
                'url' => $baseUrl === '' ? null : $baseUrl,
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }

    private function schemaWebPage(string $baseUrl, string $pageLabel, string $routePath): string
    {
        return (string) json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'WebPage',
            'name' => $pageLabel,
            'url' => $this->canonical($baseUrl, $routePath),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}
