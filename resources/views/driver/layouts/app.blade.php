<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Airport Services') }} - Driver Portal</title>
    
    <!-- TailwindCSS -->
    <script>tailwind = { darkMode: 'class' }</script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
    <!-- Dark mode: apply class before paint to avoid flash -->
    <script>
        (function(){
            try {
                if (localStorage.getItem('driverTheme') === 'dark' ||
                    (!localStorage.getItem('driverTheme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch(e){}
        })();
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* Mobile-first responsive design */
        @media (max-width: 768px) {
            .container {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
        
        .job-card {
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        
        .job-card:hover {
            transform: translateY(-4px) scale(1.02);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.22);
        }

        .stat-card {
            background: linear-gradient(145deg, var(--bg-color), var(--bg-color-end));
            border: none;
            color: white;
            min-height: 130px;
            position: relative;
            overflow: hidden;
        }

        /* Subtle sheen overlay */
        .stat-card::before {
            content: '';
            position: absolute;
            top: -40%;
            left: -40%;
            width: 80%;
            height: 80%;
            background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, transparent 70%);
            pointer-events: none;
        }

        .stat-card .stat-icon-wrap {
            width: 56px;
            height: 56px;
            border-radius: 16px;
            background: rgba(255,255,255,0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            backdrop-filter: blur(4px);
        }

        .new-jobs      { --bg-color: #4F46E5; --bg-color-end: #7C3AED; box-shadow: 0 6px 20px rgba(79,70,229,0.4); }
        .accepted-jobs { --bg-color: #F59E0B; --bg-color-end: #EF6C00; box-shadow: 0 6px 20px rgba(245,158,11,0.4); }
        .completed-jobs{ --bg-color: #059669; --bg-color-end: #0D9488; box-shadow: 0 6px 20px rgba(5,150,105,0.4); }
        .declined-jobs { --bg-color: #DC2626; --bg-color-end: #9F1239; box-shadow: 0 6px 20px rgba(220,38,38,0.4); }
        .docs-clear    { --bg-color: #16A34A; --bg-color-end: #0D9488; box-shadow: 0 6px 20px rgba(22,163,74,0.35); }
        .docs-warning  { --bg-color: #D97706; --bg-color-end: #B45309; box-shadow: 0 6px 20px rgba(217,119,6,0.35); }
        .docs-danger   { --bg-color: #DC2626; --bg-color-end: #9F1239; box-shadow: 0 6px 20px rgba(220,38,38,0.4); }

        /* Compact card style used only when all documents are clear. */
        .stat-card.docs-compact {
            min-height: 96px;
            padding-top: 0.85rem;
            padding-bottom: 0.85rem;
        }

        .stat-card.docs-compact .stat-icon-wrap {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            margin-bottom: 8px;
        }

        .stat-card.docs-compact .stat-icon-wrap i {
            font-size: 1rem;
        }

        .stat-card.docs-compact p {
            margin-bottom: 0;
            font-size: 0.68rem;
            letter-spacing: 0.08em;
        }

        /* ===== DARK MODE ===== */
        html.dark body  { background-color: #0f172a; color: #e2e8f0; }
        html.dark main  { background-color: #0f172a; }

        /* Nav */
        html.dark nav                        { background-color: #1e293b !important; border-color: #334155 !important; }
        html.dark nav h1                     { color: #e2e8f0 !important; }
        html.dark nav span                   { color: #cbd5e1 !important; }
        html.dark #theme-toggle              { border-color: #475569 !important; color: #94a3b8 !important; }
        html.dark #theme-toggle:hover        { background-color: #334155 !important; color: #e2e8f0 !important; }
        html.dark #driverNotificationButton  { border-color: #475569 !important; color: #94a3b8 !important; }
        html.dark #driverNotificationButton:hover { background-color: #334155 !important; color: #e2e8f0 !important; }
        html.dark nav button[type="submit"]  { color: #94a3b8 !important; }
        html.dark nav button[type="submit"]:hover { color: #e2e8f0 !important; }

        /* Surfaces */
        html.dark .bg-white                  { background-color: #1e293b !important; }
        html.dark .bg-gray-50                { background-color: #162032 !important; }
        html.dark .bg-gray-100               { background-color: #0f172a !important; }

        /* Text — all grays get lighter in dark mode not darker */
        html.dark .text-gray-900             { color: #f1f5f9 !important; }
        html.dark .text-gray-800             { color: #e2e8f0 !important; }
        html.dark .text-gray-700             { color: #cbd5e1 !important; }
        html.dark .text-gray-600             { color: #94a3b8 !important; }
        html.dark .text-gray-500             { color: #94a3b8 !important; }
        html.dark .text-gray-400             { color: #64748b !important; }

        /* Borders */
        html.dark .border                    { border-color: #334155 !important; }
        html.dark .border-gray-200           { border-color: #334155 !important; }
        html.dark .border-gray-100           { border-color: #1e293b !important; }

        /* Driver info — icon tile backgrounds */
        html.dark .bg-indigo-100             { background-color: #1e1b4b !important; }
        html.dark .bg-green-100              { background-color: #052e16 !important; }
        html.dark .bg-blue-100               { background-color: #0c1a2e !important; }
        html.dark .bg-purple-100             { background-color: #2d0a4e !important; }
        html.dark .bg-yellow-100             { background-color: #2d1a00 !important; }

        /* Driver info — icon colors brighter so they pop on dark tiles */
        html.dark .text-indigo-500           { color: #818cf8 !important; }
        html.dark .text-green-500            { color: #34d399 !important; }
        html.dark .text-blue-500             { color: #60a5fa !important; }
        html.dark .text-purple-500           { color: #a78bfa !important; }
        html.dark .text-yellow-500           { color: #fbbf24 !important; }

        /* Section label colours inside driver info */
        html.dark .text-green-700            { color: #4ade80 !important; }

        /* Availability toggle buttons — unselected */
        html.dark .driver-avail-btn          { background-color: #1e293b !important; border-color: #475569 !important; color: #94a3b8 !important; }
        /* Active selected */
        html.dark .driver-avail-btn.border-green-500 { background-color: #052e16 !important; border-color: #22c55e !important; color: #4ade80 !important; }
        /* Inactive selected */
        html.dark .driver-avail-btn.border-red-400   { background-color: #450a0a !important; border-color: #f87171 !important; color: #fca5a5 !important; }

        /* Availability inner icon circles */
        html.dark .driver-avail-btn .text-green-500  { color: #22c55e !important; }
        html.dark .driver-avail-btn .text-red-400    { color: #f87171 !important; }

        /* Inputs */
        html.dark input[type="datetime-local"] { background-color: #0f172a !important; border-color: #475569 !important; color: #e2e8f0 !important; }

        /* Flash messages — direct children of #page-content */
        html.dark #page-content > .bg-green-100 { background-color: #052e16 !important; border-color: #166534 !important; color: #86efac !important; }
        html.dark #page-content > .bg-red-100   { background-color: #450a0a !important; border-color: #991b1b !important; color: #fca5a5 !important; }

        /* Quick action card + driver info availability section background */
        html.dark .bg-gray-50.rounded-xl     { background-color: #0f172a !important; }
        html.dark .bg-gray-50.rounded-xl.border { border-color: #1e293b !important; }

        /* Theme toggle transition */
        #theme-toggle { transition: background 0.2s, color 0.2s; }

        /* Shared back button style across driver pages */
        .driver-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: 1px solid #cbd5e1;
            background-color: #f8fafc;
            color: #1f2937;
            font-size: 0.875rem;
            font-weight: 600;
            padding: 0.55rem 0.95rem;
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .driver-back-btn:hover {
            background-color: #ffffff;
            border-color: #94a3b8;
            box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
            transform: translateY(-1px);
        }

        .driver-back-btn i {
            color: #4f46e5;
            font-size: 0.8rem;
        }

        html.dark .driver-back-btn {
            background-color: #1f2937;
            border-color: #475569;
            color: #e2e8f0;
        }

        html.dark .driver-back-btn:hover {
            background-color: #334155;
            border-color: #64748b;
            color: #f8fafc;
            box-shadow: 0 8px 22px rgba(2, 6, 23, 0.45);
        }

        html.dark .driver-back-btn i {
            color: #a5b4fc;
        }

        /* Driver notification panel */
        #driverNotificationDropdown {
            max-height: 28rem;
        }

        #driverNotificationList .driver-note-title,
        #driverNotificationList .driver-note-message {
            word-break: break-word;
            overflow-wrap: anywhere;
        }

        @media (max-width: 640px) {
            #driverNotificationDropdown {
                position: fixed;
                left: 0.5rem;
                right: 0.5rem;
                width: auto;
                max-width: none;
                top: calc(env(safe-area-inset-top, 0px) + 4.25rem);
                margin-top: 0;
            }
        }

        html.dark #driverNotificationDropdown {
            background-color: #0f172a;
            border-color: #334155;
        }

        html.dark #driverNotificationDropdown .driver-note-header {
            background-color: #1e293b;
            border-color: #334155;
        }

        html.dark #driverNotificationList .driver-note-item {
            border-color: #1e293b;
            color: #cbd5e1;
        }

        html.dark #driverNotificationList .driver-note-item-unread {
            background-color: #1e293b;
        }

        html.dark #driverNotificationList .driver-note-item-read {
            background-color: #0f172a;
        }

        html.dark #driverNotificationList .driver-note-title {
            color: #f1f5f9;
        }

        html.dark #driverNotificationList .driver-note-time {
            color: #94a3b8;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200 pt-4 sm:pt-0 sticky top-0 z-50" style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Title -->
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">
                        @yield('title', 'Driver Portal')
                    </h1>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-3">
                    <span class="text-sm text-gray-600 hidden sm:inline">{{ auth('driver')->user()->name }}</span>

                    <div class="relative">
                        <button id="driverNotificationButton" class="relative w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none" title="Notifications">
                            <i class="fas fa-bell text-sm"></i>
                            <span id="driverNotificationBadge" class="hidden absolute -top-1 -right-1 h-5 min-w-5 px-1 rounded-full bg-red-500 text-white text-[11px] leading-5 text-center font-semibold">0</span>
                        </button>

                        <div id="driverNotificationDropdown" class="hidden absolute right-0 mt-2 w-80 max-w-[calc(100vw-1rem)] bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
                            <div class="driver-note-header p-3 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-semibold text-gray-900">Notifications</h3>
                                    <div class="flex items-center gap-3">
                                        <button id="driverMarkAllRead" class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Mark all read</button>
                                        <button id="driverClearAll" class="text-xs text-red-600 hover:text-red-700 font-medium">Clear all</button>
                                    </div>
                                </div>
                            </div>
                            <div id="driverNotificationList" class="max-h-80 overflow-y-auto">
                                <div class="p-4 text-sm text-gray-500 text-center">No notifications yet</div>
                            </div>
                        </div>
                    </div>

                    <!-- Dark / Light Toggle -->
                    <button id="theme-toggle" title="Toggle dark/light mode"
                        class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none">
                        <i id="theme-icon" class="fas fa-moon text-sm"></i>
                    </button>

                    <form action="{{ route('driver.logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm px-3 py-2 rounded">
                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main id="page-content" class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <!-- Flash Messages -->
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif
        
        @if (session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Scripts -->
    <script>
        // CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            baseUrl: '{{ url('/') }}',
            driverAcceptUrl: '{{ route('driver.jobs.accept', ':id') }}',
            driverDeclineUrl: '{{ route('driver.jobs.decline', ':id') }}',
            driverDashboardUrl: '{{ route('driver.dashboard') }}',
            driverNotificationsListUrl: '{{ route('driver.notifications.list') }}',
            driverNotificationsMarkReadUrl: '{{ route('driver.notifications.mark-read') }}',
            driverNotificationsClearUrl: '{{ route('driver.notifications.clear') }}'
        };
        
        // Driver notification panel state
        const driverNotificationState = {
            items: [],
            unreadCount: 0,
        };

        // Track SSE notifications already reflected in the UI to avoid duplicate badge increments.
        let processedDriverNotificationIds = new Set();

        const driverNotificationButton = document.getElementById('driverNotificationButton');
        const driverNotificationBadge = document.getElementById('driverNotificationBadge');
        const driverNotificationDropdown = document.getElementById('driverNotificationDropdown');
        const driverNotificationList = document.getElementById('driverNotificationList');
        const driverMarkAllRead = document.getElementById('driverMarkAllRead');
        const driverClearAll = document.getElementById('driverClearAll');

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        function parseNotificationDate(note) {
            const raw = note.created_at_iso || note.created_at || null;
            if (!raw) return null;
            const date = new Date(raw);
            return Number.isNaN(date.getTime()) ? null : date;
        }

        function formatNotificationAge(note) {
            const dt = parseNotificationDate(note);
            if (!dt) return 'Just now';

            const diffMs = Date.now() - dt.getTime();
            const sec = Math.max(0, Math.floor(diffMs / 1000));
            if (sec < 10) return 'Just now';
            if (sec < 60) return sec + ' sec ago';
            const min = Math.floor(sec / 60);
            if (min < 60) return min + (min === 1 ? ' min ago' : ' mins ago');
            const hr = Math.floor(min / 60);
            if (hr < 24) return hr + (hr === 1 ? ' hour ago' : ' hours ago');
            const day = Math.floor(hr / 24);
            return day + (day === 1 ? ' day ago' : ' days ago');
        }

        function normalizeNotification(note) {
            return {
                id: note.id,
                title: note.title || 'Notification',
                message: note.message || '',
                is_read: !!note.is_read,
                created_at: note.created_at || null,
                created_at_iso: note.created_at_iso || note.created_at || new Date().toISOString(),
            };
        }

        function updateDriverNotificationBadge() {
            if (!driverNotificationBadge) return;
            const unread = Math.max(0, Number(driverNotificationState.unreadCount || 0));
            if (unread > 0) {
                driverNotificationBadge.textContent = unread > 99 ? '99+' : String(unread);
                driverNotificationBadge.classList.remove('hidden');
            } else {
                driverNotificationBadge.classList.add('hidden');
            }
        }

        function renderDriverNotificationPanel() {
            if (!driverNotificationList) return;

            if (!driverNotificationState.items.length) {
                driverNotificationList.innerHTML = '<div class="p-4 text-sm text-gray-500 text-center">No notifications yet</div>';
                updateDriverNotificationBadge();
                return;
            }

            driverNotificationList.innerHTML = driverNotificationState.items.map(function(note) {
                const readClass = note.is_read ? 'driver-note-item-read' : 'driver-note-item-unread';
                return '' +
                    '<div class="driver-note-item ' + readClass + ' px-4 py-3 border-b border-gray-100" data-driver-note-id="' + escapeHtml(note.id) + '">' +
                        '<div class="flex items-start justify-between gap-3">' +
                            '<p class="driver-note-title text-sm font-semibold text-gray-800">' + escapeHtml(note.title) + '</p>' +
                            '<span class="driver-note-time text-xs text-gray-500 whitespace-nowrap">' + escapeHtml(formatNotificationAge(note)) + '</span>' +
                        '</div>' +
                        '<p class="driver-note-message text-sm text-gray-600 mt-1">' + escapeHtml(note.message) + '</p>' +
                    '</div>';
            }).join('');

            updateDriverNotificationBadge();
        }

        async function fetchDriverNotifications() {
            try {
                const response = await fetch(window.Laravel.driverNotificationsListUrl, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.Laravel.csrfToken }
                });
                if (!response.ok) return;

                const data = await response.json();
                const rows = Array.isArray(data.notifications) ? data.notifications : [];

                driverNotificationState.items = rows.map(normalizeNotification);
                driverNotificationState.unreadCount = Number(data.unread_count || 0);

                // Seed processed IDs so SSE replay on connect/reconnect does not double count.
                processedDriverNotificationIds.clear();
                driverNotificationState.items.forEach(function(note){
                    if (note && note.id !== undefined && note.id !== null) {
                        processedDriverNotificationIds.add(String(note.id));
                    }
                });

                renderDriverNotificationPanel();
            } catch (error) {
                console.error('Failed to fetch driver notifications:', error);
            }
        }

        async function markDriverNotificationsRead(ids) {
            try {
                await fetch(window.Laravel.driverNotificationsMarkReadUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(Array.isArray(ids) && ids.length ? { ids: ids } : {})
                });
            } catch (error) {
                console.error('Failed to mark driver notifications as read:', error);
            }
        }

        async function clearDriverNotifications() {
            try {
                await fetch(window.Laravel.driverNotificationsClearUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                });
            } catch (error) {
                console.error('Failed to clear driver notifications:', error);
            }
        }

        function prependDriverNotification(note) {
            const normalized = normalizeNotification(note);

            const existingIndex = driverNotificationState.items.findIndex(function(item) {
                return String(item.id) === String(normalized.id);
            });

            let shouldIncrementUnread = true;

            if (existingIndex >= 0) {
                const existing = driverNotificationState.items[existingIndex];
                shouldIncrementUnread = !!(existing && existing.is_read);
                driverNotificationState.items.splice(existingIndex, 1);
            }

            normalized.is_read = false;
            driverNotificationState.items.unshift(normalized);
            driverNotificationState.items = driverNotificationState.items.slice(0, 50);
            if (shouldIncrementUnread) {
                driverNotificationState.unreadCount += 1;
            }
            renderDriverNotificationPanel();
        }

        // Replace toast with panel insertion for client-side notifications.
        function showNotification(message, type = 'info') {
            prependDriverNotification({
                id: 'local-' + Date.now() + '-' + Math.floor(Math.random() * 1000),
                title: type === 'error' ? 'Action Error' : 'Update',
                message: message,
                created_at_iso: new Date().toISOString(),
                is_read: false,
            });
        }

        if (driverNotificationButton && driverNotificationDropdown) {
            driverNotificationButton.addEventListener('click', function(e) {
                e.preventDefault();
                driverNotificationDropdown.classList.toggle('hidden');
                if (!driverNotificationDropdown.classList.contains('hidden')) {
                    fetchDriverNotifications();
                }
            });
        }

        if (driverMarkAllRead) {
            driverMarkAllRead.addEventListener('click', async function(e) {
                e.preventDefault();
                await markDriverNotificationsRead([]);
                driverNotificationState.items = driverNotificationState.items.map(function(note) {
                    note.is_read = true;
                    return note;
                });
                driverNotificationState.unreadCount = 0;
                renderDriverNotificationPanel();
            });
        }

        if (driverClearAll) {
            driverClearAll.addEventListener('click', async function(e) {
                e.preventDefault();

                const ok = window.confirm('Clear all notifications? This cannot be undone.');
                if (!ok) return;

                await clearDriverNotifications();
                driverNotificationState.items = [];
                driverNotificationState.unreadCount = 0;
                renderDriverNotificationPanel();
            });
        }

        if (driverNotificationList) {
            driverNotificationList.addEventListener('click', async function(e) {
                const row = e.target.closest('[data-driver-note-id]');
                if (!row) return;

                const id = row.getAttribute('data-driver-note-id');
                const note = driverNotificationState.items.find(function(item) {
                    return String(item.id) === String(id);
                });

                if (!note || note.is_read) return;

                note.is_read = true;
                driverNotificationState.unreadCount = Math.max(0, driverNotificationState.unreadCount - 1);
                renderDriverNotificationPanel();

                if (/^\d+$/.test(String(id))) {
                    await markDriverNotificationsRead([Number(id)]);
                }
            });
        }

        document.addEventListener('click', function(e) {
            if (!driverNotificationDropdown || !driverNotificationButton) return;
            if (driverNotificationDropdown.classList.contains('hidden')) return;

            if (!e.target.closest('#driverNotificationDropdown') && !e.target.closest('#driverNotificationButton')) {
                driverNotificationDropdown.classList.add('hidden');
            }
        });

        // Load initial notification history for panel state.
        fetchDriverNotifications();
        
        // Job action handlers
        function acceptJob(bookingId) {
            console.log('Accepting job:', bookingId);
            console.log('CSRF Token:', window.Laravel.csrfToken);

            // Provide immediate visual feedback while the request is processing.
            const button = (typeof event !== 'undefined' && event && event.target && event.target.closest)
                ? event.target.closest('button')
                : null;
            const originalButtonHtml = button ? button.innerHTML : null;
            if (button) {
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Accepting...';
            }
            
            const url = window.Laravel.driverAcceptUrl.replace(':id', bookingId);
            console.log('Request URL:', url);
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.success) {
                    showNotification(data.message, 'success');
                    // Redirect to dashboard to show updated counts
                    setTimeout(() => {
                        window.location.href = window.Laravel.driverDashboardUrl;
                    }, 1000);
                } else {
                    showNotification(data.error || 'Failed to accept job', 'error');
                    if (button) {
                        button.disabled = false;
                        button.innerHTML = originalButtonHtml || '<i class="fas fa-check mr-2"></i>Accept';
                    }
                }
            })
            .catch(error => {
                console.error('Accept job error:', error);
                showNotification('Error accepting job: ' + error.message, 'error');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = originalButtonHtml || '<i class="fas fa-check mr-2"></i>Accept';
                }
            });
        }
        
        function declineJob(bookingId) {
            if (confirm('Are you sure you want to decline this job?')) {
                console.log('Declining job:', bookingId);
                
                const url = window.Laravel.driverDeclineUrl.replace(':id', bookingId);
                console.log('Request URL:', url);
                
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    console.log('Response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        showNotification(data.message, 'success');
                        // Redirect to dashboard to show updated counts
                        setTimeout(() => {
                            window.location.href = window.Laravel.driverDashboardUrl;
                        }, 1000);
                    } else {
                        showNotification(data.error || 'Failed to decline job', 'error');
                    }
                })
                .catch(error => {
                    console.error('Decline job error:', error);
                    showNotification('Error declining job: ' + error.message, 'error');
                });
            }
        }

        // Real-time notifications using Server-Sent Events (no polling!)
        window.Laravel.driverNotificationsUrl = '{{ route('driver.notifications.list') }}';
        window.Laravel.driverNewJobsUrl = '{{ route('driver.jobs.new') }}?partial=1';
        window.Laravel.driverAcceptedJobsUrl = '{{ route('driver.jobs.accepted') }}?partial=1';
        window.Laravel.driverDashboardCountsUrl = '{{ route('driver.dashboard.counts') }}';

        // Map/Open directions helper (used by jobs/views)
        (function(){
            try {
                if (window._driver_map_links_attached) return; window._driver_map_links_attached = true;

                function openWithDestination(dest){
                    var url = 'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(dest) + '&travelmode=driving';
                    window.open(url, '_blank');
                }

                document.addEventListener('click', function(e){
                    try {
                        var el = e.target && e.target.closest ? e.target.closest('.js-open-directions') : null;
                        if (!el) return;
                        e.preventDefault();

                        var dest = el.dataset && el.dataset.destination;
                        if (!dest) { if (typeof showNotification === 'function') showNotification('Address not available'); return; }

                        if (!navigator.geolocation) {
                            if (typeof showNotification === 'function') showNotification('Location unavailable — opening destination only');
                            openWithDestination(dest);
                            return;
                        }

                        if (typeof showNotification === 'function') showNotification('Requesting your location...');

                        navigator.geolocation.getCurrentPosition(function(pos){
                            try {
                                var lat = pos.coords.latitude, lng = pos.coords.longitude;
                                var url = 'https://www.google.com/maps/dir/?api=1&origin=' + lat + ',' + lng + '&destination=' + encodeURIComponent(dest) + '&travelmode=driving';
                                window.open(url, '_blank');
                            } catch (sce) { console.error('Failed to open maps with origin', sce); openWithDestination(dest); }
                        }, function(err){
                            console.warn('Geolocation failed or denied:', err);
                            if (typeof showNotification === 'function') showNotification('Location permission denied — opening destination only');
                            openWithDestination(dest);
                        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
                    } catch (inner) { console.error('Driver map link handler error', inner); }
                });
            } catch (err) { console.error('Failed to initialize driver map links', err); }
        })();

        let eventSource = null;
        let reconnectTimeout = null;

        function connectNotificationStream() {
            if (eventSource) {
                eventSource.close();
            }

            eventSource = new EventSource('{{ route("driver.notifications.stream") }}');

            // Handler that processes each notification only ONCE
            var __driverIsRefreshing = false;
            eventSource.addEventListener('notification', function(e) {
                try {
                    const notification = JSON.parse(e.data);
                    
                    // Skip if we've already processed this notification
                    if (processedDriverNotificationIds.has(String(notification.id))) {
                        console.log('Driver notification', notification.id, 'already processed, skipping');
                        return;
                    }
                    
                    // Mark this notification as processed
                    processedDriverNotificationIds.add(String(notification.id));
                    console.log('Processing NEW driver notification:', notification.id);

                    // Add incoming notification to persistent panel
                    prependDriverNotification(notification);

                    // ONE-TIME refresh for this notification (no debouncing)
                    if (__driverIsRefreshing) {
                        console.log('Driver already refreshing, skipping this notification refresh');
                        return;
                    }
                    __driverIsRefreshing = true;

                    fetch(window.Laravel.driverDashboardCountsUrl, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.Laravel.csrfToken } })
                    .then(resp => resp.json())
                    .then(json => {
                        if (json && json.success && json.counts) {
                            updateCounts(json.counts);

                            // Refresh the active jobs list depending on current page
                            if (window.location.pathname.indexOf('/driver/jobs/accepted') !== -1) {
                                // Skip refresh on accepted jobs page - handled by individual AJAX calls
                                console.log('On accepted jobs page - skipping SSE refresh to prevent conflicts with AJAX updates');
                            }
                            if (window.location.pathname.indexOf('/driver/jobs/new') !== -1) {
                                // If the driver is on the New Jobs page, refresh it so assigned/unassigned jobs are removed immediately
                                refreshNewJobsList();
                            }

                            // Skip general refresh on accepted jobs page to prevent conflicts with AJAX
                            if (window.location.pathname.indexOf('/driver/jobs/accepted') === -1) {
                                try { if (typeof refreshAcceptedJobsList === 'function') refreshAcceptedJobsList(); } catch(e) {}
                            }
                        }
                    }).catch(err => { console.error('Failed to refresh driver counts', err); })
                    .finally(function(){ 
                        __driverIsRefreshing = false; 
                        console.log('Driver refresh completed for notification:', notification.id);
                    });

                } catch (err) {
                    console.error('Error handling notification:', err);
                }
            });

            eventSource.onerror = function(err) {
                console.log('SSE connection error, reconnecting...');
                eventSource.close();
                // Reconnect after 5 seconds
                reconnectTimeout = setTimeout(connectNotificationStream, 5000);
            };
        }

        function updateCounts(counts) {
            const elNew = document.getElementById('new-jobs-count');
            const elQuick = document.getElementById('new-jobs-quick-count');
            const elAcc = document.getElementById('accepted-jobs-count');
            const elComp = document.getElementById('completed-jobs-count');
            const elDec = document.getElementById('declined-jobs-count');
            
            if (elNew) elNew.textContent = counts.new;
            if (elQuick) elQuick.textContent = counts.new;
            if (elAcc) elAcc.textContent = counts.accepted;
            if (elComp) elComp.textContent = counts.completed;
            if (elDec) elDec.textContent = counts.declined;
        }

        function refreshAcceptedJobsList() {
            fetch(window.Laravel.driverAcceptedJobsUrl, { headers: { 'Accept': 'text/html' } })
                .then(r => r.text())
                .then(html => {
                    try {
                        // If the response is a full HTML page (login/error), skip refresh
                        if (/<!doctype|<html|<body/i.test(html)) {
                            console.warn('refreshAcceptedJobsList: full page HTML detected, skipping refresh');
                            return;
                        }

                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('accepted-jobs-container');
                        if (newContainer) {
                            const old = document.getElementById('accepted-jobs-container');
                            if (old) old.innerHTML = newContainer.innerHTML;
                        } else {
                            // Skip reload fallback - let AJAX handle updates
                            console.log('Could not find accepted-jobs-container, skipping update');
                        }
                    } catch (err) {
                        console.error('Failed to parse/replace accepted jobs list', err);
                        // Skip reload fallback - let AJAX handle updates
                    }
                })
                .catch(err => { console.error('Failed to refresh accepted jobs list', err); });
        }

        function refreshNewJobsList() {
            fetch(window.Laravel.driverNewJobsUrl, { headers: { 'Accept': 'text/html' } })
                .then(r => r.text())
                .then(html => {
                    try {
                        if (/<!doctype|<html|<body/i.test(html)) {
                            console.warn('refreshNewJobsList: full page HTML detected, skipping refresh');
                            return;
                        }

                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const newContainer = doc.getElementById('jobs-list-container');
                        if (newContainer) {
                            const old = document.getElementById('jobs-list-container');
                            if (old) old.innerHTML = newContainer.innerHTML;
                        } else {
                            // Skip reload fallback
                            console.log('Could not find jobs-list-container, skipping update');
                        }
                    } catch (err) {
                        console.error('Failed to parse/replace new jobs list', err);
                        // Skip reload fallback
                    }
                })
                .catch(err => { console.error('Failed to refresh new jobs list', err); });
        }

        // Start SSE connection
        connectNotificationStream();

        // ========================================
        // GPS Location Sharing (Global for all driver pages)
        // ========================================
        (function(){
            let driverLocationWatchId = null;
            let driverLocationInterval = null;
            let driverLocationSharingActive = false;

            function startDriverLocationSharing() {
                if (!navigator.geolocation) {
                    console.log('Geolocation not supported by browser');
                    return;
                }

                if (driverLocationSharingActive) {
                    console.log('Driver location sharing already active');
                    return;
                }

                driverLocationSharingActive = true;
                console.log('Starting GPS location sharing...');

                // Get initial position
                navigator.geolocation.getCurrentPosition(
                    sendDriverLocationUpdate,
                    handleDriverLocationError,
                    { enableHighAccuracy: true, timeout: 10000 }
                );

                // Watch for position changes
                driverLocationWatchId = navigator.geolocation.watchPosition(
                    sendDriverLocationUpdate,
                    handleDriverLocationError,
                    { enableHighAccuracy: true, maximumAge: 5000 }
                );

                // Also send location every 15 seconds as backup
                driverLocationInterval = setInterval(() => {
                    navigator.geolocation.getCurrentPosition(
                        sendDriverLocationUpdate,
                        handleDriverLocationError,
                        { enableHighAccuracy: true, timeout: 10000 }
                    );
                }, 15000);
            }

            function stopDriverLocationSharing() {
                if (driverLocationWatchId !== null) {
                    navigator.geolocation.clearWatch(driverLocationWatchId);
                    driverLocationWatchId = null;
                }
                if (driverLocationInterval !== null) {
                    clearInterval(driverLocationInterval);
                    driverLocationInterval = null;
                }
                driverLocationSharingActive = false;
                console.log('Driver location sharing stopped');
            }

            function sendDriverLocationUpdate(position) {
                const data = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude,
                    accuracy: position.coords.accuracy,
                    heading: position.coords.heading,
                    speed: position.coords.speed
                };

                fetch('{{ route("driver.location.update") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': window.Laravel.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        console.log('Driver location updated:', data.latitude.toFixed(4), data.longitude.toFixed(4));
                    }
                })
                .catch(error => {
                    console.error('Failed to send driver location:', error);
                });
            }

            function handleDriverLocationError(error) {
                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        console.error('Driver location permission denied');
                        break;
                    case error.POSITION_UNAVAILABLE:
                        console.error('Driver location unavailable');
                        break;
                    case error.TIMEOUT:
                        console.error('Driver location request timeout');
                        break;
                }
            }

            // Start location sharing after a short delay to let page load
            setTimeout(startDriverLocationSharing, 2000);

            // Cleanup on page unload
            window.addEventListener('beforeunload', stopDriverLocationSharing);
        })();

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (eventSource) eventSource.close();
            if (reconnectTimeout) clearTimeout(reconnectTimeout);
        });

        // ===== Dark / Light Mode Toggle =====
        (function(){
            var html = document.documentElement;
            var btn  = document.getElementById('theme-toggle');
            var icon = document.getElementById('theme-icon');

            function applyTheme(dark){
                if (dark) {
                    html.classList.add('dark');
                    icon.classList.replace('fa-moon', 'fa-sun');
                    btn.title = 'Switch to Light Mode';
                } else {
                    html.classList.remove('dark');
                    icon.classList.replace('fa-sun', 'fa-moon');
                    btn.title = 'Switch to Dark Mode';
                }
            }

            // Set correct icon on load
            applyTheme(html.classList.contains('dark'));

            btn.addEventListener('click', function(){
                var isDark = !html.classList.contains('dark');
                try { localStorage.setItem('driverTheme', isDark ? 'dark' : 'light'); } catch(e){}
                applyTheme(isDark);
            });
        })();
    </script>
    
    @yield('scripts')
</body>
</html>