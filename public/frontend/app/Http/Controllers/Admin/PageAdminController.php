<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PagePartial;
use App\Models\Url;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PageAdminController extends Controller
{
    private array $partialKeys = [
        'head',
        'preloader',
        'scroll_up',
        'offcanvas',
        'header',
        'breadcrumb',
        'quotes',
        'testimonials',
        'why_us',
        'card_fleet',
        'steps',
        'card_blog',
        'faq',
        'footer',
        'script',
    ];

    public function index(): View
    {
        $pages = Page::with(['urls' => function ($query) {
            $query->orderByDesc('is_active')->orderBy('group_slug')->orderBy('slug');
        }])->orderBy('name')->paginate(25);

        return view('admin.pages.index', [
            'pages' => $pages,
        ]);
    }

    public function create(): View
    {
        $page = new Page([
            'number_of_rows' => '1,2,1',
        ]);

        return view('admin.pages.form', [
            'page' => $page,
            'rowBlocks' => $this->buildInitialRowBlocks($page),
            'partialToggles' => $this->defaultPartials(),
            'primaryUrl' => null,
            'formAction' => route('admin.pages.store'),
            'formMethod' => 'POST',
            'formTitle' => 'Create Page',
            'submitLabel' => 'Create Page',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateRequest($request);
        $rowPayload = $this->buildRowPayload($validated);

        return DB::transaction(function () use ($validated, $rowPayload) {
            $page = Page::create([
                'name' => $validated['name'],
                'head_title' => $validated['head_title'] ?? null,
                'quote_title' => $validated['quote_title'] ?? null,
                'quote_subtitle' => $validated['quote_subtitle'] ?? null,
                'quote_description' => $validated['quote_description'] ?? null,
                'why_us_title' => $validated['why_us_title'] ?? null,
                'why_us_heading' => $validated['why_us_heading'] ?? null,
                'why_use_heading' => $validated['why_use_heading'] ?? null,
                'number_of_rows' => $rowPayload['number_of_rows'],
                'one_column' => $rowPayload['one_column'],
                'two_column' => $rowPayload['two_column'],
                'three_column' => $rowPayload['three_column'],
                'row_blocks' => $rowPayload['row_blocks'],
            ]);

            $this->syncPartials($page, $validated);

            $this->syncPrimaryUrl($page, $validated);

            return redirect()
                ->route('admin.pages.edit', $page)
                ->with('status', 'Page created successfully.');
        });
    }

    public function edit(Page $page): View
    {
        $page->load(['urls' => function ($query) {
            $query->orderByDesc('is_active')->orderBy('group_slug')->orderBy('slug');
        }]);

        return view('admin.pages.form', [
            'page' => $page,
            'rowBlocks' => $this->buildInitialRowBlocks($page),
            'partialToggles' => $this->resolvePagePartials($page),
            'primaryUrl' => $page->urls->first(),
            'formAction' => route('admin.pages.update', $page),
            'formMethod' => 'PUT',
            'formTitle' => 'Edit Page',
            'submitLabel' => 'Update Page',
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $validated = $this->validateRequest($request, $page->id);
        $rowPayload = $this->buildRowPayload($validated, $page);

        return DB::transaction(function () use ($page, $validated, $rowPayload) {
            $page->update([
                'name' => $validated['name'],
                'head_title' => $validated['head_title'] ?? null,
                'quote_title' => $validated['quote_title'] ?? null,
                'quote_subtitle' => $validated['quote_subtitle'] ?? null,
                'quote_description' => $validated['quote_description'] ?? null,
                'why_us_title' => $validated['why_us_title'] ?? null,
                'why_us_heading' => $validated['why_us_heading'] ?? null,
                'why_use_heading' => $validated['why_use_heading'] ?? null,
                'number_of_rows' => $rowPayload['number_of_rows'],
                'one_column' => $rowPayload['one_column'],
                'two_column' => $rowPayload['two_column'],
                'three_column' => $rowPayload['three_column'],
                'row_blocks' => $rowPayload['row_blocks'],
            ]);

            $this->syncPartials($page, $validated);

            $this->syncPrimaryUrl($page, $validated);

            return redirect()
                ->route('admin.pages.edit', $page)
                ->with('status', 'Page updated successfully.');
        });
    }

    public function destroy(Page $page): RedirectResponse
    {
        DB::transaction(function () use ($page) {
            Url::where('page_id', $page->id)->delete();
            $page->delete();
        });

        return redirect()
            ->route('admin.pages.index')
            ->with('status', 'Page deleted successfully.');
    }

    private function validateRequest(Request $request, ?int $pageId = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'head_title' => ['nullable', 'string', 'max:255'],
            'quote_title' => ['nullable', 'string', 'max:255'],
            'quote_subtitle' => ['nullable', 'string', 'max:255'],
            'quote_description' => ['nullable', 'string'],
            'why_us_title' => ['nullable', 'string', 'max:255'],
            'why_us_heading' => ['nullable', 'string', 'max:255'],
            'why_use_heading' => ['nullable', 'string', 'max:255'],
            'number_of_rows' => ['nullable', 'string', 'regex:/^[123](\s*,\s*[123])*$/'],
            'one_column' => ['nullable', 'string'],
            'two_column' => ['nullable', 'string'],
            'three_column' => ['nullable', 'string'],
            'row_blocks_json' => ['nullable', 'string'],
            'partials' => ['nullable', 'array'],
            'partials.*' => ['nullable', 'boolean'],
            'url_id' => ['nullable', 'integer', 'exists:urls,id'],
            'group_slug' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/'],
            'slug' => ['nullable', 'string', 'max:150', 'regex:/^[a-z0-9-]+$/'],
            'url_is_active' => ['nullable', 'boolean'],
        ]);

        $groupSlug = isset($validated['group_slug']) ? trim((string) $validated['group_slug']) : '';
        $slug = isset($validated['slug']) ? trim((string) $validated['slug']) : '';

        if (($groupSlug === '') xor ($slug === '')) {
            throw ValidationException::withMessages([
                'group_slug' => 'Both group slug and page slug are required when URL mapping is provided.',
                'slug' => 'Both group slug and page slug are required when URL mapping is provided.',
            ]);
        }

        if ($groupSlug !== '' && $slug !== '') {
            $existingUrl = Url::query()
                ->where('group_slug', $groupSlug)
                ->where('slug', $slug)
                ->when($pageId, function ($query) use ($pageId) {
                    $query->where('page_id', '!=', $pageId);
                })
                ->exists();

            if ($existingUrl) {
                throw ValidationException::withMessages([
                    'slug' => 'This URL already exists. Please use a different group or slug.',
                ]);
            }
        }

        $validated['group_slug'] = $groupSlug;
        $validated['slug'] = $slug;

        return $validated;
    }

    private function defaultPartials(): array
    {
        $defaults = [];
        foreach ($this->partialKeys as $key) {
            $defaults[$key] = true;
        }

        return $defaults;
    }

    private function resolvePagePartials(Page $page): array
    {
        $defaults = $this->defaultPartials();

        $partialConfig = PagePartial::where('page_id', $page->id)->first();
        if (!$partialConfig) {
            return $defaults;
        }

        foreach ($this->partialKeys as $key) {
            $defaults[$key] = (bool) $partialConfig->{$key};
        }

        return $defaults;
    }

    private function syncPartials(Page $page, array $validated): void
    {
        $payload = $this->defaultPartials();
        $input = is_array($validated['partials'] ?? null) ? $validated['partials'] : [];

        foreach ($this->partialKeys as $key) {
            if (array_key_exists($key, $input)) {
                $payload[$key] = (bool) $input[$key];
            }
        }

        PagePartial::updateOrCreate(
            ['page_id' => $page->id],
            $payload
        );
    }

    private function buildInitialRowBlocks(Page $page): array
    {
        if (is_array($page->row_blocks) && $page->row_blocks !== []) {
            $blocks = [];

            foreach ($page->row_blocks as $rowBlock) {
                if (!is_array($rowBlock)) {
                    continue;
                }

                $layout = isset($rowBlock['layout']) ? (int) $rowBlock['layout'] : 0;
                if (!in_array($layout, [1, 2, 3], true)) {
                    continue;
                }

                $html = isset($rowBlock['html']) && is_string($rowBlock['html'])
                    ? $rowBlock['html']
                    : '';

                $blocks[] = [
                    'layout' => $layout,
                    'html' => $html,
                ];
            }

            if ($blocks !== []) {
                return $blocks;
            }
        }

        $numberOfRows = is_string($page->number_of_rows) ? trim($page->number_of_rows) : '';
        if ($numberOfRows === '') {
            $numberOfRows = '1,2,1';
        }

        $templates = [
            1 => is_string($page->one_column) ? $page->one_column : '',
            2 => is_string($page->two_column) ? $page->two_column : '',
            3 => is_string($page->three_column) ? $page->three_column : '',
        ];

        $tokens = preg_split('/\s*,\s*|\s+/', $numberOfRows) ?: [];
        $blocks = [];
        foreach ($tokens as $token) {
            if (!in_array($token, ['1', '2', '3'], true)) {
                continue;
            }

            $layout = (int) $token;
            $blocks[] = [
                'layout' => $layout,
                'html' => $templates[$layout] ?? '',
            ];
        }

        return $blocks === [] ? [['layout' => 1, 'html' => '']] : $blocks;
    }

    private function buildRowPayload(array $validated, ?Page $existingPage = null): array
    {
        $rowBlocks = $this->parseRowBlocksJson($validated['row_blocks_json'] ?? null);

        if ($rowBlocks === []) {
            $fallbackPage = $existingPage ?? new Page([
                'number_of_rows' => $validated['number_of_rows'] ?? '1',
                'one_column' => $validated['one_column'] ?? '',
                'two_column' => $validated['two_column'] ?? '',
                'three_column' => $validated['three_column'] ?? '',
            ]);

            $rowBlocks = $this->buildInitialRowBlocks($fallbackPage);
        }

        if ($rowBlocks === []) {
            throw ValidationException::withMessages([
                'row_blocks_json' => 'Please add at least one row block.',
            ]);
        }

        $numberOfRowsParts = [];
        $firstTemplateByLayout = [1 => null, 2 => null, 3 => null];

        foreach ($rowBlocks as $rowBlock) {
            $layout = (int) $rowBlock['layout'];
            $html = (string) $rowBlock['html'];

            $numberOfRowsParts[] = (string) $layout;

            if ($firstTemplateByLayout[$layout] === null && trim($html) !== '') {
                $firstTemplateByLayout[$layout] = $html;
            }
        }

        return [
            'row_blocks' => $rowBlocks,
            'number_of_rows' => implode(',', $numberOfRowsParts),
            'one_column' => $firstTemplateByLayout[1] ?? ($validated['one_column'] ?? ($existingPage?->one_column ?? '')),
            'two_column' => $firstTemplateByLayout[2] ?? ($validated['two_column'] ?? ($existingPage?->two_column ?? '')),
            'three_column' => $firstTemplateByLayout[3] ?? ($validated['three_column'] ?? ($existingPage?->three_column ?? '')),
        ];
    }

    private function parseRowBlocksJson(?string $json): array
    {
        if (!is_string($json) || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        if (!is_array($decoded)) {
            throw ValidationException::withMessages([
                'row_blocks_json' => 'Row configuration is invalid JSON.',
            ]);
        }

        $rowBlocks = [];
        foreach ($decoded as $index => $rowBlock) {
            $rowNumber = $index + 1;

            if (!is_array($rowBlock)) {
                throw ValidationException::withMessages([
                    'row_blocks_json' => "Row {$rowNumber} format is invalid.",
                ]);
            }

            $layout = isset($rowBlock['layout']) ? (int) $rowBlock['layout'] : 0;
            if (!in_array($layout, [1, 2, 3], true)) {
                throw ValidationException::withMessages([
                    'row_blocks_json' => "Row {$rowNumber} layout must be one of: 1, 2, 3.",
                ]);
            }

            $html = isset($rowBlock['html']) && is_string($rowBlock['html'])
                ? trim($rowBlock['html'])
                : '';

            if ($html === '') {
                throw ValidationException::withMessages([
                    'row_blocks_json' => "Row {$rowNumber} HTML content is required.",
                ]);
            }

            $rowBlocks[] = [
                'layout' => $layout,
                'html' => $html,
            ];
        }

        return array_slice($rowBlocks, 0, 50);
    }

    private function syncPrimaryUrl(Page $page, array $validated): void
    {
        $groupSlug = $validated['group_slug'] ?? '';
        $slug = $validated['slug'] ?? '';
        $urlId = $validated['url_id'] ?? null;

        if ($groupSlug === '' || $slug === '') {
            if ($urlId) {
                Url::where('id', (int) $urlId)
                    ->where('page_id', $page->id)
                    ->delete();
            }

            return;
        }

        $payload = [
            'page_id' => $page->id,
            'group_slug' => $groupSlug,
            'slug' => $slug,
            'date' => now()->toDateString(),
            'is_active' => (bool) ($validated['url_is_active'] ?? false),
            'meta' => ['source' => 'admin'],
        ];

        if ($urlId) {
            Url::where('id', (int) $urlId)
                ->where('page_id', $page->id)
                ->update($payload);

            return;
        }

        Url::create($payload);
    }
}
