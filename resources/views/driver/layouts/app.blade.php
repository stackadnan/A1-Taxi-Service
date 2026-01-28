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
    <nav class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
                        <button type="submit" class="text-gray-600 hover:text-gray-800 text-sm">
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

        // Notification polling (checks for unread driver notifications every 10s)
        window.Laravel.driverNotificationsUrl = '{{ route('driver.notifications.unread') }}';
        window.Laravel.driverNewJobsUrl = '{{ route('driver.jobs.new') }}?partial=1';

        (function pollDriverNotifications(){
            try {
                fetch(window.Laravel.driverNotificationsUrl, {
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.Laravel.csrfToken }
                }).then(r => r.json()).then(data => {
                    if (!data || !data.count) return;

                    // show each notification and update counts
                    const count = data.count || 0;
                    if (count > 0) {
                        (data.notifications || []).forEach(n => {
                            showNotification(n.message, 'success');
                        });

                        // update counts on dashboard
                        const el = document.getElementById('new-jobs-count');
                        const quick = document.getElementById('new-jobs-quick-count');
                        if (el) el.textContent = (parseInt(el.textContent || '0', 10) + count);
                        if (quick) quick.textContent = (parseInt(quick.textContent || '0', 10) + count);

                        // If on new jobs page, refresh the list via AJAX
                        if (window.location.pathname.indexOf('/driver/jobs/new') !== -1) {
                            fetch(window.Laravel.driverNewJobsUrl, { headers: { 'Accept': 'text/html' } })
                                .then(resp => resp.text())
                                .then(html => {
                                    const container = document.getElementById('page-content');
                                    if (container) container.innerHTML = html;
                                });
                        }
                    }
                }).catch(() => {});
            } catch (e) { /* ignore */ }
            setTimeout(pollDriverNotifications, 10000);
        })();
    </script>
    
    @yield('scripts')
</body>
</html>