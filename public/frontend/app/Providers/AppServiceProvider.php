<?php

namespace App\Providers;

use App\Models\Breadcrumb;
use App\Models\CardBlog;
use App\Models\CardFleet;
use App\Models\FeatureBenefit;
use App\Models\FaqItem;
use App\Models\Footer;
use App\Models\Gallery;
use App\Models\Header;
use App\Models\Offcanvas;
use App\Models\Page;
use App\Models\PagePartial;
use App\Models\QuoteSection;
use App\Models\Seo;
use App\Models\StepItem;
use App\Models\Testimonial;
use App\Models\Url;
use App\Models\WhyUs;
use App\Support\GalleryPath;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('*', function ($view) {
            static $galleryImageIdMap = null;
            static $galleryImagePathMap = null;

            $normalizePath = static function (?string $path): ?string {
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
            };

            if ($galleryImageIdMap === null || $galleryImagePathMap === null) {
                if (Schema::hasTable('gallery')) {
                    $galleryRows = Gallery::where('is_active', true)
                        ->get(['id', 'source_path', 'short_url', 'image_path', 'meta']);

                    $galleryImageIdMap = [];
                    $galleryImagePathMap = [];

                    foreach ($galleryRows as $row) {
                        $sourcePath = $normalizePath($row->source_path);
                        $shortUrl = $normalizePath($row->short_url);
                        $imagePath = $normalizePath($row->image_path);
                        $renderPath = $shortUrl ?? $imagePath;

                        if ($sourcePath === null && $renderPath === null) {
                            continue;
                        }

                        if ($sourcePath === null) {
                            $sourcePath = $renderPath;
                        }

                        if ($renderPath === null) {
                            $renderPath = $sourcePath;
                        }

                        $galleryImageIdMap[$sourcePath] = (int) $row->id;
                        $galleryImagePathMap[$sourcePath] = $renderPath;

                        if (!isset($galleryImageIdMap[$renderPath])) {
                            $galleryImageIdMap[$renderPath] = (int) $row->id;
                        }

                        if (!isset($galleryImagePathMap[$renderPath])) {
                            $galleryImagePathMap[$renderPath] = $renderPath;
                        }

                        $aliases = is_array($row->meta['aliases'] ?? null) ? $row->meta['aliases'] : [];
                        foreach ($aliases as $aliasPath) {
                            $aliasPath = $normalizePath(is_string($aliasPath) ? $aliasPath : null);
                            if ($aliasPath === null) {
                                continue;
                            }

                            $galleryImageIdMap[$aliasPath] = (int) $row->id;
                            $galleryImagePathMap[$aliasPath] = $renderPath;
                        }
                    }
                } else {
                    $galleryImageIdMap = [];
                    $galleryImagePathMap = [];
                }
            }

            $view->with('galleryImageIdMap', $galleryImageIdMap);
            $view->with('galleryImagePathMap', $galleryImagePathMap);
        });

        View::composer('partials.head', function ($view) {
            $seo = null;

            if (Schema::hasTable('seo')) {
                $pageId = request()->attributes->get('url_page_id');
                $requestPath = trim((string) request()->path(), '/');
                $routeCandidates = $requestPath === '' ? ['', '/'] : [$requestPath, '/'.$requestPath];

                $baseQuery = Seo::query()
                    ->where('is_active', true)
                    ->orderByDesc('date')
                    ->orderByDesc('id');

                $seo = (clone $baseQuery)
                    ->whereIn('route_path', $routeCandidates)
                    ->first();

                if (!$seo && is_numeric($pageId)) {
                    $seo = (clone $baseQuery)
                        ->where('page_id', (int) $pageId)
                        ->first();
                }

                if (!$seo && is_numeric($pageId)) {
                    $seo = (clone $baseQuery)
                        ->whereNull('route_path')
                        ->where('page_id', (int) $pageId)
                        ->first();
                }

                if (!$seo) {
                    $seo = (clone $baseQuery)
                        ->whereNull('page_id')
                        ->whereNull('route_path')
                        ->first();
                }
            }

            $view->with([
                'seoMetaTitle' => $seo?->meta_title,
                'seoMetaDescription' => $seo?->meta_description,
                'seoMetaKeywords' => $seo?->meta_keywords,
                'seoCanonical' => $seo?->canonical,
                'seoSchemaScript' => $seo?->schema_script,
                'seoRobots' => $seo?->robots,
                'seoOgTitle' => $seo?->og_title,
                'seoOgDescription' => $seo?->og_description,
                'seoOgImage' => $seo?->og_image,
            ]);
        });

        View::composer('partials.card-fleet', function ($view) {
            $fleetItems = CardFleet::orderBy('order')->get();

            $view->with('fleetItems', $fleetItems);
        });

        View::composer('partials.card-blog', function ($view) {
            $blogItems = CardBlog::orderBy('order')->get();

            $view->with('blogItems', $blogItems);
        });

        View::composer('partials.quotes', function ($view) {
            $quote = QuoteSection::where('section_key', 'quote')->first();
            $page = null;
            $pageId = request()->attributes->get('url_page_id');

            if (is_numeric($pageId)) {
                $page = Page::find((int) $pageId);
            }

            $heroTitle = null;
            $heroSubtitle = null;
            $heroDescription = null;
            if ($page) {
                $heroTitle = is_string($page->quote_title) ? trim($page->quote_title) : null;
                $heroSubtitle = is_string($page->quote_subtitle) ? trim($page->quote_subtitle) : null;
                $heroDescription = is_string($page->quote_description) ? trim($page->quote_description) : null;

                if (!$heroTitle) {
                    $pageName = is_string($page->name) ? trim($page->name) : 'London';
                    $baseName = trim((string) preg_replace('/\s+Airport$/i', '', $pageName));
                    if ($baseName === '') {
                        $baseName = $pageName;
                    }

                    $heroTitle = "Reliable {$baseName} Airport Taxi Service";
                }

                if (!$heroSubtitle) {
                    $pageName = is_string($page->name) ? trim($page->name) : 'UK';
                    $heroSubtitle = str_ends_with(strtolower($pageName), 'airport')
                        ? "{$pageName} Pickups and Drop-offs"
                        : "{$pageName} Airport Transfer Service";
                }

                if (!$heroDescription) {
                    $pageName = is_string($page->name) ? trim($page->name) : 'UK';
                    $heroDescription = "Book professional {$pageName} airport taxi transfers to and from all major UK airports. We provide punctual drivers, fixed fares, and comfortable vehicles for every journey.";
                }
            }

            $view->with([
                'heroTitle' => $heroTitle ?? $quote->hero_title ?? 'Reliable London Airport Taxi Service',
                'heroSubtitle' => $heroSubtitle ?? $quote->hero_subtitle ?? 'Airport Transfers Across the UK',
                'heroDescription' => $heroDescription ?? $quote->description ?? 'Book professional London airport taxi transfers to and from all major UK airports. Whether you are travelling alone, with family, or in a group, we provide comfortable, punctual and affordable transport with fixed prices and no hidden charges.',
                'heroAdditional' => 'Reserve your taxi in advance through our quick online booking system and enjoy a smooth, stress-free journey to or from the airport.',
                'contactSentence' => 'Need assistance? Our customer support team is available 24 hours a day, 7 days a week on',
                'phoneNumber' => $quote->phone ?? '(+44) 1582 801 611',
                'highlights' => $quote->highlights ?? [
                    'Free cancellation up to 12 hours before pickup',
                    'Real-time flight tracking for timely pickups',
                    'Fully licensed and professional drivers',
                    'Comfortable vehicles for individuals and groups',
                    '24/7 customer support and assistance',
                ],
            ]);
        });

        View::composer('partials.offcanvas', function ($view) {
            $offcanvas = null;

            if (Schema::hasTable('offcanvas')) {
                try {
                    $offcanvas = Offcanvas::where('section_key', 'offcanvas')->first();
                } catch (\Throwable $e) {
                    $offcanvas = null;
                }
            }

            $view->with([
                'logo' => $offcanvas->logo ?? GalleryPath::path('i/154'),
                'address' => $offcanvas->address ?? '960 Capability Green, LU1 3PE Luton',
                'email' => $offcanvas->email ?? 'info@a1airportcars.co.uk',
                'phone' => $offcanvas->phone ?? '(+44) - 1582 - 801 - 611',
                'buttonText' => $offcanvas->button_text ?? 'Manage My Booking',
                'buttonLink' => $offcanvas->button_link ?? 'manage-booking',
                'socialLinks' => $offcanvas->social_links ?? [
                    ['icon' => 'fab fa-facebook-f', 'link' => '#'],
                    ['icon' => 'fab fa-twitter', 'link' => '#'],
                    ['icon' => 'fab fa-youtube', 'link' => '#'],
                    ['icon' => 'fab fa-linkedin-in', 'link' => '#'],
                ],
            ]);
        });

        View::composer('partials.testimonials', function ($view) {
            $testimonials = Testimonial::orderBy('order')->get();

            $view->with([
                'testimonials' => $testimonials,
                'testimonialSectionTitle' => 'Our Testimonials',
                'testimonialSectionHeading' => 'What They’re Saying About A1 Airport Cars',
                'testimonialSectionDescription' => 'Hear from our satisfied customers who trust A1 Airport Cars for reliable, comfortable, and on-time airport transfers. We pride ourselves on delivering a smooth travel experience from pickup to drop-off.',
            ]);
        });

        View::composer('partials.steps', function ($view) {
            $steps = StepItem::orderBy('order')->get();
            $features = FeatureBenefit::orderBy('order')->get();

            $page = null;
            $pageId = request()->attributes->get('url_page_id');

            if (is_numeric($pageId)) {
                $page = Page::find((int) $pageId);
            }

            if (!$page) {
                $defaultUrl = Url::where('group_slug', 'airport-transfers')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->first();

                if ($defaultUrl && is_numeric($defaultUrl->page_id)) {
                    $page = Page::find((int) $defaultUrl->page_id);
                }
            }

            if (!$page) {
                $page = Page::orderBy('id')->first();
            }

            $featureHeading = is_string($page?->why_use_heading) ? trim($page->why_use_heading) : '';

            if ($featureHeading === '') {
                $featureHeading = 'Why You Should Use A1 Airport Cars';
            }

            $view->with([
                'steps' => $steps,
                'featureHeading' => $featureHeading,
                'features' => $features,
            ]);
        });

        View::composer('partials.footer', function ($view) {
            $footer = Footer::where('section_key', 'footer')->first();
            $social = $footer->social_links ?? [
                ['icon' => 'fab fa-facebook-f', 'link' => '#'],
                ['icon' => 'fab fa-twitter', 'link' => '#'],
                ['icon' => 'fa-brands fa-linkedin-in', 'link' => '#'],
                ['icon' => 'fa-brands fa-youtube', 'link' => '#'],
            ];

            $view->with([
                'footerLogo' => $footer->logo ?? GalleryPath::path('i/152'),
                'footerTagline' => $footer->tagline ?? 'Your go to option for reliable Airport Transfers',
                'contactAddress' => $footer->contact_address ?? '960 Capability Green, LU1 3PE Luton, United Kingdom',
                'contactEmail' => $footer->contact_email ?? 'info@a1airportcars.co.uk',
                'contactPhone' => $footer->contact_phone ?? '(+44) - 1582 - 801 - 611',
                'links' => $footer->links ?? [
                    ['label' => 'FAQ', 'url' => 'about'],
                    ['label' => 'Terms & Conditions', 'url' => 'car-details'],
                    ['label' => 'Refund Policy', 'url' => 'news-details'],
                    ['label' => 'Privacy Policy', 'url' => 'gallery'],
                    ['label' => 'Contact', 'url' => 'contact'],
                ],
                'airports' => $footer->airports ?? [
                    ['label' => 'Heathrow Airport Transfers', 'url' => '/airport-transfers/heathrow-airport-transfers'],
                    ['label' => 'Gatwick Airport Transfers', 'url' => '/airport-transfers/gatwick-airport-transfers'],
                    ['label' => 'Stansted Airport Transfers', 'url' => '/airport-transfers/stansted-airport-transfers'],
                    ['label' => 'Luton Airport Transfers', 'url' => '/airport-transfers/luton-airport-transfers'],
                    ['label' => 'Manchester Airport Transfers', 'url' => '/airport-transfers/manchester-airport-transfers'],
                    ['label' => 'Birmingham Airport Transfers', 'url' => '/airport-transfers/birmingham-airport-transfers'],
                    ['label' => 'London City Airport Transfers', 'url' => '/airport-transfers/london-city-airport-transfers'],
                ],
                'cities' => $footer->cities ?? [
                    'Aylesbury', 'Buckingham', 'Coventry', 'Baldock', 'Bedford',
                    'Cambridge', 'Corby', 'Dartford', 'Daventry', 'Dunstable',
                    'Harpenden', 'East Grinstead', 'East Midlands',
                ],
                'copyright' => $footer->copyright ?? '© Copyright '.date('Y').' A1 Airport Cars | Powered by <a href="./">BXS</a>',
                'socialLinks' => $social,
            ]);
        });

        View::composer('partials.header', function ($view) {
            $header = null;

            if (Schema::hasTable('headers')) {
                try {
                    $header = Header::where('section_key', 'header')->first();
                } catch (\Throwable $e) {
                    $header = null;
                }
            }

            $airportLinks = $header->airport_links ?? [
                ['label' => 'Heathrow Airport Transfers', 'url' => '/airport-transfers/heathrow-airport-transfers'],
                ['label' => 'Gatwick Airport Transfers', 'url' => '/airport-transfers/gatwick-airport-transfers'],
                ['label' => 'Stansted Airport Transfers', 'url' => '/airport-transfers/stansted-airport-transfers'],
                ['label' => 'Luton Airport Transfers', 'url' => '/airport-transfers/luton-airport-transfers'],
                ['label' => 'Manchester Airport Transfers', 'url' => '/airport-transfers/manchester-airport-transfers'],
                ['label' => 'Birmingham Airport Transfers', 'url' => '/airport-transfers/birmingham-airport-transfers'],
                ['label' => 'London City Airport Transfers', 'url' => '/airport-transfers/london-city-airport-transfers'],
            ];

            $cityLinks = $header->city_links ?? [
                ['label' => 'Aylesbury', 'url' => '/city-transfers/aylesbury-city-transfers'],
                ['label' => 'Buckingham', 'url' => '/city-transfers/buckingham-city-transfers'],
                ['label' => 'Coventry', 'url' => '/city-transfers/coventry-city-transfers'],
                ['label' => 'Baldock', 'url' => '/city-transfers/baldock-city-transfers'],
                ['label' => 'Bedford', 'url' => '/city-transfers/bedford-city-transfers'],
                ['label' => 'Cambridge', 'url' => '/city-transfers/cambridge-city-transfers'],
                ['label' => 'Corby', 'url' => '/city-transfers/corby-city-transfers'],
                ['label' => 'Dartford', 'url' => '/city-transfers/dartford-city-transfers'],
                ['label' => 'Daventry', 'url' => '/city-transfers/daventry-city-transfers'],
                ['label' => 'Dunstable', 'url' => '/city-transfers/dunstable-city-transfers'],
                ['label' => 'Harpenden', 'url' => '/city-transfers/harpenden-city-transfers'],
                ['label' => 'East Grinstead', 'url' => '/city-transfers/east-grinstead-city-transfers'],
                ['label' => 'East Midlands', 'url' => '/city-transfers/east-midlands-city-transfers'],
            ];

            $otherLinks = [];

            if (Schema::hasTable('urls')) {
                try {
                    $dynamicUrls = Url::query()
                        ->with('page:id,name')
                        ->where('is_active', true)
                        ->orderBy('group_slug')
                        ->orderBy('id')
                        ->get(['id', 'page_id', 'group_slug', 'slug']);

                    $dynamicAirportLinks = [];
                    $dynamicCityLinks = [];
                    $dynamicOtherLinks = [];
                    $seen = [];

                    foreach ($dynamicUrls as $url) {
                        $path = '/'.ltrim($url->group_slug.'/'.$url->slug, '/');

                        if (isset($seen[$path])) {
                            continue;
                        }

                        $seen[$path] = true;

                        $label = is_string($url->page?->name) && trim($url->page->name) !== ''
                            ? trim($url->page->name)
                            : ucfirst(str_replace('-', ' ', (string) $url->slug));

                        $item = [
                            'label' => $label,
                            'url' => $path,
                        ];

                        if ($url->group_slug === 'airport-transfers') {
                            $dynamicAirportLinks[] = $item;
                        } elseif ($url->group_slug === 'city-transfers') {
                            $dynamicCityLinks[] = $item;
                        } else {
                            $dynamicOtherLinks[] = $item;
                        }
                    }

                    if ($dynamicAirportLinks !== []) {
                        $airportLinks = $dynamicAirportLinks;
                    }

                    if ($dynamicCityLinks !== []) {
                        $cityLinks = $dynamicCityLinks;
                    }

                    $otherLinks = $dynamicOtherLinks;
                } catch (\Throwable $e) {
                    $otherLinks = [];
                }
            }

            $view->with([
                'topEmail' => $header->top_email ?? 'info@example.com',
                'topAddress' => $header->top_address ?? '88 Broklyn Golden Street. New York',
                'topLinks' => $header->top_links ?? [
                    ['label' => 'Manage Bookings', 'url' => 'manage-booking'],
                    ['label' => 'Support', 'url' => 'contact'],
                    ['label' => 'Contact', 'url' => 'contact'],
                ],
                'socialLinks' => $header->social_links ?? [
                    ['icon' => 'fab fa-facebook-f', 'url' => '#'],
                    ['icon' => 'fab fa-twitter', 'url' => '#'],
                    ['icon' => 'fa-brands fa-linkedin-in', 'url' => '#'],
                    ['icon' => 'fa-brands fa-youtube', 'url' => '#'],
                ],
                'logoLight' => $header->logo_light ?? GalleryPath::path('i/152'),
                'logoDark' => $header->logo_dark ?? GalleryPath::path('i/154'),
                'phoneLabel' => $header->phone_label ?? 'Call Anytime',
                'phoneNumber' => $header->phone_number ?? '+92 (8800) - 9850',
                'buttonText' => $header->button_text ?? 'Manage Bookings',
                'buttonLink' => $header->button_link ?? 'manage-booking',
                'airportLinks' => $airportLinks,
                'cityLinks' => $cityLinks,
                'otherLinks' => $otherLinks,
            ]);
        });

        View::composer('partials.faq', function ($view) {
            $pageId = request()->attributes->get('url_page_id');

            if (is_numeric($pageId)) {
                $faqs = FaqItem::where('page_id', (int) $pageId)
                    ->orderBy('order')
                    ->get();
            } else {
                $faqs = FaqItem::whereNull('page_id')
                    ->orderBy('order')
                    ->get();
            }

            if ($faqs->isEmpty()) {
                $faqs = FaqItem::whereNull('page_id')
                    ->orderBy('order')
                    ->get();
            }

            $view->with([
                'faqTitle' => 'Question & Answers',
                'faqSubtitle' => 'Frequently asked questions',
                'faqs' => $faqs,
            ]);
        });

        View::composer('airport', function ($view) {
            $pageId = request()->attributes->get('url_page_id');
            $page = null;

            if (is_numeric($pageId)) {
                $page = Page::find((int) $pageId);
            }

            if (!$page) {
                $defaultUrl = Url::where('group_slug', 'airport-transfers')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->first();

                if ($defaultUrl && is_numeric($defaultUrl->page_id)) {
                    $page = Page::find((int) $defaultUrl->page_id);
                }
            }

            if (!$page) {
                $page = Page::orderBy('id')->first();
            }

            $pageName = $page?->name ?? 'Airport';
            $defaultEnabledPartials = [
                'head',
                'preloader',
                'scroll-up',
                'offcanvas',
                'header',
                'breadcrumb',
                'quotes',
                'testimonials',
                'why-us',
                'card-fleet',
                'steps',
                'card-blog',
                'faq',
                'footer',
                'script',
            ];
            $enabledPartials = $defaultEnabledPartials;

            if ($page && is_numeric($page->id)) {
                $partialConfig = PagePartial::where('page_id', (int) $page->id)->first();

                if ($partialConfig) {
                    $partialToggleMap = [
                        'head' => (bool) $partialConfig->head,
                        'preloader' => (bool) $partialConfig->preloader,
                        'scroll-up' => (bool) $partialConfig->scroll_up,
                        'offcanvas' => (bool) $partialConfig->offcanvas,
                        'header' => (bool) $partialConfig->header,
                        'breadcrumb' => (bool) $partialConfig->breadcrumb,
                        'quotes' => (bool) $partialConfig->quotes,
                        'testimonials' => (bool) $partialConfig->testimonials,
                        'why-us' => (bool) $partialConfig->why_us,
                        'card-fleet' => (bool) $partialConfig->card_fleet,
                        'steps' => (bool) $partialConfig->steps,
                        'card-blog' => (bool) $partialConfig->card_blog,
                        'faq' => (bool) $partialConfig->faq,
                        'footer' => (bool) $partialConfig->footer,
                        'script' => (bool) $partialConfig->script,
                    ];

                    $enabledPartials = array_keys(array_filter($partialToggleMap));
                }
            }

            $rowPatternRaw = is_string($page?->number_of_rows)
                ? trim($page->number_of_rows)
                : '';

            if ($rowPatternRaw === '') {
                $rowPatternRaw = '1';
            }

            $patternTokens = preg_split('/\s*,\s*|\s+/', $rowPatternRaw) ?: [];
            $rowPattern = [];

            foreach ($patternTokens as $token) {
                $normalizedToken = trim((string) $token);
                if (in_array($normalizedToken, ['1', '2', '3'], true)) {
                    $rowPattern[] = (int) $normalizedToken;
                }
            }

            if ($rowPattern === []) {
                $rowPattern = [1];
            }

            $rowPattern = array_slice($rowPattern, 0, 20);

            $rowTemplateMap = [
                1 => is_string($page?->one_column) ? trim($page->one_column) : '',
                2 => is_string($page?->two_column) ? trim($page->two_column) : '',
                3 => is_string($page?->three_column) ? trim($page->three_column) : '',
            ];

            $rowFallbackTemplateMap = [
                1 => '<div class="row"><div class="col-12"><div class="about-content pt-4"><div class="section-title-content"><h3 class="wow fadeInUp" data-wow-delay=".4s">Reliable '.$pageName.' Transfers</h3></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Add your custom one-row HTML in admin under the one_column field.</p></div></div></div>',
                2 => '<div class="row g-4"><div class="col-md-6"><div class="about-content pt-4"><div class="section-title-content"><h4 class="wow fadeInUp" data-wow-delay=".4s">Left Block</h4></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Add your custom two-column row HTML in admin under the two_column field.</p></div></div><div class="col-md-6"><div class="about-content pt-4"><div class="section-title-content"><h4 class="wow fadeInUp" data-wow-delay=".4s">Right Block</h4></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Your second block content will appear here.</p></div></div></div>',
                3 => '<div class="row g-4"><div class="col-lg-4"><div class="about-content pt-4"><div class="section-title-content"><h4 class="wow fadeInUp" data-wow-delay=".4s">First Block</h4></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Add your custom three-column row HTML in admin under the three_column field.</p></div></div><div class="col-lg-4"><div class="about-content pt-4"><div class="section-title-content"><h4 class="wow fadeInUp" data-wow-delay=".4s">Second Block</h4></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Middle content placeholder.</p></div></div><div class="col-lg-4"><div class="about-content pt-4"><div class="section-title-content"><h4 class="wow fadeInUp" data-wow-delay=".4s">Third Block</h4></div><p class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">Right content placeholder.</p></div></div></div>',
            ];

            $airportContentRows = [];
            $resolvedRowPattern = [];

            $rowBlocks = is_array($page?->row_blocks) ? $page->row_blocks : [];
            foreach ($rowBlocks as $rowBlock) {
                if (!is_array($rowBlock)) {
                    continue;
                }

                $layout = isset($rowBlock['layout']) ? (int) $rowBlock['layout'] : 0;
                if (!in_array($layout, [1, 2, 3], true)) {
                    continue;
                }

                $template = isset($rowBlock['html']) && is_string($rowBlock['html'])
                    ? trim($rowBlock['html'])
                    : '';

                if ($template === '') {
                    $template = $rowTemplateMap[$layout] ?? '';
                }

                if ($template === '') {
                    $template = $rowFallbackTemplateMap[$layout] ?? '';
                }

                if ($template !== '') {
                    $airportContentRows[] = $template;
                    $resolvedRowPattern[] = $layout;
                }
            }

            if ($airportContentRows === []) {
                foreach ($rowPattern as $rowSize) {
                    $template = $rowTemplateMap[$rowSize] ?? '';
                    if ($template === '') {
                        $template = $rowFallbackTemplateMap[$rowSize] ?? '';
                    }

                    if ($template !== '') {
                        $airportContentRows[] = $template;
                        $resolvedRowPattern[] = $rowSize;
                    }
                }
            }

            $airportContentHtml = implode("\n", $airportContentRows);

            $view->with([
                'enabledPartials' => $enabledPartials,
                'airportHeadTitle' => $page?->head_title ?? 'A1 Airport Cars ',
                'airportNumberOfRows' => implode(',', $resolvedRowPattern === [] ? $rowPattern : $resolvedRowPattern),
                'airportContentHtml' => $airportContentHtml,
            ]);
        });

        View::composer('partials.why-us', function ($view) {
            $whyUs = WhyUs::where('section_key', 'why-us')->first();
            $page = null;
            $pageId = request()->attributes->get('url_page_id');

            if (is_numeric($pageId)) {
                $page = Page::find((int) $pageId);
            }

            if (!$page) {
                $defaultUrl = Url::where('group_slug', 'airport-transfers')
                    ->where('is_active', true)
                    ->orderBy('id')
                    ->first();

                if ($defaultUrl && is_numeric($defaultUrl->page_id)) {
                    $page = Page::find((int) $defaultUrl->page_id);
                }
            }

            if (!$page) {
                $page = Page::orderBy('id')->first();
            }

            $sectionTitle = is_string($page?->why_us_title) ? trim($page->why_us_title) : '';
            $sectionSubtitle = is_string($whyUs?->section_subtitle) ? trim($whyUs->section_subtitle) : '';

            if ($sectionTitle === '') {
                $sectionTitle = $whyUs->section_title ?? 'Why Choose Us';
            }

            if ($sectionSubtitle === '') {
                $sectionSubtitle = 'Why Book an Airport Taxi with Us?';
            }

            $view->with([
                'sectionTitle' => $sectionTitle,
                'sectionSubtitle' => $sectionSubtitle,
                'leftItems' => $whyUs->left_items ?? [
                    'Save up to 40% compared to other taxi fares',
                    'The price you see is the price you pay',
                    'No hidden fees or surprise charges',
                    'Free 45 minutes airport waiting time',
                    'Real-time flight monitoring for timely pickups',
                ],
                'rightItems' => $whyUs->right_items ?? [
                    'Prices: Up to 40% cheaper than many airport taxis',
                    'Vehicles: Saloon, Executive, MPV, and 8-Seater Minivans',
                    'Drivers: Fully licensed and background checked',
                    'Cancellation: No cancellation charge*',
                    'Baby Seats: Available free of charge (subject to availability)',
                ],
            ]);
        });

        View::composer('partials.breadcrumb', function ($view) {
            $route = request()->route();
            $pageKey = 'home';

            if ($route) {
                if ($route->getName() === 'pages.show') {
                    $pageKey = $route->parameter('slug') ?: 'home';
                } elseif ($route->getName() === 'pages.legacy') {
                    $pageKey = strtolower($route->parameter('legacy') ?: 'home');
                } else {
                    $pageKey = $route->getName() ?: 'home';
                }
            }

            $breadcrumb = Breadcrumb::where('page_key', $pageKey)->first();

            $view->with([
                'img' => $breadcrumb->img ?? GalleryPath::path('i/151'),
                'Title' => $breadcrumb->title ?? 'Home',
                'Title2' => $breadcrumb->title2 ?? ucfirst(str_replace('-', ' ', $pageKey)),
                'SubTitle' => $breadcrumb->subtitle ?? '',
            ]);
        });
    }
}
