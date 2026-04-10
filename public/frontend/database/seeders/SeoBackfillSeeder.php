<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeoBackfillSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $today = $now->toDateString();
        $baseUrl = rtrim((string) config('app.url', ''), '/');

        $urlsByPage = DB::table('urls')
            ->where('is_active', true)
            ->orderBy('id')
            ->get(['page_id', 'group_slug', 'slug'])
            ->groupBy('page_id');

        $rowsToInsert = [];

        $pages = DB::table('pages')
            ->orderBy('id')
            ->get(['id', 'name', 'head_title', 'quote_description']);

        foreach ($pages as $page) {
            $hasSeo = DB::table('seo')->where('page_id', (int) $page->id)->exists();
            if ($hasSeo) {
                continue;
            }

            $pageName = trim((string) ($page->name ?? 'A1 Airport Cars'));
            if ($pageName === '') {
                $pageName = 'A1 Airport Cars';
            }

            $headTitle = trim((string) ($page->head_title ?? ''));
            $metaTitle = $headTitle !== '' ? $headTitle : $pageName.' Transfers | A1 Airport Cars';

            $description = trim((string) ($page->quote_description ?? ''));
            if ($description === '') {
                $description = 'Book reliable '.$pageName.' transfers with fixed fares, professional drivers, and 24/7 support.';
            }

            $routePath = null;
            $urlRow = $urlsByPage->get($page->id)?->first();
            if ($urlRow && is_string($urlRow->group_slug) && is_string($urlRow->slug)) {
                $routePath = trim($urlRow->group_slug.'/'.$urlRow->slug, '/');
            }

            $canonical = $baseUrl !== ''
                ? ($routePath ? $baseUrl.'/'.$routePath : $baseUrl.'/')
                : ($routePath ? '/'.$routePath : '/');

            $schemaScript = json_encode([
                '@context' => 'https://schema.org',
                '@type' => 'Service',
                'name' => $pageName.' Transfers',
                'provider' => [
                    '@type' => 'Organization',
                    'name' => 'A1 Airport Cars',
                    'url' => $baseUrl !== '' ? $baseUrl : null,
                ],
            ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            $rowsToInsert[] = [
                'page_id' => (int) $page->id,
                'route_path' => $routePath,
                'meta_title' => $metaTitle,
                'meta_description' => $description,
                'meta_keywords' => $pageName.', airport transfers, city transfers, taxi service, A1 Airport Cars',
                'canonical' => $canonical,
                'schema_script' => $schemaScript,
                'robots' => 'index,follow',
                'og_title' => $metaTitle,
                'og_description' => $description,
                'date' => $today,
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!empty($rowsToInsert)) {
            DB::table('seo')->insert($rowsToInsert);
        }
    }
}
