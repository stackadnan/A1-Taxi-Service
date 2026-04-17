<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $formTitle }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
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
    </style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h1 class="h4 mb-1">{{ $formTitle }}</h1>
            <p class="text-muted mb-0">Configure content blocks and URL for this page.</p>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-secondary" href="{{ route('admin.pages.index') }}">Back to Pages</a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-danger">Logout</button>
            </form>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ $formAction }}" method="POST" class="card shadow-sm">
        @csrf
        @if ($formMethod !== 'POST')
            @method($formMethod)
        @endif

        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Page Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $page->name) }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Rows Pattern (auto-generated)</label>
                    <input type="text" id="rows_pattern_preview" class="form-control" value="{{ old('number_of_rows', $page->number_of_rows ?: '1,2,1') }}" readonly>
                    <div class="form-text">This value updates automatically from the row builder.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Head Title</label>
                    <input type="text" name="head_title" class="form-control" value="{{ old('head_title', $page->head_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Quote Title</label>
                    <input type="text" name="quote_title" class="form-control" value="{{ old('quote_title', $page->quote_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Quote Subtitle</label>
                    <input type="text" name="quote_subtitle" class="form-control" value="{{ old('quote_subtitle', $page->quote_subtitle) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Us Title</label>
                    <input type="text" name="why_us_title" class="form-control" value="{{ old('why_us_title', $page->why_us_title) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Us Heading</label>
                    <input type="text" name="why_us_heading" class="form-control" value="{{ old('why_us_heading', $page->why_us_heading) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label">Why Use Heading</label>
                    <input type="text" name="why_use_heading" class="form-control" value="{{ old('why_use_heading', $page->why_use_heading) }}">
                </div>

                <div class="col-12">
                    <label class="form-label">Quote Description</label>
                    <textarea name="quote_description" rows="4" class="form-control">{{ old('quote_description', $page->quote_description) }}</textarea>
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

                    <input type="hidden" name="number_of_rows" id="number_of_rows" value="{{ old('number_of_rows', $page->number_of_rows ?: '1,2,1') }}">
                    <input type="hidden" name="row_blocks_json" id="row_blocks_json" value="{{ old('row_blocks_json', '') }}">

                    <div id="rowBlocksContainer" class="vstack gap-3"></div>
                    <div class="form-text mt-2">Each row stores separate HTML. Example: pattern 1,1 can have two different one-column rows.</div>
                </div>

                <div class="col-12">
                    <h2 class="h6 mb-3">Partials</h2>
                    <p class="text-muted mb-3">Enable or disable individual page sections. Example: uncheck Breadcrumb to hide it on this page.</p>

                    @php
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
                    @endphp

                    <div class="row g-2">
                        @foreach ($partials as $key => $label)
                            @php
                                $isChecked = old("partials.$key", ($partialToggles[$key] ?? true) ? '1' : '0') == '1';
                            @endphp
                            <div class="col-md-3 col-sm-6">
                                <input type="hidden" name="partials[{{ $key }}]" value="0">
                                <div class="form-check border rounded p-2 bg-white">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="partials[{{ $key }}]"
                                        id="partial_{{ $key }}"
                                        value="1"
                                        {{ $isChecked ? 'checked' : '' }}
                                    >
                                    <label class="form-check-label" for="partial_{{ $key }}">{{ $label }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <h2 class="h6 mb-3">Primary URL Mapping (Auto appears in header)</h2>
            <input type="hidden" name="url_id" value="{{ old('url_id', $primaryUrl?->id) }}">

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Group Slug</label>
                    <input type="text" name="group_slug" class="form-control" value="{{ old('group_slug', $primaryUrl?->group_slug) }}" placeholder="airport-transfers">
                    <div class="form-text">Examples: airport-transfers, city-transfers, other-pages</div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Page Slug</label>
                    <input type="text" name="slug" class="form-control" value="{{ old('slug', $primaryUrl?->slug) }}" placeholder="heathrow-airport-transfers">
                </div>

                <div class="col-md-4 d-flex align-items-end">
                    <div class="form-check">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="url_is_active"
                            value="1"
                            id="url_is_active"
                            {{ old('url_is_active', $primaryUrl?->is_active ? 1 : 0) ? 'checked' : '' }}
                        >
                        <label class="form-check-label" for="url_is_active">Active URL</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer d-flex gap-2 justify-content-end">
            <a class="btn btn-outline-secondary" href="{{ route('admin.pages.index') }}">Cancel</a>
            <button type="submit" class="btn btn-primary">{{ $submitLabel }}</button>
        </div>
    </form>
</div>

<script>
    (function () {
        const container = document.getElementById('rowBlocksContainer');
        const toggleBtn = document.getElementById('toggleRowOptions');
        const panel = document.getElementById('rowOptionPanel');
        const numberOfRowsInput = document.getElementById('number_of_rows');
        const rowBlocksInput = document.getElementById('row_blocks_json');
        const rowsPatternPreview = document.getElementById('rows_pattern_preview');

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
                mode: 'code',
                design,
                html: buildHtmlFromDesign(safeLayout, design),
            };
        }

        const oldRowBlocksJson = @json(old('row_blocks_json', ''));
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
            rows = @json($rowBlocks ?? []);
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
            return {
                layout,
                mode: 'code',
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
            rowsPatternPreview.value = numberOfRowsInput.value;
            rowBlocksInput.value = JSON.stringify(rows);
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
                    designFieldsHtml.push(`
                        <div class="col-12 border rounded p-3 bg-light-subtle">
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
                    rows[index].mode = mode;
                    renderRows();
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

        toggleBtn.addEventListener('click', function () {
            panel.classList.toggle('d-none');
        });

        document.querySelectorAll('.add-row-type').forEach((btn) => {
            btn.addEventListener('click', function () {
                const layout = normalizeLayout(this.dataset.layout);
                rows.push(createDefaultRow(layout));
                renderRows();
            });
        });

        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function () {
                updateHiddenInputs();
            });
        }

        renderRows();
    })();
</script>
</body>
</html>
