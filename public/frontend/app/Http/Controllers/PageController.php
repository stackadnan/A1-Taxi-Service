<?php

namespace App\Http\Controllers;

use App\Models\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class PageController extends Controller
{
    private ?bool $hasUrlsTable = null;

    public function home(): Response
    {
        return response()->view(config('pages.home', 'index'));
    }

    public function show(string $slug): Response|RedirectResponse
    {
        $groupUrl = null;
        if ($this->hasUrlsTable()) {
            $groupUrl = Url::where('group_slug', strtolower($slug))
                ->where('is_active', true)
                ->orderBy('id')
                ->first();
        }

        if ($groupUrl) {
            return redirect()->to('/'.$groupUrl->group_slug.'/'.$groupUrl->slug, 302);
        }

        $view = $this->resolveView($slug);
        if ($view === null) {
            abort(404);
        }

        return response()->view($view);
    }

    public function showNested(string $groupSlug, string $slug): Response
    {
        if (! $this->hasUrlsTable()) {
            abort(404);
        }

        $url = Url::where('group_slug', strtolower($groupSlug))
            ->where('slug', strtolower($slug))
            ->where('is_active', true)
            ->first();

        if (!$url) {
            abort(404);
        }

        $view = $this->resolveNestedView($groupSlug);

        if (!is_string($view) || $view === '') {
            abort(404);
        }

        request()->attributes->set('url_page_id', $url->page_id);

        return response()->view($view, [
            'currentUrl' => $url,
            'nestedGroupSlug' => $groupSlug,
            'nestedSlug' => $slug,
        ]);
    }

    public function legacy(string $legacy): RedirectResponse
    {
        $slug = strtolower($legacy);
        $slug = config("pages.legacy_aliases.{$slug}", $slug);

        if ($slug === '') {
            return redirect()->route('home', status: 301);
        }

        if ($this->resolveView($slug) === null) {
            abort(404);
        }

        return redirect()->to('/' . $slug, 301);
    }

    private function resolveView(string $slug): ?string
    {
        $view = config("pages.views.{$slug}");
        if (is_string($view) && $view !== '') {
            return $view;
        }

        if (str_ends_with($slug, '-airport-taxi')) {
            return 'airport';
        }

        // City links are dynamic and share one landing template.
        if (str_ends_with($slug, '-taxi')) {
            return 'city-transfers';
        }

        return null;
    }

    private function resolveNestedView(string $groupSlug): ?string
    {
        return match (strtolower($groupSlug)) {
            'airport-transfers' => 'airport',
            'city-transfers', 'cruise-port-transfers' => 'airport',
            default => 'airport',
        };
    }

    private function hasUrlsTable(): bool
    {
        if ($this->hasUrlsTable !== null) {
            return $this->hasUrlsTable;
        }

        try {
            $this->hasUrlsTable = Schema::hasTable((new Url())->getTable());
        } catch (\Throwable $e) {
            $this->hasUrlsTable = false;
        }

        return $this->hasUrlsTable;
    }
}
