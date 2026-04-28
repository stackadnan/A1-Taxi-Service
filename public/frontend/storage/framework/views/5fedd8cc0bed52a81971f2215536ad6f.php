<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($formTitle); ?></title>
    <link rel="stylesheet" href="<?php echo e(asset('assets/css/bootstrap.min.css')); ?>">
    <style>
        .row-code-editor {
            font-family: Consolas, "Courier New", monospace;
            min-height: 220px;
        }

        .design-editor {
            min-height: 120px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding: .75rem;
            background: #fff;
        }

        .design-editor:focus {
            outline: none;
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .25);
        }

        .row-tab-btn.active {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }

        .quote-description-editor {
            min-height: 220px;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1"><?php echo e($formTitle); ?></h1>
            <p class="text-muted mb-0">Configure content blocks and URL for this page.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.pages.index')); ?>">Back to Pages</a>
            <form action="<?php echo e(route('admin.logout')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    <?php if(session('status')): ?>
        <div class="alert alert-success"><?php echo e(session('status')); ?></div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?php echo e($formAction); ?>" method="POST" class="card shadow-sm">
        <?php echo csrf_field(); ?>
        <?php if($formMethod !== 'POST'): ?>
            <?php echo method_field($formMethod); ?>
        <?php endif; ?>

        <div class="card-body">
            <?php
                $seoTabFields = [
                    'seo_meta_title',
                    'seo_canonical',
                    'seo_meta_description',
                    'seo_meta_keywords',
                    'seo_schema_script',
                ];
                $hasSeoTabErrors = false;
                foreach ($seoTabFields as $seoTabField) {
                    if ($errors->has($seoTabField)) {
                        $hasSeoTabErrors = true;
                        break;
                    }
                }

                $activeAdminTab = old('admin_tab', $hasSeoTabErrors ? 'seo' : 'page');
                if (!in_array($activeAdminTab, ['page', 'seo'], true)) {
                    $activeAdminTab = 'page';
                }
            ?>

            <input type="hidden" name="admin_tab" id="admin_tab" value="<?php echo e($activeAdminTab); ?>">

            <ul class="nav nav-tabs mb-4" role="tablist">
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link admin-tab-btn <?php echo e($activeAdminTab === 'page' ? 'active' : ''); ?>" data-tab-target="page" role="tab">Page</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button type="button" class="nav-link admin-tab-btn <?php echo e($activeAdminTab === 'seo' ? 'active' : ''); ?>" data-tab-target="seo" role="tab">SEO</button>
                </li>
            </ul>

            <div id="admin-tab-page" class="admin-tab-pane <?php echo e($activeAdminTab === 'page' ? '' : 'd-none'); ?>">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Name</label>
                    <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $page->name)); ?>" required>
                </div>

                <!--<div class="col-md-6">-->
                <!--    <label class="form-label">Rows Pattern (auto-generated)</label>-->
                <!--    <input type="text" id="rows_pattern_preview" class="form-control" value="<?php echo e(old('number_of_rows', $page->number_of_rows ?: '1,2,1')); ?>" readonly>-->
                <!--    <div class="form-text">This value updates automatically from the row builder.</div>-->
                <!--</div>-->

                <div class="col-md-6">
                    <label class="form-label">Head Title</label>
                    <input type="text" name="head_title" class="form-control" value="<?php echo e(old('head_title', $page->head_title)); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Header Title</label>
                    <input type="text" name="quote_title" class="form-control" value="<?php echo e(old('quote_title', $page->quote_title)); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Header Subtitle</label>
                    <input type="text" name="quote_subtitle" class="form-control" value="<?php echo e(old('quote_subtitle', $page->quote_subtitle)); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Us Title</label>
                    <input type="text" name="why_us_title" class="form-control" value="<?php echo e(old('why_us_title', $page->why_us_title)); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Us Heading</label>
                    <input type="text" name="why_us_heading" class="form-control" value="<?php echo e(old('why_us_heading', $page->why_us_heading)); ?>">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Use Heading</label>
                    <input type="text" name="why_use_heading" class="form-control" value="<?php echo e(old('why_use_heading', $page->why_use_heading)); ?>">
                </div>

                <div class="col-12">
                    <?php
                        $defaultServiceHighlightsTemplate = '<!-- Service Highlights -->'
                            ."\n"
                            .'<ul class="text-white list-unstyled hero-features" data-animation="fadeInUp">'
                            ."\n"
                            .'    <li>✔ Free cancellation up to 12 hours before pickup</li>'
                            ."\n"
                            .'    <li>✔ Real-time flight tracking for timely pickups</li>'
                            ."\n"
                            .'    <li>✔ Fully licensed and professional drivers</li>'
                            ."\n"
                            .'    <li>✔ Comfortable vehicles for individuals and groups</li>'
                            ."\n"
                            .'    <li>✔ 24/7 customer support and assistance</li>'
                            ."\n"
                            .'</ul>';

                        $defaultQuoteDescriptionTemplate = '<p class="text-white mb-3" data-animation="fadeInUp">Book professional London airport taxi transfers to and from all major UK airports. Whether you are travelling alone, with family, or in a group, we provide comfortable, punctual and affordable transport with fixed prices and no hidden charges.</p>'
                            ."\n\n"
                            .'<p class="text-white mb-4" data-animation="fadeInUp">Reserve your taxi in advance through our quick online booking system and enjoy a smooth, stress-free journey to or from the airport.</p>'
                            ."\n\n"
                            .'<p class="text-white mb-4" data-animation="fadeInUp">Need assistance? Our customer support team is available <strong>24 hours a day, 7 days a week</strong> on <strong>(+44) 1582 801 611</strong>.</p>'
                            ."\n\n"
                            .$defaultServiceHighlightsTemplate;

                        $quoteDescriptionValue = old('quote_description', $page->quote_description);
                        if (!is_string($quoteDescriptionValue) || trim($quoteDescriptionValue) === '') {
                            $quoteDescriptionValue = $defaultQuoteDescriptionTemplate;
                        } elseif (!str_contains($quoteDescriptionValue, 'hero-features')) {
                            $quoteDescriptionValue = rtrim($quoteDescriptionValue)."\n\n".$defaultServiceHighlightsTemplate;
                        }

                        $quoteDescriptionMode = old('quote_description_mode', 'design');
                        if (!in_array($quoteDescriptionMode, ['design', 'code'], true)) {
                            $quoteDescriptionMode = 'design';
                        }
                    ?>

                    <label class="form-label">Header Description</label>
                    <input type="hidden" name="quote_description_mode" id="quote_description_mode" value="<?php echo e($quoteDescriptionMode); ?>">

                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm row-tab-btn quote-desc-tab-btn <?php echo e($quoteDescriptionMode === 'code' ? 'btn-primary active' : 'btn-outline-primary'); ?>" data-mode="code">Code Editor</button>
                        <button type="button" class="btn btn-sm row-tab-btn quote-desc-tab-btn <?php echo e($quoteDescriptionMode === 'design' ? 'btn-primary active' : 'btn-outline-primary'); ?>" data-mode="design">Design Editor</button>
                    </div>

                    <div id="quote_description_code_pane" class="<?php echo e($quoteDescriptionMode === 'code' ? '' : 'd-none'); ?>">
                        <textarea name="quote_description" id="quote_description" rows="10" class="form-control row-code-editor"><?php echo e($quoteDescriptionValue); ?></textarea>
                    </div>

                    <div id="quote_description_design_pane" class="<?php echo e($quoteDescriptionMode === 'design' ? '' : 'd-none'); ?>">
                        <div class="d-flex gap-2 mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary quote-desc-cmd" data-cmd="bold"><b>B</b></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quote-desc-cmd" data-cmd="italic"><i>I</i></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quote-desc-cmd" data-cmd="underline"><u>U</u></button>
                            <button type="button" class="btn btn-sm btn-outline-secondary quote-desc-cmd" data-cmd="insertUnorderedList">List</button>
                            <input type="color" id="quote_desc_color" class="form-control form-control-color" value="#ffffff" title="Choose text color">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="quote_desc_apply_color">Apply Text Color</button>
                        </div>
                        <div id="quote_description_design_editor" class="design-editor quote-description-editor" contenteditable="true"></div>
                    </div>

                   
                </div>

                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Row Content Builder</label>
                        <div>
                            <button type="button" id="toggleRowOptions" class="btn btn-sm btn-success">+ Add Row</button>
                        </div>
                    </div>
                    <div id="rowOptionPanel" class="d-none border rounded p-2 mb-3 bg-white">
                        <div class="small text-muted mb-2">Choose row type:</div>
                        <div class="d-flex flex-wrap gap-2">
                            <button type="button" class="btn btn-sm btn-outline-primary add-row-type" data-layout="1">One Column</button>
                            <button type="button" class="btn btn-sm btn-outline-primary add-row-type" data-layout="2">Two Columns</button>
                            <button type="button" class="btn btn-sm btn-outline-primary add-row-type" data-layout="3">Three Columns</button>
                        </div>
                    </div>

                    <input type="hidden" name="number_of_rows" id="number_of_rows" value="<?php echo e(old('number_of_rows', $page->number_of_rows ?: '1,2,1')); ?>">
                    <input type="hidden" name="row_blocks_json" id="row_blocks_json" value="<?php echo e(old('row_blocks_json', '')); ?>">

                    <div id="rowBlocksContainer" class="vstack gap-3"></div>
                    <div class="form-text mt-2">Each row stores separate HTML. Example: pattern 1,1 can have two different one-column rows.</div>
                </div>

                <div class="col-12">
                    <h2 class="h6 mb-3">Includes</h2>
                    <p class="text-muted mb-3">Enable or disable individual page sections. Example: uncheck Breadcrumb to hide it on this page.</p>

                    <?php
                        $partials = [
                            'head' => 'Head',
                            'preloader' => 'Preloader',
                            'scroll_up' => 'Scroll Up',
                            'offcanvas' => 'Offcanvas',
                            'header' => 'Header',
                            'breadcrumb' => 'Breadcrumb',
                            'quotes' => 'Quotes',
                            'testimonials' => 'Testimonials',
                            'why_us' => 'Why Us',
                            'card_fleet' => 'Card Fleet',
                            'steps' => 'Steps',
                            'card_blog' => 'Card Blog',
                            'faq' => 'FAQ',
                            'footer' => 'Footer',
                            'script' => 'Script',
                        ];
                    ?>

                    <div class="row g-2">
                        <?php $__currentLoopData = $partials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $isChecked = old("partials.$key", ($partialToggles[$key] ?? true) ? '1' : '0') == '1';
                            ?>
                            <div class="col-md-3 col-sm-6">
                                <input type="hidden" name="partials[<?php echo e($key); ?>]" value="0">
                                <div class="form-check border rounded p-2 bg-white">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="partials[<?php echo e($key); ?>]"
                                        id="partial_<?php echo e($key); ?>"
                                        value="1"
                                        <?php echo e($isChecked ? 'checked' : ''); ?>

                                    >
                                    <label class="form-check-label" for="partial_<?php echo e($key); ?>"><?php echo e($label); ?></label>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h6 mb-3">Primary URL Mapping (Auto appears in header)</h2>
            <input type="hidden" name="url_id" value="<?php echo e(old('url_id', $primaryUrl?->id)); ?>">

            <?php
                $selectedGroupSlug = old('group_slug', $primaryUrl?->group_slug ?? '');
                $groupSlugList = $groupSlugOptions ?? [];
                $selectedGroupExists = in_array($selectedGroupSlug, $groupSlugList, true);
            ?>

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Existing Group Slugs</label>
                    <select name="group_slug" class="form-select">
                        <option value="">Select group slug</option>
                        <?php $__currentLoopData = ($groupSlugOptions ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $existingGroupSlug): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($existingGroupSlug); ?>" <?php echo e($selectedGroupSlug === $existingGroupSlug ? 'selected' : ''); ?>>
                                <?php echo e($existingGroupSlug); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <div class="form-text">Pick from existing group slugs below.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Or Add New Group Slug</label>
                    <input
                        type="text"
                        name="group_slug_custom"
                        class="form-control"
                        value="<?php echo e(old('group_slug_custom', $selectedGroupExists ? '' : $selectedGroupSlug)); ?>"
                        placeholder="testing"
                    >
                    <div class="form-text">Optional. If provided, this value is used instead of the dropdown.</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Page Slug</label>
                    <input type="text" name="slug" class="form-control" value="<?php echo e(old('slug', $primaryUrl?->slug)); ?>" placeholder="heathrow-airport-transfers">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="url_is_active"
                            value="1"
                            id="url_is_active"
                            <?php echo e(old('url_is_active', $primaryUrl?->is_active ? 1 : 0) ? 'checked' : ''); ?>

                        >
                        <label class="form-check-label" for="url_is_active">Active URL</label>
                    </div>
                </div>
            </div>
            </div>

            <div id="admin-tab-seo" class="admin-tab-pane <?php echo e($activeAdminTab === 'seo' ? '' : 'd-none'); ?>">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Meta Title</label>
                        <input
                            type="text"
                            name="seo_meta_title"
                            class="form-control"
                            value="<?php echo e(old('seo_meta_title', $seoData['meta_title'] ?? '')); ?>"
                            placeholder="Enter meta title"
                        >
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Canonical Url</label>
                        <input
                            type="url"
                            name="seo_canonical"
                            class="form-control"
                            value="<?php echo e(old('seo_canonical', $seoData['canonical'] ?? '')); ?>"
                            placeholder="https://example.com/your-page"
                        >
                    </div>

                    <div class="col-md-8">
                        <label class="form-label">Meta Description</label>
                        <textarea name="seo_meta_description" rows="4" class="form-control" placeholder="Enter meta description"><?php echo e(old('seo_meta_description', $seoData['meta_description'] ?? '')); ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Meta Keywords</label>
                        <input
                            type="text"
                            name="seo_meta_keywords"
                            class="form-control"
                            value="<?php echo e(old('seo_meta_keywords', $seoData['meta_keywords'] ?? '')); ?>"
                            placeholder="keyword1, keyword2"
                        >
                    </div>

                    <div class="col-12">
                        <label class="form-label">SEO Schema</label>
                        <textarea name="seo_schema_script" rows="14" class="form-control" placeholder="Paste JSON-LD schema script here"><?php echo e(old('seo_schema_script', $seoData['schema_script'] ?? '')); ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2 justify-content-end">
            <a class="btn btn-outline-secondary" href="<?php echo e(route('admin.pages.index')); ?>">Cancel</a>
            <button type="submit" class="btn btn-primary"><?php echo e($submitLabel); ?></button>
        </div>
    </form>
</div>

<script>
    (function () {
        const adminTabInput = document.getElementById('admin_tab');
        const adminTabButtons = document.querySelectorAll('.admin-tab-btn');
        const adminPageTabPane = document.getElementById('admin-tab-page');
        const adminSeoTabPane = document.getElementById('admin-tab-seo');

        const container = document.getElementById('rowBlocksContainer');
        const toggleBtn = document.getElementById('toggleRowOptions');
        const panel = document.getElementById('rowOptionPanel');
        const numberOfRowsInput = document.getElementById('number_of_rows');
        const rowBlocksInput = document.getElementById('row_blocks_json');
        const rowsPatternPreview = document.getElementById('rows_pattern_preview');

        const quoteDescriptionInput = document.getElementById('quote_description');
        const quoteDescriptionModeInput = document.getElementById('quote_description_mode');
        const quoteDescriptionCodePane = document.getElementById('quote_description_code_pane');
        const quoteDescriptionDesignPane = document.getElementById('quote_description_design_pane');
        const quoteDescriptionDesignEditor = document.getElementById('quote_description_design_editor');
        const quoteDescriptionTabButtons = document.querySelectorAll('.quote-desc-tab-btn');
        const quoteDescriptionCmdButtons = document.querySelectorAll('.quote-desc-cmd');
        const quoteDescriptionColorPicker = document.getElementById('quote_desc_color');
        const quoteDescriptionApplyColorBtn = document.getElementById('quote_desc_apply_color');
        let selectedQuoteColor = '#ffffff';

        function setActiveAdminTab(tabName) {
            const safeTab = tabName === 'seo' ? 'seo' : 'page';

            adminTabButtons.forEach((btn) => {
                const isActive = btn.dataset.tabTarget === safeTab;
                btn.classList.toggle('active', isActive);
            });

            if (adminPageTabPane) {
                adminPageTabPane.classList.toggle('d-none', safeTab !== 'page');
            }

            if (adminSeoTabPane) {
                adminSeoTabPane.classList.toggle('d-none', safeTab !== 'seo');
            }

            if (adminTabInput) {
                adminTabInput.value = safeTab;
            }
        }

        adminTabButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                setActiveAdminTab(this.dataset.tabTarget);
            });
        });

        setActiveAdminTab(adminTabInput ? adminTabInput.value : 'page');

        function setQuoteDescriptionMode(mode) {
            const safeMode = mode === 'code' ? 'code' : 'design';

            quoteDescriptionTabButtons.forEach((btn) => {
                const isActive = btn.dataset.mode === safeMode;
                btn.classList.toggle('active', isActive);
                btn.classList.toggle('btn-primary', isActive);
                btn.classList.toggle('btn-outline-primary', !isActive);
            });

            if (quoteDescriptionCodePane) {
                quoteDescriptionCodePane.classList.toggle('d-none', safeMode !== 'code');
            }

            if (quoteDescriptionDesignPane) {
                quoteDescriptionDesignPane.classList.toggle('d-none', safeMode !== 'design');
            }

            if (quoteDescriptionModeInput) {
                quoteDescriptionModeInput.value = safeMode;
            }
        }

        function normalizeColorToHex(colorValue) {
            if (typeof colorValue !== 'string') {
                return null;
            }

            const value = colorValue.trim();
            if (value === '') {
                return null;
            }

            const shortHexMatch = value.match(/^#([0-9a-fA-F]{3})$/);
            if (shortHexMatch) {
                return '#'+shortHexMatch[1].split('').map((ch) => ch + ch).join('').toLowerCase();
            }

            const longHexMatch = value.match(/^#([0-9a-fA-F]{6})$/);
            if (longHexMatch) {
                return '#'+longHexMatch[1].toLowerCase();
            }

            const rgbMatch = value.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
            if (rgbMatch) {
                const [r, g, b] = [rgbMatch[1], rgbMatch[2], rgbMatch[3]].map((part) => {
                    const numeric = Number(part);
                    return Math.max(0, Math.min(255, numeric));
                });

                return '#'+[r, g, b]
                    .map((part) => part.toString(16).padStart(2, '0'))
                    .join('');
            }

            const probe = document.createElement('span');
            probe.style.color = value;
            document.body.appendChild(probe);
            const computed = window.getComputedStyle(probe).color;
            probe.remove();

            const computedMatch = computed.match(/^rgba?\(\s*(\d+)\s*,\s*(\d+)\s*,\s*(\d+)/i);
            if (!computedMatch) {
                return null;
            }

            const [r, g, b] = [computedMatch[1], computedMatch[2], computedMatch[3]].map((part) => Number(part));
            return '#'+[r, g, b]
                .map((part) => part.toString(16).padStart(2, '0'))
                .join('');
        }

        function extractQuoteColorFromHtml(html) {
            if (typeof html !== 'string' || html.trim() === '') {
                return '#ffffff';
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const styledElements = Array.from(doc.body.querySelectorAll('[style]'));

            for (const el of styledElements) {
                const inlineColor = el.style ? el.style.color : '';
                const normalized = normalizeColorToHex(inlineColor);
                if (normalized) {
                    return normalized;
                }
            }

            return '#ffffff';
        }

        function stripQuoteColorFromHtml(html) {
            if (typeof html !== 'string' || html.trim() === '') {
                return '';
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const targets = Array.from(doc.body.querySelectorAll('*'));

            targets.forEach((el) => {
                el.classList.remove('text-white', 'text-light', 'text-muted');
                el.style.removeProperty('color');
                el.style.removeProperty('opacity');

                const styleAttr = el.getAttribute('style');
                if (styleAttr !== null && styleAttr.trim() === '') {
                    el.removeAttribute('style');
                }
            });

            return doc.body.innerHTML;
        }

        function applyQuoteColorToHtml(html, color) {
            if (typeof html !== 'string' || html.trim() === '') {
                return '';
            }

            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            let targets = Array.from(doc.body.querySelectorAll('*'));

            if (targets.length === 0) {
                const raw = (doc.body.innerHTML || '').trim();
                const wrapper = doc.createElement('p');
                wrapper.className = 'mb-3';
                wrapper.setAttribute('data-animation', 'fadeInUp');
                wrapper.innerHTML = raw;
                doc.body.innerHTML = '';
                doc.body.appendChild(wrapper);
                targets = Array.from(doc.body.querySelectorAll('*'));
            }

            targets.forEach((el) => {
                el.classList.remove('text-white', 'text-light', 'text-muted');
                el.style.setProperty('color', color, 'important');
                el.style.setProperty('opacity', '1', 'important');
            });

            return doc.body.innerHTML;
        }

        if (quoteDescriptionInput && quoteDescriptionDesignEditor) {
            const initialHtml = quoteDescriptionInput.value || '';
            selectedQuoteColor = extractQuoteColorFromHtml(initialHtml);

            if (quoteDescriptionColorPicker) {
                quoteDescriptionColorPicker.value = selectedQuoteColor;
            }

            const sanitizedInitialHtml = stripQuoteColorFromHtml(initialHtml);
            quoteDescriptionInput.value = sanitizedInitialHtml;
            quoteDescriptionDesignEditor.innerHTML = sanitizedInitialHtml;

            quoteDescriptionInput.addEventListener('input', function () {
                const sanitized = stripQuoteColorFromHtml(this.value || '');
                this.value = sanitized;
                quoteDescriptionDesignEditor.innerHTML = sanitized;
            });

            quoteDescriptionDesignEditor.addEventListener('input', function () {
                quoteDescriptionInput.value = stripQuoteColorFromHtml(this.innerHTML || '');
            });
        }

        quoteDescriptionTabButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                setQuoteDescriptionMode(this.dataset.mode);
            });
        });

        quoteDescriptionCmdButtons.forEach((btn) => {
            btn.addEventListener('click', function () {
                if (!quoteDescriptionDesignEditor) {
                    return;
                }

                quoteDescriptionDesignEditor.focus();
                document.execCommand(this.dataset.cmd, false, null);

                if (quoteDescriptionInput) {
                    quoteDescriptionInput.value = quoteDescriptionDesignEditor.innerHTML;
                }
            });
        });

        if (quoteDescriptionApplyColorBtn) {
            quoteDescriptionApplyColorBtn.addEventListener('click', function () {
                if (!quoteDescriptionColorPicker) {
                    return;
                }

                selectedQuoteColor = quoteDescriptionColorPicker.value || '#ffffff';
            });
        }

        if (quoteDescriptionColorPicker) {
            quoteDescriptionColorPicker.addEventListener('input', function () {
                selectedQuoteColor = quoteDescriptionColorPicker.value || '#ffffff';
            });
        }

        setQuoteDescriptionMode(quoteDescriptionModeInput ? quoteDescriptionModeInput.value : 'design');

        const layoutLabelMap = {
            1: 'One Column',
            2: 'Two Columns',
            3: 'Three Columns',
        };

        const defaultTitleMap = {
            1: ['Section Title'],
            2: ['Left Title', 'Right Title'],
            3: ['Left Title', 'Center Title', 'Right Title'],
        };

        const defaultDescriptionMap = {
            1: ['Write section description here.'],
            2: ['Write left column description here.', 'Write right column description here.'],
            3: ['Write left column description here.', 'Write center column description here.', 'Write right column description here.'],
        };

        const slotClassMap = {
            1: ['col-12'],
            2: ['col-md-6', 'col-md-6'],
            3: ['col-lg-4 col-md-6', 'col-lg-4 col-md-6', 'col-lg-4 col-md-12'],
        };

        function escapeHtml(value) {
            return String(value)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function buildHtmlFromDesign(layout, design) {
            const safeLayout = normalizeLayout(layout);
            const headingTag = safeLayout === 1 ? 'h3' : 'h4';
            const rowClass = safeLayout === 1 ? 'row' : 'row g-4';
            const slotClasses = slotClassMap[safeLayout];

            let html = `<div class="${rowClass}">`;
            for (let i = 0; i < safeLayout; i++) {
                const item = design[i] || { title: '', description: '' };
                html += `
<div class="${slotClasses[i]}">
    <div class="about-content pt-4">
        <div class="section-title-content">
            <${headingTag} class="wow fadeInUp" data-wow-delay=".4s">${escapeHtml(item.title || '')}</${headingTag}>
        </div>
        <div class="mt-1 mt-md-0 wow fadeInUp" data-wow-delay=".6s">${item.description || ''}</div>
    </div>
</div>`;
            }

            html += '\n</div>';
            return html;
        }

        function buildDefaultDesign(layout) {
            const safeLayout = normalizeLayout(layout);
            const titles = defaultTitleMap[safeLayout];
            const descriptions = defaultDescriptionMap[safeLayout];

            const result = [];
            for (let i = 0; i < safeLayout; i++) {
                result.push({
                    title: titles[i],
                    description: `<p>${descriptions[i]}</p>`,
                });
            }

            return result;
        }

        function parseDesignFromHtml(layout, html) {
            const safeLayout = normalizeLayout(layout);
            const fallback = buildDefaultDesign(safeLayout);

            if (typeof html !== 'string' || html.trim() === '') {
                return fallback;
            }

            try {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const cards = Array.from(doc.querySelectorAll('.about-content'));

                if (cards.length === 0) {
                    return fallback;
                }

                const result = [];
                for (let i = 0; i < safeLayout; i++) {
                    const card = cards[i];
                    if (!card) {
                        result.push(fallback[i]);
                        continue;
                    }

                    const titleNode = card.querySelector('h1,h2,h3,h4,h5,h6');
                    const descNode = card.querySelector('.mt-1.mt-md-0') || card.querySelector('p,div');

                    result.push({
                        title: titleNode ? titleNode.textContent.trim() : fallback[i].title,
                        description: descNode ? descNode.innerHTML.trim() : fallback[i].description,
                    });
                }

                return result;
            } catch (e) {
                return fallback;
            }
        }

        function createDefaultRow(layout) {
            const safeLayout = normalizeLayout(layout);
            const design = buildDefaultDesign(safeLayout);
            return {
                layout: safeLayout,
                mode: 'design',
                design,
                html: buildHtmlFromDesign(safeLayout, design),
            };
        }

        const oldRowBlocksJson = <?php echo json_encode(old('row_blocks_json', ''), 512) ?>;
        let rows = [];

        if (typeof oldRowBlocksJson === 'string' && oldRowBlocksJson.trim() !== '') {
            try {
                const parsed = JSON.parse(oldRowBlocksJson);
                if (Array.isArray(parsed)) {
                    rows = parsed;
                }
            } catch (e) {
                rows = [];
            }
        }

        if (!Array.isArray(rows) || rows.length === 0) {
            rows = <?php echo json_encode($rowBlocks ?? [], 15, 512) ?>;
        }

        if (!Array.isArray(rows)) {
            rows = [];
        }

        function normalizeLayout(value) {
            const layout = Number(value);
            return [1, 2, 3].includes(layout) ? layout : 1;
        }

        rows = rows.map((row) => {
            const layout = normalizeLayout(row.layout);
            const html = typeof row.html === 'string' ? row.html : '';
            const design = parseDesignFromHtml(layout, html);
            const mode = row.mode === 'code' ? 'code' : 'design';
            return {
                layout,
                mode,
                design,
                html: html.trim() === '' ? buildHtmlFromDesign(layout, design) : html,
            };
        });

        if (rows.length === 0) {
            rows.push(createDefaultRow(1));
        }

        function updateHiddenInputs() {
            const cleaned = rows.map((row) => ({
                layout: normalizeLayout(row.layout),
                mode: row.mode === 'design' ? 'design' : 'code',
                design: Array.isArray(row.design) ? row.design : [],
                html: typeof row.html === 'string' ? row.html : '',
            }));

            rows = cleaned;

            const pattern = rows.map((row) => String(row.layout)).join(',');
            numberOfRowsInput.value = pattern || '1';
            if (rowsPatternPreview) {
                rowsPatternPreview.value = numberOfRowsInput.value;
            }
            if (rowBlocksInput) {
                rowBlocksInput.value = JSON.stringify(rows);
            }
        }

        function renderRows() {
            container.innerHTML = '';

            rows.forEach((row, index) => {
                const rowCard = document.createElement('div');
                rowCard.className = 'card border';

                const layout = normalizeLayout(row.layout);
                const html = typeof row.html === 'string' ? row.html : '';
                const mode = row.mode === 'design' ? 'design' : 'code';
                const design = Array.isArray(row.design) ? row.design : buildDefaultDesign(layout);

                const designFieldsHtml = [];
                for (let i = 0; i < layout; i++) {
                    const title = (design[i] && typeof design[i].title === 'string') ? design[i].title : '';
                    const description = (design[i] && typeof design[i].description === 'string') ? design[i].description : '';
                    const blockColumnClass = slotClassMap[layout] && slotClassMap[layout][i]
                        ? `col-12 ${slotClassMap[layout][i]}`
                        : 'col-12';
                    designFieldsHtml.push(`
                        <div class="${blockColumnClass} border rounded p-3 bg-light-subtle">
                            <div class="fw-semibold mb-2">Block ${i + 1}</div>
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control mb-2 design-title" data-index="${index}" data-block="${i}" value="${escapeHtml(title)}">

                            <div class="d-flex gap-2 mb-2">
                                <button type="button" class="btn btn-sm btn-outline-secondary design-cmd" data-cmd="bold" data-index="${index}" data-block="${i}"><b>B</b></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary design-cmd" data-cmd="italic" data-index="${index}" data-block="${i}"><i>I</i></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary design-cmd" data-cmd="underline" data-index="${index}" data-block="${i}"><u>U</u></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary design-cmd" data-cmd="insertUnorderedList" data-index="${index}" data-block="${i}">List</button>
                            </div>

                            <label class="form-label">Description</label>
                            <div class="design-editor design-description" contenteditable="true" data-index="${index}" data-block="${i}">${description}</div>
                        </div>
                    `);
                }

                rowCard.innerHTML = `
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="fw-semibold">Row ${index + 1} - ${layoutLabelMap[layout]}</div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary move-up" data-index="${index}">↑</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary move-down" data-index="${index}">↓</button>
                            <button type="button" class="btn btn-sm btn-outline-danger remove-row" data-index="${index}">Remove</button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-sm row-tab-btn ${mode === 'code' ? 'active btn-primary' : 'btn-outline-primary'} row-tab" data-index="${index}" data-mode="code">Code Editor</button>
                            <button type="button" class="btn btn-sm row-tab-btn ${mode === 'design' ? 'active btn-primary' : 'btn-outline-primary'} row-tab" data-index="${index}" data-mode="design">Design Editor</button>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Row Type</label>
                                <select class="form-select row-layout" data-index="${index}">
                                    <option value="1" ${layout === 1 ? 'selected' : ''}>One Column</option>
                                    <option value="2" ${layout === 2 ? 'selected' : ''}>Two Columns</option>
                                    <option value="3" ${layout === 3 ? 'selected' : ''}>Three Columns</option>
                                </select>
                            </div>

                            <div class="col-12 ${mode === 'code' ? '' : 'd-none'} row-pane-code" data-index="${index}">
                                <label class="form-label">Row HTML Content</label>
                                <textarea class="form-control row-html row-code-editor" data-index="${index}" rows="8"></textarea>
                            </div>

                            <div class="col-12 ${mode === 'design' ? '' : 'd-none'} row-pane-design" data-index="${index}">
                                <div class="row g-3">
                                    ${designFieldsHtml.join('')}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(rowCard);
                const textarea = rowCard.querySelector('.row-html');
                if (textarea) {
                    textarea.value = html;
                }
            });

            updateHiddenInputs();
            bindRowEvents();
        }

        function bindRowEvents() {
            container.querySelectorAll('.row-tab').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const index = Number(this.dataset.index);
                    const mode = this.dataset.mode === 'design' ? 'design' : 'code';

                    if (!rows[index]) {
                        return;
                    }

                    rows[index].mode = mode;

                    // Keep the builder visible when switching row editor mode.
                    setActiveAdminTab('page');

                    const rowCard = this.closest('.card');
                    if (!rowCard) {
                        renderRows();
                        return;
                    }

                    rowCard.querySelectorAll('.row-tab').forEach((tabBtn) => {
                        const tabMode = tabBtn.dataset.mode === 'design' ? 'design' : 'code';
                        const isActive = tabMode === mode;
                        tabBtn.classList.toggle('active', isActive);
                        tabBtn.classList.toggle('btn-primary', isActive);
                        tabBtn.classList.toggle('btn-outline-primary', !isActive);
                    });

                    const codePane = rowCard.querySelector(`.row-pane-code[data-index="${index}"]`);
                    const designPane = rowCard.querySelector(`.row-pane-design[data-index="${index}"]`);

                    if (codePane) {
                        codePane.classList.toggle('d-none', mode !== 'code');
                    }

                    if (designPane) {
                        designPane.classList.toggle('d-none', mode !== 'design');
                    }

                    updateHiddenInputs();
                });
            });

            container.querySelectorAll('.row-layout').forEach((select) => {
                select.addEventListener('change', function () {
                    const index = Number(this.dataset.index);
                    const newLayout = normalizeLayout(this.value);
                    rows[index] = createDefaultRow(newLayout);
                    renderRows();
                });
            });

            container.querySelectorAll('.row-html').forEach((textarea) => {
                textarea.addEventListener('input', function () {
                    const index = Number(this.dataset.index);
                    rows[index].html = this.value;
                    rows[index].design = parseDesignFromHtml(rows[index].layout, rows[index].html);
                    updateHiddenInputs();
                });
            });

            container.querySelectorAll('.design-title').forEach((input) => {
                input.addEventListener('input', function () {
                    const rowIndex = Number(this.dataset.index);
                    const blockIndex = Number(this.dataset.block);
                    if (!Array.isArray(rows[rowIndex].design)) {
                        rows[rowIndex].design = buildDefaultDesign(rows[rowIndex].layout);
                    }
                    rows[rowIndex].design[blockIndex].title = this.value;
                    rows[rowIndex].html = buildHtmlFromDesign(rows[rowIndex].layout, rows[rowIndex].design);
                    updateHiddenInputs();
                });
            });

            container.querySelectorAll('.design-description').forEach((editor) => {
                editor.addEventListener('input', function () {
                    const rowIndex = Number(this.dataset.index);
                    const blockIndex = Number(this.dataset.block);
                    if (!Array.isArray(rows[rowIndex].design)) {
                        rows[rowIndex].design = buildDefaultDesign(rows[rowIndex].layout);
                    }
                    rows[rowIndex].design[blockIndex].description = this.innerHTML;
                    rows[rowIndex].html = buildHtmlFromDesign(rows[rowIndex].layout, rows[rowIndex].design);
                    updateHiddenInputs();
                });
            });

            container.querySelectorAll('.design-cmd').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const cmd = this.dataset.cmd;
                    const rowIndex = Number(this.dataset.index);
                    const blockIndex = Number(this.dataset.block);
                    const editor = container.querySelector(`.design-description[data-index="${rowIndex}"][data-block="${blockIndex}"]`);
                    if (!editor) {
                        return;
                    }

                    editor.focus();
                    document.execCommand(cmd, false, null);

                    if (!Array.isArray(rows[rowIndex].design)) {
                        rows[rowIndex].design = buildDefaultDesign(rows[rowIndex].layout);
                    }

                    rows[rowIndex].design[blockIndex].description = editor.innerHTML;
                    rows[rowIndex].html = buildHtmlFromDesign(rows[rowIndex].layout, rows[rowIndex].design);
                    updateHiddenInputs();
                });
            });

            container.querySelectorAll('.remove-row').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const index = Number(this.dataset.index);
                    rows.splice(index, 1);
                    if (rows.length === 0) {
                        rows.push(createDefaultRow(1));
                    }
                    renderRows();
                });
            });

            container.querySelectorAll('.move-up').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const index = Number(this.dataset.index);
                    if (index <= 0) return;
                    const temp = rows[index - 1];
                    rows[index - 1] = rows[index];
                    rows[index] = temp;
                    renderRows();
                });
            });

            container.querySelectorAll('.move-down').forEach((btn) => {
                btn.addEventListener('click', function () {
                    const index = Number(this.dataset.index);
                    if (index >= rows.length - 1) return;
                    const temp = rows[index + 1];
                    rows[index + 1] = rows[index];
                    rows[index] = temp;
                    renderRows();
                });
            });
        }

        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', function () {
                panel.classList.toggle('d-none');
            });
        }

        document.querySelectorAll('.add-row-type').forEach((btn) => {
            btn.addEventListener('click', function () {
                const layout = normalizeLayout(this.dataset.layout);
                rows.push(createDefaultRow(layout));
                renderRows();
            });
        });

        const form = document.querySelector('form.card');
        if (form) {
            form.addEventListener('submit', function () {
                if (quoteDescriptionInput && quoteDescriptionDesignEditor) {
                    if (quoteDescriptionModeInput && quoteDescriptionModeInput.value === 'design') {
                        quoteDescriptionInput.value = quoteDescriptionDesignEditor.innerHTML;
                    }

                    const cleanHtml = stripQuoteColorFromHtml(quoteDescriptionInput.value || '');
                    quoteDescriptionInput.value = applyQuoteColorToHtml(cleanHtml, selectedQuoteColor || '#ffffff');
                }

                updateHiddenInputs();
            });
        }

        renderRows();
    })();
</script>
</body>
</html>
<?php /**PATH /home/executiveairport/public_html/frontend/resources/views/admin/pages/form.blade.php ENDPATH**/ ?>