<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') - AirportServices</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <style>
    /* Smooth page transition */
    body {
      transition: opacity 0.2s ease-in-out;
    }
  </style>
</head>
<body class="min-h-screen bg-gray-50 overflow-x-hidden">
  <!-- Sidebar for lg+ -->
    @include('admin.partials.sidebar')

    <!-- Mobile: top bar -->
    <div class="flex-1 min-h-screen lg:ml-20" id="mainContent">
      <header class="lg:hidden bg-white border-b">
        <div class="flex items-center justify-between px-4 py-3">
          <div class="flex items-center">
            <button id="sidebarToggle" class="mr-3 p-2 rounded hover:bg-gray-100">
              <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <img src="{{ asset('images/aero-cab-logo.png') }}" alt="logo" class="h-8" />
          </div>
          Broadcast
          <div>
            <a href="{{ route('admin.logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="text-sm text-gray-600">Logout</a>
          </div>
        </div>
      </header>

      <!-- Desktop top bar -->
      <header class="hidden lg:flex items-center justify-between bg-white border-b px-6 py-4">
        @php
          $broadcasts = $broadcasts ?? collect();
        @endphp
        @if($broadcasts->count() > 0)
        <div class="flex-1 flex items-center gap-3 mr-4 overflow-hidden">
          <div class="flex items-center gap-2 flex-shrink-0">
            <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path>
            </svg>
          </div>
          <div id="desktop-ticker-viewport" class="flex-1 overflow-hidden h-6 min-w-0">
            <div id="desktop-ticker-track" class="inline-block whitespace-nowrap"></div>
          </div>
        </div>
        @else
        <div class="flex-1"></div>
        @endif
        <div class="flex items-center gap-4">
          <!-- Notifications Bell -->
          <div class="relative">
            <button id="notificationButton" class="relative p-2 rounded hover:bg-gray-100">
              <svg class="h-6 w-6 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
              </svg>
              <span id="notificationBadge" class="hidden absolute -top-1 -right-1 h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center">0</span>
            </button>
            
            <!-- Notification Dropdown -->
            <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-80 bg-white border rounded-lg shadow-lg z-50">
              <div class="p-3 border-b bg-gray-50 rounded-t-lg">
                <div class="flex items-center justify-between">
                  <h3 class="font-semibold text-gray-900">Notifications</h3>
                  <button id="markAllRead" class="text-sm text-blue-600 hover:text-blue-800">Mark all read</button>
                </div>
              </div>
              <div id="notificationList" class="max-h-64 overflow-y-auto">
                <div class="p-4 text-center text-gray-500">
                  No new notifications
                </div>
              </div>
            </div>
          </div>
          <!-- desktop top-right sidebar toggle removed — using edge arrow instead -->

          <div class="relative">
            @php
              $user = auth()->user();
              $name = $user->name ?? 'User';
              $parts = preg_split('/\s+/', trim($name));
              $initials = strtoupper(substr(($parts[0] ?? 'U'), 0, 1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
            @endphp
            <button id="userMenuButton" class="flex items-center gap-2 px-3 py-2 rounded hover:bg-gray-100">
              @if($user && !empty($user->avatar))
                <img src="{{ $user->avatar }}" alt="{{ $name }}" class="h-8 w-8 rounded-full object-cover" />
              @else
                <div class="h-8 w-8 rounded-full bg-indigo-600 text-white flex items-center justify-center text-sm font-semibold">{{ $initials }}</div>
              @endif
              <span class="text-sm text-gray-700 ml-2 sidebar-label hidden">{{ $name }}</span>
              <svg class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div id="userMenu" class="hidden absolute right-0 mt-2 w-52 bg-white border rounded shadow-lg">
              <div class="py-1">
                <a href="{{ route('admin.notifications.index') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/></svg>
                  Notification Setting
                </a>
                <a href="#" id="userLogout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center gap-2">
                  <svg class="h-4 w-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                  Logout
                </a>
              </div>
            </div>
          </div>
        </div>
        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}" class="hidden" data-turbo="false">@csrf</form>
        <!-- Form used for idle auto-logout -->
        <form id="idleLogoutForm" method="POST" action="{{ route('admin.logout') }}" class="hidden" data-turbo="false">@csrf</form>
      </header>

      <main class="p-6">
        @yield('content')
      </main>

      {{-- Global modals (used by many admin pages) --}}
      @include('admin.pricing._modals')
    </div>
  </div>

  <script>
    // mobile sidebar toggle
    document.getElementById('sidebarToggle')?.addEventListener('click', function(){
      var sidebar = document.querySelector('aside[aria-label="Sidebar"]');
      if(!sidebar) return;
      sidebar.classList.toggle('hidden');
    });

    // user menu toggle (desktop)
    document.getElementById('userMenuButton')?.addEventListener('click', function(e){
      e.preventDefault();
      var menu = document.getElementById('userMenu');
      if (!menu) return;
      menu.classList.toggle('hidden');
    });

    document.getElementById('userLogout')?.addEventListener('click', function(e){ e.preventDefault(); document.getElementById('logout-form').submit(); });

    // close user menu when clicking outside
    document.addEventListener('click', function(e){ if (!e.target.closest('#userMenu') && !e.target.closest('#userMenuButton')) { var menu = document.getElementById('userMenu'); if (menu && !menu.classList.contains('hidden')) menu.classList.add('hidden'); } });

    // Desktop sidebar collapse/expand
    function setSidebarCollapsed(collapsed) {
      var aside = document.getElementById('sidebar');
      var mainContent = document.getElementById('mainContent');
      if (!aside) return;
      var labels = document.querySelectorAll('.sidebar-label');
      if (collapsed) {
        aside.classList.remove('w-64');
        aside.classList.add('w-20');
        labels.forEach(function(l){ l.classList.add('hidden'); });
        // hide username label in header
        document.querySelectorAll('.sidebar-label').forEach(function(l){ l.classList.add('hidden'); });
        if (mainContent) {
          mainContent.classList.remove('lg:ml-64');
          mainContent.classList.add('lg:ml-20');
        }
      } else {
        aside.classList.remove('w-20');
        aside.classList.add('w-64');
        labels.forEach(function(l){ l.classList.remove('hidden'); });
        if (mainContent) {
          mainContent.classList.remove('lg:ml-20');
          mainContent.classList.add('lg:ml-64');
        }
      }
      localStorage.setItem('sidebarCollapsed', collapsed ? '1' : '0');
    }

    // desktop toggle removed — edge toggle controls sidebar now

    function updateEdgeToggle(){
      var aside = document.getElementById('sidebar');
      var edge = document.getElementById('sidebarEdgeToggle');
      var icon = document.getElementById('sidebarEdgeIcon');
      if (!aside || !edge || !icon) return;
      var collapsed = localStorage.getItem('sidebarCollapsed') !== '0';
      if (collapsed){
        // show right-facing arrow
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>';
      } else {
        // left arrow
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>';
      }
      // show the toggle on large screens; it's positioned inside the sidebar so it stays attached
      if (window.innerWidth >= 1024) edge.classList.remove('hidden'); else edge.classList.add('hidden');
    }

    // ensure edge toggle updated on DOM ready
    document.addEventListener('DOMContentLoaded', updateEdgeToggle);


    // initialize sidebar collapsed state (default: collapsed)
    (function(){
      var val = localStorage.getItem('sidebarCollapsed');
      if (val === null) val = '1';
      setSidebarCollapsed(val === '1');

      // wire edge toggle click handler
      var edge = document.getElementById('sidebarEdgeToggle');
      if (edge) {
        edge.addEventListener('click', function(){
          var curr = localStorage.getItem('sidebarCollapsed');
          var collapsed = (curr !== '0');
          setSidebarCollapsed(!collapsed);
          updateEdgeToggle();
        });
      }

      // keep arrow icon/visibility updated when window resizes
      window.addEventListener('resize', updateEdgeToggle);

      // ensure edge toggle icon and visibility set initially
      updateEdgeToggle();

    })();

    // Auto-logout on idle with pre-logout warning modal
    (function(){
      var idleSeconds = {{ (int) config('session.idle_seconds') }}; // seconds
      var idleMs = idleSeconds * 1000;

      // Warning time: if idleSeconds >= 20 show warning 10s before; otherwise show at half the time
      var warningSeconds = idleSeconds >= 20 ? 10 : Math.max(1, Math.floor(idleSeconds / 2));
      var warningMs = warningSeconds * 1000;

      var idleTimer = null;
      var warningTimer = null;
      var countdownInterval = null;

      var modal = document.createElement('div');
      modal.innerHTML = `
        <div id="idle-warning-modal" class="fixed inset-0 z-50 flex items-center justify-center hidden">
          <div class="fixed inset-0 bg-black bg-opacity-50"></div>
          <div class="bg-white rounded-lg shadow-lg p-6 z-60 max-w-md mx-4">
            <h3 class="text-lg font-semibold mb-2">You will be logged out soon</h3>
            <p class="text-sm text-gray-600 mb-4">You have been idle. You will be logged out in <span id="idle-countdown">${warningSeconds}</span> seconds.</p>
            <div class="flex justify-end">
              <button id="idle-stay-button" class="px-4 py-2 mr-2 bg-gray-200 rounded">Stay signed in</button>
              <button id="idle-logout-button" class="px-4 py-2 bg-red-600 text-white rounded">Logout now</button>
            </div>
          </div>
        </div>
      `;
      document.body.appendChild(modal);

      var showModal = function() {
        document.getElementById('idle-warning-modal').classList.remove('hidden');
        var remaining = warningSeconds;
        var countdownEl = document.getElementById('idle-countdown');
        if (countdownInterval) clearInterval(countdownInterval);
        countdownEl.textContent = remaining;
        countdownInterval = setInterval(function(){
          remaining -= 1;
          countdownEl.textContent = remaining;
          if (remaining <= 0) {
            clearInterval(countdownInterval);
          }
        }, 1000);
      };

      var hideModal = function() {
        document.getElementById('idle-warning-modal').classList.add('hidden');
        if (countdownInterval) clearInterval(countdownInterval);
      };

      function performLogout() {
        var f = document.getElementById('idleLogoutForm');
        if (f) {
          f.submit();
        } else {
          fetch("{{ route('admin.logout') }}", {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '' ,
              'Content-Type': 'application/json'
            },
            credentials: 'same-origin'
          }).finally(function(){ window.location = '/'; });
        }
      }

      function startTimers() {
        // clear existing
        if (idleTimer) clearTimeout(idleTimer);
        if (warningTimer) clearTimeout(warningTimer);
        hideModal();

        // schedule warning and logout
        if (idleMs <= warningMs) {
          // if warning period is longer than total, just warn immediately
          warningTimer = setTimeout(function(){ showModal(); }, 0);
          idleTimer = setTimeout(function(){ performLogout(); }, idleMs);
        } else {
          warningTimer = setTimeout(function(){ showModal(); }, idleMs - warningMs);
          idleTimer = setTimeout(function(){ performLogout(); }, idleMs);
        }
      }

      // attach buttons
      document.addEventListener('click', function(e){
        if (e.target && e.target.id === 'idle-stay-button') {
          // user wants to stay signed in
          hideModal();
          startTimers();
        }
        if (e.target && e.target.id === 'idle-logout-button') {
          performLogout();
        }
      });

      // events that indicate activity
      ['mousemove','mousedown','keydown','touchstart','scroll','click'].forEach(function(evt){
        window.addEventListener(evt, startTimers, true);
      });

      // start timers
      startTimers();

      // debug: expose idleSeconds in console
      console.log('[IdleLogout] idle seconds:', idleSeconds);
    })();

    // Ticker Animation Script (Desktop Header)
    @php
      $broadcasts = $broadcasts ?? collect();
    @endphp
    @if($broadcasts->count() > 0)
    (function(){
      var track = document.getElementById('desktop-ticker-track');
      var viewport = document.getElementById('desktop-ticker-viewport');
      if (!track || !viewport) return;

      // Build concatenated items
      var broadcasts = @json($broadcasts);
      var itemsHtml = '';
      broadcasts.forEach(function(b){
        var title = (b.title || '').toString().trim();
        var msg = (b.message || '').toString().replace(/\n+/g,' ').trim();
        itemsHtml += '<span class="inline-block px-8 text-sm font-medium text-gray-700">' +
                      escapeHtml(title ? title + ' — ' + msg : msg) + '</span>';
      });
      if (!itemsHtml) itemsHtml = '<span class="inline-block px-8 text-sm font-medium text-gray-700">No announcements</span>';

      // Duplicate content to allow continuous scroll
      track.innerHTML = itemsHtml + itemsHtml;

      // Ensure style tag for animation exists
      var styleEl = document.getElementById('desktop-ticker-style');
      if (!styleEl) { styleEl = document.createElement('style'); styleEl.id = 'desktop-ticker-style'; document.head.appendChild(styleEl); }

      function startTicker(){
        // remove any previous animation
        track.style.animation = 'none';
        // small timeout to ensure layout measurements are correct
        setTimeout(function(){
          var trackWidth = track.offsetWidth / 2; // single sequence width
          var viewWidth = viewport.offsetWidth;
          var pxPerSecond = 80; // speed (slower for better readability)
          var duration = Math.max(10, (trackWidth + viewWidth) / pxPerSecond);

          // create keyframes
          styleEl.textContent = "@keyframes desktopMarqueeAnim { from { transform: translateX(0); } to { transform: translateX(-" + trackWidth + "px); } }";

          track.style.display = 'inline-block';
          track.style.willChange = 'transform';
          track.style.animation = 'desktopMarqueeAnim ' + duration + 's linear infinite';
        }, 50);
      }

      startTicker();
      window.addEventListener('resize', function(){ startTicker(); });

      // Pause on hover
      viewport.addEventListener('mouseenter', function(){
        track.style.animationPlayState = 'paused';
      });
      viewport.addEventListener('mouseleave', function(){
        track.style.animationPlayState = 'running';
      });

      function escapeHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    })();
    @endif

    // Old Ticker Animation Script (Legacy - kept for reference)
    @php
      $broadcasts = $broadcasts ?? collect();
    @endphp
    @if($broadcasts->count() > 0)
    (function(){
      var track = document.getElementById('ticker-track');
      var viewport = document.getElementById('ticker-viewport');
      if (!track || !viewport) return;

      // Build concatenated items
      var broadcasts = @json($broadcasts);
      var itemsHtml = '';
      broadcasts.forEach(function(b){
        var title = (b.title || '').toString().trim();
        var msg = (b.message || '').toString().replace(/\n+/g,' ').trim();
        itemsHtml += '<span class="inline-block px-6 text-sm text-indigo-900">' +
                      escapeHtml(title) + ' — ' + escapeHtml(msg) + '</span>';
      });
      if (!itemsHtml) itemsHtml = '<span class="inline-block px-6 text-sm text-indigo-900">No announcements</span>';

      // Duplicate content to allow continuous scroll
      track.innerHTML = itemsHtml + itemsHtml;

      // Ensure style tag for animation exists
      var styleEl = document.getElementById('ticker-style');
      if (!styleEl) { styleEl = document.createElement('style'); styleEl.id = 'ticker-style'; document.head.appendChild(styleEl); }

      function startTicker(){
        // remove any previous animation
        track.style.animation = 'none';
        // small timeout to ensure layout measurements are correct
        setTimeout(function(){
          var trackWidth = track.offsetWidth / 2; // single sequence width
          var viewWidth = viewport.offsetWidth;
          var pxPerSecond = 100; // speed
          var duration = Math.max(8, (trackWidth + viewWidth) / pxPerSecond);

          // create keyframes
          styleEl.textContent = "@keyframes marqueeAnim { from { transform: translateX(0); } to { transform: translateX(-" + trackWidth + "px); } }";

          track.style.display = 'inline-block';
          track.style.willChange = 'transform';
          track.style.animation = 'marqueeAnim ' + duration + 's linear infinite';
        }, 50);
      }

      startTicker();
      window.addEventListener('resize', function(){ startTicker(); });

      // Toggle pause/resume button
      var toggle = document.getElementById('ticker-toggle');
      var isPaused = false; // initial state: running
      if (toggle) {
        toggle.addEventListener('click', function(){
          isPaused = !isPaused;
          track.style.animationPlayState = isPaused ? 'paused' : 'running';
          toggle.textContent = isPaused ? 'Resume' : 'Pause';
        });
      }

      // Pause on hover
      viewport.addEventListener('mouseenter', function(){
        track.style.animationPlayState = 'paused';
      });
      viewport.addEventListener('mouseleave', function(){
        if (!isPaused) track.style.animationPlayState = 'running';
      });

      function escapeHtml(s){ return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
    })();
    @endif

    // Notification System
    (function(){
      var notificationButton = document.getElementById('notificationButton');
      var notificationDropdown = document.getElementById('notificationDropdown');
      var notificationBadge = document.getElementById('notificationBadge');
      var notificationList = document.getElementById('notificationList');
      var markAllRead = document.getElementById('markAllRead');
      
      if (!notificationButton || !notificationDropdown) return;

      // Toggle notification dropdown
      notificationButton.addEventListener('click', function(e) {
        e.preventDefault();
        notificationDropdown.classList.toggle('hidden');
        if (!notificationDropdown.classList.contains('hidden')) {
          loadNotifications();
        }
      });

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
          notificationDropdown.classList.add('hidden');
        }
      });

      // Mark all as read
      markAllRead.addEventListener('click', function(e) {
        e.preventDefault();
        markNotificationsAsRead();
      });

      // Load notifications from server
      function loadNotifications() {
        fetch('{{ route("admin.notifications.unread") }}', {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          updateNotificationUI(data);
        })
        .catch(error => {
          console.error('Error loading notifications:', error);
        });
      }

      // Update notification UI
      function updateNotificationUI(data) {
        // Update badge
        if (data.count > 0) {
          notificationBadge.textContent = data.count;
          notificationBadge.classList.remove('hidden');
        } else {
          notificationBadge.classList.add('hidden');
        }

        // Update notification list
        if (data.notifications && data.notifications.length > 0) {
          var html = '';
          data.notifications.forEach(function(notification) {
            var date = new Date(notification.created_at);
            var timeAgo = getTimeAgo(date);
            html += `
              <div class="p-3 border-b hover:bg-gray-50">
                <div class="font-medium text-sm text-gray-900">${notification.title}</div>
                <div class="text-sm text-gray-600 mt-1">${notification.message}</div>
                <div class="text-xs text-gray-400 mt-1">${timeAgo}</div>
              </div>
            `;
          });
          notificationList.innerHTML = html;
        } else {
          notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">No new notifications</div>';
        }

        // update lastNotificationCount so we can detect new items on subsequent polls
        if (typeof window.lastNotificationCount === 'undefined') window.lastNotificationCount = 0;
        window.lastNotificationCount = data.count; 
      }

      // Show a small inline toast near the top-right
      function showInlineToast(message, durationMs){
        if (!message) return;
        var el = document.createElement('div');
        el.className = 'inline-toast fixed top-16 right-6 z-60 bg-indigo-600 text-white text-sm px-4 py-2 rounded shadow';
        el.textContent = message;
        document.body.appendChild(el);
        setTimeout(function(){ el.remove(); }, durationMs || 2000);
      }

      // Refresh bookings area on either dashboard or bookings page
      function refreshBookingsView(){
        // Dashboard bookings area
        if (typeof refreshBookingList === 'function' && document.getElementById('dashboard-bookings-list')){
          try { refreshBookingList(); return; } catch(e){ console.error('refreshBookingList failed', e); }
        }

        // Bookings page: determine current visible pane
        var activePane = document.querySelector('[data-pane]:not(.hidden)');
        if (!activePane) {
          // fallback: find first pane
          activePane = document.querySelector('[data-pane]');
        }
        if (!activePane) return;

        var key = activePane.getAttribute('data-pane');
        var container = document.getElementById('booking-' + key + '-container');
        if (!container) return;

        // reload content via partial endpoint
        var url = '{{ route('admin.bookings.index') }}?partial=1&section=' + encodeURIComponent(key);
        container.innerHTML = '<div class="p-4 text-gray-600">Refreshing…</div>';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
          .then(function(res){ if (!res.ok) return res.text().then(function(t){ throw new Error('Failed to load: ' + res.status + '\n' + t.slice(0,200)); }); return res.text(); })
          .then(function(html){ container.innerHTML = html; try { if (typeof runInjectedScripts === 'function') runInjectedScripts(container); } catch(e){}; try{ if (typeof attachPagination === 'function') attachPagination(container); if (typeof attachBookingViewButtons === 'function') attachBookingViewButtons(container); } catch(e){ console.error(e); }
            // small visual cue
            showInlineToast('Bookings updated', 1500);

            // Refresh tab badges counts
            try {
              fetch('{{ route('admin.bookings.counts') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function(r){ return r.json(); })
                .then(function(json){ if (json && json.counts) {
                  Object.keys(json.counts).forEach(function(k){
                    var el = document.querySelector('[data-count-for="' + k + '"]');
                    if (el) el.textContent = json.counts[k] || 0;
                  });
                } }).catch(function(){/* ignore */});
            } catch(e){ console.warn('Failed to refresh booking counts', e); }
          })
          .catch(function(err){ console.error('Refresh booking pane failed', err); });
      }

      // Mark notifications as read
      function markNotificationsAsRead() {
        fetch('{{ route("admin.notifications.mark-read") }}', {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            notificationBadge.classList.add('hidden');
            notificationList.innerHTML = '<div class="p-4 text-center text-gray-500">No new notifications</div>';
            
            // Trigger refresh of the booking list if on dashboard or bookings page
            if (typeof refreshBookingsView === 'function') {
              refreshBookingsView();
            } else if (typeof refreshBookingList === 'function') {
              refreshBookingList();
            }
          }
        })
        .catch(error => {
          console.error('Error marking notifications as read:', error);
        });
      }

      // Helper function to format time ago
      function getTimeAgo(date) {
        var now = new Date();
        var diff = Math.floor((now - date) / 1000); // seconds
        
        if (diff < 60) return 'Just now';
        if (diff < 3600) return Math.floor(diff / 60) + ' minutes ago';
        if (diff < 86400) return Math.floor(diff / 3600) + ' hours ago';
        return Math.floor(diff / 86400) + ' days ago';
      }

      // Poll for new notifications every 5 seconds (faster so admin UI updates quickly)
      setInterval(function() {
        fetch('{{ route("admin.notifications.unread") }}', {
          method: 'GET',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        })
        .then(response => response.json())
        .then(data => {
          // Only update badge count without showing dropdown
          if (data.count > 0) {
            notificationBadge.textContent = data.count;
            notificationBadge.classList.remove('hidden');

            // If the count increased since last poll, refresh booking list and show browser notification
            if (typeof window.lastNotificationCount === 'undefined') window.lastNotificationCount = 0;
            if (data.count > window.lastNotificationCount) {
              // refresh booking UI to show updated driver responses/status
              if (typeof refreshBookingsView === 'function') {
                refreshBookingsView();
              } else if (typeof refreshBookingList === 'function') {
                refreshBookingList();
              }

              // Show browser notification for the latest item
              if ('Notification' in window && Notification.permission === 'granted') {
                if (data.notifications && data.notifications.length > 0) {
                  var latestNotification = data.notifications[0];
                  new Notification(latestNotification.title, {
                    body: latestNotification.message,
                    icon: '/images/logo.png'
                  });
                }
              }

              // Show an in-app toast indicating the list has been refreshed
              if (typeof showInlineToast === 'function') {
                showInlineToast('Bookings updated', 2500);
              }
            }

            // store latest count
            window.lastNotificationCount = data.count;
          } else {
            notificationBadge.classList.add('hidden');
            window.lastNotificationCount = 0;
          }
        })
        .catch(error => {
          console.error('Error polling notifications:', error);
        });
      }, 5000); // 5 seconds

      // Request notification permission
      if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
      }

      // Initial load
      loadNotifications();
    })();
  </script>
</body>
</html>