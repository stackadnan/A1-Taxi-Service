<aside id="sidebar" class="w-20 bg-white border-r hidden lg:block transition-all duration-200 fixed left-0 top-0 h-screen z-50" aria-label="Sidebar">
  <style>
    /* Prevent horizontal overflow on inner scroll wrapper and hide its scrollbars; allow overlay/button to overflow outside the sidebar */
    #sidebar { position: fixed; overflow-x: visible; }
    #sidebar .sidebar-scroll { overflow-x: hidden; -ms-overflow-style: none; scrollbar-width: none; }
    #sidebar .sidebar-scroll::-webkit-scrollbar { display: none; width: 0; height: 0; }
    /* Hover brick slide-out */
    #sidebar a.flex { position: relative; }
    #sidebar .sidebar-label {
      position: absolute;
      left: calc(100% + 10px);
      top: 50%;
      min-width: 220px;
      max-width: 340px;
      transform: translateX(-20px) translateY(-50%);
      background: #fff;
      padding: 12px 16px;
      border-radius: 6px;
      box-shadow: 0 12px 28px rgba(7,12,34,0.12);
      white-space: nowrap;
      opacity: 0;
      transition: transform .22s cubic-bezier(.18,.9,.32,1), opacity .18s ease, border-color .18s ease, box-shadow .18s ease;
      pointer-events: none;
      color: #0f172a;
      font-size: 14px;
      border: 2px solid transparent;
      z-index: 140;
      display: flex;
      align-items: center;
      gap: 10px;
    }
    #sidebar .sidebar-label::before {
      content: '';
      display: block;
      width: 8px;
      height: 36px;
      background: linear-gradient(180deg, #2563eb, #7c3aed);
      border-radius: 4px;
      margin-right: 10px;
      flex: 0 0 8px;
    }

    /* When the sidebar is expanded (w-64) show labels inline and remove floating behavior */
    #sidebar.w-64 .sidebar-label{
      position: static;
      left: auto;
      top: auto;
      transform: none;
      min-width: 0;
      max-width: none;
      background: transparent;
      padding: 0;
      border-radius: 0;
      box-shadow: none;
      white-space: nowrap;
      opacity: 1;
      transition: none;
      pointer-events: auto;
      color: #0f172a;
      font-size: 14px;
      border: none;
      z-index: auto;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      margin-left: 12px;
    }
    #sidebar.w-64 .sidebar-label::before { display: none; }

    #sidebar a.flex:hover .sidebar-label,
    #sidebar a.flex:focus .sidebar-label {
      display: flex !important;
      opacity: 1;
      transform: translateX(0) translateY(-50%);
      pointer-events: auto;
      border-color: #4f46e5;
      box-shadow: 0 18px 40px rgba(15,23,42,0.18);
    }

    /* When sidebar is expanded, keep label stable: don't move or change size on hover */
    #sidebar.w-64 a.flex:hover .sidebar-label,
    #sidebar.w-64 a.flex:focus .sidebar-label {
      transform: none !important;
      opacity: 1 !important;
      pointer-events: auto;
      border-color: transparent !important;
      box-shadow: none !important;
    }

    /* ensure hidden class doesn't permanently hide label when hovering */
    #sidebar .sidebar-label.hidden { display: flex !important; opacity: 0; pointer-events: none; }
    @media (max-width: 1023px) { #sidebar .sidebar-label { display: none !important; } }
  </style>

  <div class="h-full px-4 py-6 overflow-y-auto overflow-x-hidden sidebar-scroll">
    <div class="mb-6">
      <img src="{{ asset('images/aero-cab-logo.png') }}" alt="logo" class="h-10" />
    </div>

    <nav class="space-y-1">
      <a href="{{ route('admin.dashboard') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
        <span class="ml-3 sidebar-label">Dashboard</span>
      </a>

      <a href="{{ route('admin.bookings.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/bookings*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/bookings*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/><path d="m9 16 2 2 4-4"/></svg>
        <span class="ml-3 sidebar-label">Booking</span>
      </a>


      <a href="{{ route('admin.drivers.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/drivers*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/drivers*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/><circle cx="7" cy="17" r="2"/><path d="M9 17h6"/><circle cx="17" cy="17" r="2"/></svg>
        <span class="ml-3 sidebar-label">Driver Record</span>
      </a>


      <a href="{{ route('admin.pricing.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/pricing*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/pricing*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        <span class="ml-3 sidebar-label">Pricing Management</span>
      </a>


      <a href="{{ route('admin.reviews.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/reviews*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/reviews*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        <span class="ml-3 sidebar-label">Review & Concern</span>
      </a>

      @if(auth()->user() && auth()->user()->hasPermission('user.view'))
      <a href="{{ route('admin.users.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/users*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/users*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
        <span class="ml-3 sidebar-label">Users Management</span>
      </a>
      @endif

      <a href="{{ route('admin.accounts.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/accounts*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/accounts*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
        <span class="ml-3 sidebar-label">Accounts</span>
      </a>
    </nav>

    <!-- Admin Setting at bottom -->
    <div class="absolute bottom-6 left-0 right-0 px-4">
      <a href="{{ route('admin.settings.index') }}" class="flex items-center px-3 h-12 rounded relative hover:bg-gray-100 {{ Request::is('admin/settings*') ? 'bg-gray-100 font-semibold' : '' }}">
        <svg class="h-5 w-5 flex-shrink-0 {{ Request::is('admin/settings*') ? 'text-indigo-600' : 'text-gray-500' }}" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/></svg>
        <span class="ml-3 sidebar-label">Admin</span>
      </a>
    </div>


  </div>

  <!-- Edge toggle attached to sidebar (stays with it) -->
  <button id="sidebarEdgeToggle" aria-label="Toggle sidebar" class="hidden lg:flex absolute top-1/2 transform -translate-y-1/2 h-8 w-8 bg-white border rounded-full shadow z-50 items-center justify-center -right-4">
    <svg id="sidebarEdgeIcon" class="h-4 w-4 text-gray-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path id="arrowPath" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
    </svg>
  </button>

  <style>
    /* Runtime hover brick (overlay) - updated design and slide animation */
    .sidebar-hover-brick{
      position: fixed;
      left: 0; top: 0;
      /* start offset so it appears to slide out from the icon */
      transform: translateX(-18px) translateY(-50%);
      min-width: 220px; max-width: 380px;
      background: #fff; color: #0f172a; font-size:14px;
      padding: 10px 14px; border-radius:10px;
      box-shadow: 0 18px 40px rgba(15,23,42,0.12);
      border: 1px solid rgba(79,70,229,0.20);
      display: flex; align-items:center; gap:12px; z-index:999999;
      pointer-events:none; opacity:0;
      transition: transform .22s cubic-bezier(.2,.9,.3,1), opacity .14s ease, box-shadow .12s ease, border-color .12s ease;
      transform-origin: left center;
    }
    .sidebar-hover-brick.show{
      opacity:1;
      transform: translateX(0) translateY(-50%);
      box-shadow: 0 26px 50px rgba(15,23,42,0.16);
    }
    .sidebar-hover-brick .accent{ width:10px; height:36px; border-radius:6px; background: linear-gradient(180deg,#5b21b6,#7c3aed); flex:0 0 10px; }
    .sidebar-hover-brick .text{ font-weight:600; font-size:15px; color:#0f172a; white-space:nowrap; margin-left: 12px; }

    /* Hide overlay on narrow screens (labels already hidden there) */
    @media (max-width: 1023px){ .sidebar-hover-brick{ display:none !important; } }

    /* When overlay is active, change the sidebar icon color to purple instead of hiding it */
    #sidebar a.overlay-active svg { color: #7c3aed !important; transition: color .18s ease; }
  </style>

  <script>
    (function(){
      try{
        var sidebar = document.getElementById('sidebar'); if(!sidebar) return;
        var anchors = sidebar.querySelectorAll('a.flex');
        var overlay = null; var showTimeout=null, hideTimeout=null;

        function createOverlay(){
          overlay = document.createElement('div'); overlay.className = 'sidebar-hover-brick';
          // accent bar and text only (no icon)
          overlay.innerHTML = '<div class="accent"></div><div class="text"></div>';
          document.body.appendChild(overlay);
        }

        function isSidebarCollapsed(){
          try { var val = localStorage.getItem('sidebarCollapsed'); if (val === null) return true; return val === '1'; } catch(e){ return true; }
        }

        anchors.forEach(function(a){
          var labelElem = a.querySelector('.sidebar-label');
          var text = labelElem ? labelElem.textContent.trim() : (a.title || '');
          var svg = a.querySelector('svg'); var svgHtml = svg ? svg.outerHTML : '';

          a.addEventListener('pointerenter', function(){
            // only show overlay when sidebar is collapsed
            if (!isSidebarCollapsed()) return;
            // mark this anchor as overlay-active (hide its inline svg to avoid duplicate icons)
            a.classList.add('overlay-active');

            if (!overlay) createOverlay();
            clearTimeout(hideTimeout);
            showTimeout = setTimeout(function(){
              var rect = a.getBoundingClientRect();
              var left = rect.right + 12; var top = rect.top + rect.height/2;

              // set content
              overlay.querySelector('.text').textContent = text;

              // clamp horizontal position to avoid overflow (prevents scrollbar)
              var overlayWidth = overlay.offsetWidth || 260;
              var maxLeft = window.innerWidth - overlayWidth - 12;
              if (left > maxLeft) left = Math.max(12, maxLeft);

              // clamp vertical position so overlay never creates scrolling
              var overlayHeight = overlay.offsetHeight || 56;
              var minTop = overlayHeight/2 + 12;
              var maxTop = window.innerHeight - overlayHeight/2 - 12;
              if (top < minTop) top = minTop;
              if (top > maxTop) top = maxTop;

              // position and show
              overlay.style.left = left + 'px'; overlay.style.top = top + 'px';
              overlay.classList.add('show');
            }, 80);
          });
          a.addEventListener('pointerleave', function(){
            clearTimeout(showTimeout);
            // remove overlay-active class immediately so icon returns to grey
            a.classList.remove('overlay-active');
            hideTimeout = setTimeout(function(){ if(overlay) overlay.classList.remove('show'); }, 120);
          });
          // keyboard accessibility
          a.addEventListener('focus', function(){ a.dispatchEvent(new Event('pointerenter')); });
          a.addEventListener('blur', function(){ a.dispatchEvent(new Event('pointerleave')); });
        });
      }catch(e){ console.error('hover overlay attach failed', e); }
    })();
  </script>
</aside>