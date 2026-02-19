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

        /* ===== DARK MODE ===== */
        html.dark body  { background-color: #0f172a; color: #e2e8f0; }
        html.dark main  { background-color: #0f172a; }

        /* Nav */
        html.dark nav                        { background-color: #1e293b !important; border-color: #334155 !important; }
        html.dark nav h1                     { color: #e2e8f0 !important; }
        html.dark nav span                   { color: #cbd5e1 !important; }
        html.dark #theme-toggle              { border-color: #475569 !important; color: #94a3b8 !important; }
        html.dark #theme-toggle:hover        { background-color: #334155 !important; color: #e2e8f0 !important; }
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
            driverDashboardUrl: '{{ route('driver.dashboard') }}'
        };
        
        // Basic notification system
        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-md shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500' : 'bg-red-500'
            } text-white`;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Job action handlers
        function acceptJob(bookingId) {
            console.log('Accepting job:', bookingId);
            console.log('CSRF Token:', window.Laravel.csrfToken);
            
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
                }
            })
            .catch(error => {
                console.error('Accept job error:', error);
                showNotification('Error accepting job: ' + error.message, 'error');
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
        window.Laravel.driverNotificationsUrl = '{{ route('driver.notifications.unread') }}';
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
        let processedDriverNotificationIds = new Set(); // Track processed notifications

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
                    if (processedDriverNotificationIds.has(notification.id)) {
                        console.log('Driver notification', notification.id, 'already processed, skipping');
                        return;
                    }
                    
                    // Mark this notification as processed
                    processedDriverNotificationIds.add(notification.id);
                    console.log('Processing NEW driver notification:', notification.id);

                    // Immediate lightweight feedback
                    if (typeof showNotification === 'function') showNotification(notification.message, 'success');

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