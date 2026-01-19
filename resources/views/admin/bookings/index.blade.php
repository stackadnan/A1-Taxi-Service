@extends('layouts.admin')

@section('title', 'Bookings')

@section('content')
<style>
  /* Prevent hover effects on active tabs only, not buttons */
  /* ul[role="tablist"] a.bg-indigo-200.text-white:hover {
    background-color: rgb(79 70 229) !important;
    color: white !important;
    border-color: rgb(79 70 229) !important;
  } */
</style>
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Bookings</h1>
    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['section' => 'new_manual'])) }}" 
       class="px-4 py-2 bg-indigo-600 text-white rounded-lg font-medium hover:bg-indigo-700 transition-all shadow-md hover:shadow-lg flex items-center gap-2"
       data-tab="new_manual">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
      </svg>
      Generate Booking
    </a>
  </div>

  <div class="mb-4">
    <ul class="flex flex-wrap gap-3" role="tablist">
      @foreach($sections as $key => $label)
        @if($key !== 'new_manual')
          @php $isActive = ($key === $active); @endphp
          <li>
            <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['section' => $key])) }}" class="px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $isActive ? 'border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" data-tab="{{ $key }}">
              {{ $label }}
              <span class="ml-2 inline-block {{ $isActive ? '' : 'bg-gray-100 text-gray-700' }} text-xs px-2 py-0.5 rounded">{{ $counts[$key] ?? 0 }}</span>
            </a>
          </li>
        @endif
      @endforeach
    </ul>
  </div>

  <div id="bookings-tabs">
    @php $keys = array_keys($sections); @endphp

    @foreach($keys as $key)
      @php $isActivePane = ($key === $active); @endphp
      <section data-pane="{{ $key }}" class="{{ $isActivePane ? 'tab-pane' : 'hidden tab-pane' }}">
        <h2 class="text-lg font-semibold mb-2">{{ $sections[$key] }}</h2>
        <div id="booking-{{ $key }}-container">
          <div class="text-gray-600">Loading {{ $sections[$key] }}…</div>
        </div>
      </section>
    @endforeach
  </div>

  <script>
  (function(){
    // Generic helpers
    function runInjectedScripts(container){
      try {
        var scripts = container.querySelectorAll('script');
        scripts.forEach(function(s){
          var ns = document.createElement('script');
          if (s.src) {
            ns.src = s.src; ns.async = false; document.head.appendChild(ns);
            ns.onload = function(){ console.debug('Loaded injected script', s.src); };
            ns.onerror = function(e){ console.error('Injected script failed to load', s.src, e); };
          } else {
            ns.text = s.textContent; document.head.appendChild(ns);
          }
          s.parentNode.removeChild(s);
        });
      } catch(e) { console.error('runInjectedScripts error', e); }
    }
    // Expose globally for modal responses
    if (typeof window.runInjectedScripts === 'undefined') window.runInjectedScripts = runInjectedScripts;

    function attachPagination(container){
      var links = container.querySelectorAll('.pagination a');
      links.forEach(function(a){
        if (a.dataset.ajaxAttached) return; a.dataset.ajaxAttached = '1';
        a.addEventListener('click', function(e){
          e.preventDefault();
          var href = a.getAttribute('href');
          if (!href) return;
          fetch(href + (href.indexOf('?') === -1 ? '?' : '&') + 'partial=1', { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){ return r.text(); }).then(function(html){ container.innerHTML = html; runInjectedScripts(container); attachPagination(container); attachRowHandlers(container); }).catch(function(err){ console.error('Pagination load failed', err); });
        });
      });
    }

    function attachRowHandlers(container){
      // rows should have data-booking-id attributes in partial view; attach click handler if present
      var rows = container.querySelectorAll('[data-booking-id]');
      rows.forEach(function(r){ if (r.dataset.rowBound) return; r.dataset.rowBound = '1'; r.addEventListener('click', function(){ var id = r.getAttribute('data-booking-id'); if (!id) return; window.location.href = '/admin/bookings/' + id; }); });
    }

    // Load a single booking pane via AJAX partial
    function loadSection(key){
      var container = document.getElementById('booking-' + key + '-container');
      if (!container) return Promise.resolve();
      if (container.dataset.loaded) return Promise.resolve();
      container.innerHTML = '<div class="p-4 text-gray-600">Loading…</div>';
      var url = '{{ route('admin.bookings.index') }}?partial=1&section=' + encodeURIComponent(key);
      return fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(res){ if (!res.ok) return res.text().then(function(t){ throw new Error('Failed to load: ' + res.status + '\n' + t.slice(0,200)); }); return res.text(); }).then(function(html){ container.innerHTML = html; container.dataset.loaded = '1'; runInjectedScripts(container); attachPagination(container); attachRowHandlers(container); return Promise.resolve(); }).catch(function(err){ console.error('Load section '+key+' error', err); container.innerHTML = '<div class="text-red-600">Failed to load. <button id="retry-'+key+'" class="ml-2 px-2 py-1 border rounded">Retry</button></div>'; var btn = document.getElementById('retry-'+key); if (btn) btn.addEventListener('click', function(){ loadSection(key); }); return Promise.resolve(); });
    }

    // preload all sections on first load, then hide via CSS (so switching is instant)
    var sections = @json(array_keys($sections));
    Promise.all(sections.map(function(k){ return loadSection(k); })).then(function(){ console.debug('All booking sections loaded'); }).catch(function(){ console.warn('Some booking sections failed to load'); });

    // Tab activation (show/hide panes similar to pricing)
    var tabs = document.querySelectorAll('ul[role="tablist"] [data-tab]');
    var panes = document.querySelectorAll('[data-pane]');
    function activate(tabName){
      tabs.forEach(function(t){
        if(t.getAttribute('data-tab') === tabName){
          t.classList.add('border-indigo-600','text-indigo-700');
          t.classList.remove('text-gray-700');
          t.setAttribute('aria-selected','true');
        } else {
          t.classList.remove('border-indigo-600','text-indigo-700');
          t.classList.add('text-gray-700');
          t.setAttribute('aria-selected','false');
        }
      });
      panes.forEach(function(p){ if(p.getAttribute('data-pane') === tabName) p.classList.remove('hidden'); else p.classList.add('hidden'); });
      // update querystring (so links remain meaningful)
      try { history.replaceState(null, '', '?section=' + tabName); } catch(e){}
    }

    tabs.forEach(function(t){ t.addEventListener('click', function(e){ e.preventDefault(); activate(t.getAttribute('data-tab')); }); });

    // Handle New Manual Booking button (it uses an anchor outside tab list)
    var manualBtn = document.querySelector('[data-tab="new_manual"]');
    // keep track of last non-manual active tab so we can return to it when toggling
    var lastNonManualTab = '{{ $active }}' === 'new_manual' ? (function(){ var t = document.querySelector('ul[role="tablist"] [data-tab]'); return t ? t.getAttribute('data-tab') : null; })() : '{{ $active }}';

    if (manualBtn) manualBtn.addEventListener('click', function(e){
      e.preventDefault();
      var activePane = document.querySelector('[data-pane]:not(.hidden)');
      var activeName = activePane ? activePane.getAttribute('data-pane') : null;

      if (activeName === 'new_manual') {
        // currently visible -> toggle hide: return to last non-manual tab if we have one
        if (lastNonManualTab && lastNonManualTab !== 'new_manual') {
          activate(lastNonManualTab);
        } else {
          // fallback: activate first available tab (non-manual)
          var firstTab = document.querySelector('ul[role="tablist"] [data-tab]');
          if (firstTab) {
            activate(firstTab.getAttribute('data-tab'));
          } else {
            // hide manual pane only
            var pane = document.querySelector('[data-pane="new_manual"]');
            if (pane) pane.classList.add('hidden');
          }
        }
      } else {
        // store last non-manual tab and show manual pane
        if (activeName && activeName !== 'new_manual') lastNonManualTab = activeName;
        activate('new_manual');
      }
    });

    // Set initial visibility based on server-provided active
    activate('{{ $active }}');
  })();
  </script>
</div>
@endsection
