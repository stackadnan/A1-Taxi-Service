<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Airport Services') }} - Driver Portal</title>
    
    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Font Awesome -->
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
            transition: all 0.3s ease;
        }
        
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--bg-color), var(--bg-color-end));
            border: none;
            color: white;
            min-height: 120px;
        }
        
        .new-jobs { --bg-color: #2563eb; --bg-color-end: #1d4ed8; }
        .accepted-jobs { --bg-color: #f59e0b; --bg-color-end: #d97706; }
        .completed-jobs { --bg-color: #10b981; --bg-color-end: #059669; }
        .declined-jobs { --bg-color: #ef4444; --bg-color-end: #dc2626; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Navigation Header -->
    <nav class="bg-white shadow-sm border-b border-gray-200 pt-4 sm:pt-0" style="padding-top: env(safe-area-inset-top, 0px);">
        <div class="max-w-7xl mx-auto mt-5 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo/Title -->
                <div class="flex items-center">
                    <h1 class="text-xl font-semibold text-gray-800">
                        @yield('title', 'Driver Portal')
                    </h1>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ auth('driver')->user()->name }}</span>
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
    </script>
    
    @yield('scripts')
</body>
</html>