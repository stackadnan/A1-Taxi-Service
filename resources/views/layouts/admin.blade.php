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
  </script>
</body>
</html>