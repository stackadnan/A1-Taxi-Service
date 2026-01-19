@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
  <!-- Panels side-by-side -->
  <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-4 rounded-lg bg-white border shadow-sm">
      <h3 class="font-semibold mb-3 text-lg text-red-600">Urgent Attention <span class="ml-2 inline-block bg-red-600 text-white text-xs px-2 py-0.5 rounded">3</span></h3>
      <div class="overflow-auto">
        <table class="w-full text-sm table-fixed">
          <thead>
            <tr class="text-left text-xs text-gray-500">
              <th class="w-1/6 px-2 py-2">Booking ID</th>
              <th class="w-1/3 px-2 py-2">Pickup</th>
              <th class="w-1/3 px-2 py-2">Dropoff</th>
              <th class="w-1/3 px-2 py-2">Pickup Date/Time</th>
              <th class="w-1/6 px-2 py-2">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-t">
              <td class="px-2 py-3">B12345</td>
              <td class="px-2 py-3">Heathrow Arrivals</td>
              <td class="px-2 py-3">City Centre</td>
              <td class="px-2 py-3">19:00 01/14/2026</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">POB</span></td>
            </tr>
            <tr class="border-t">
              <td class="px-2 py-3">B12346</td>
              <td class="px-2 py-3">Gatwick</td>
              <td class="px-2 py-3">Airport Hotel</td>
              <td class="px-2 py-3">14:00 01/15/2026</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700">Waiting</span></td>
            </tr>
            <tr class="border-t">
              <td class="px-2 py-3">B12347</td>
              <td class="px-2 py-3">City Terminal</td>
              <td class="px-2 py-3">Heathrow Short Stay</td>
              <td class="px-2 py-3">13:00 01/17/2026</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-red-100 text-red-700">Urgent</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <div class="p-4 rounded-lg bg-white border shadow-sm">
      <h3 class="font-semibold mb-3 text-lg">Driver Status</h3>
      <div class="overflow-auto" style="max-height:240px;">
        <table class="w-full text-sm table-fixed">
          <thead>
            <tr class="text-left text-xs text-gray-500">
              <th class="w-1/4 px-2 py-2">Driver</th>
              <th class="w-1/4 px-2 py-2">Booking</th>
              <th class="w-1/4 px-2 py-2">Status</th>
              <th class="w-1/4 px-2 py-2">Since</th>
              <th class="w-1/4 px-2 py-2">Track</th>
            </tr>
          </thead>
          <tbody>
            <tr class="border-t">
              <td class="px-2 py-3">Driver A</td>
              <td class="px-2 py-3">D102198</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-green-100 text-green-700">On Route</span></td>
              <td class="px-2 py-3">00:12:34</td>
              <td class="px-2 py-3">📍</td>
            </tr>
            <tr class="border-t">
              <td class="px-2 py-3">Driver B</td>
              
              <td class="px-2 py-3">None</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-700">Idle</span></td>
              <td class="px-2 py-3">00:45:10</td>
              <td class="px-2 py-3">📍</td>
            </tr>
            <tr class="border-t">
              <td class="px-2 py-3">Driver C</td>
              <td class="px-2 py-3">D102128</td>
              <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded bg-blue-100 text-blue-700">POB</span></td>
              <td class="px-2 py-3">00:05:20</td>
              <td class="px-2 py-3">📍</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Bookings area: full-width panel matching top panels -->
  <div class="mb-4">
    <div class="p-4 rounded-lg bg-white border shadow-md">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-xl font-semibold">Bookings</h2>
        <a href="{{ route('admin.bookings.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">
          View All Bookings →
        </a>
      </div>

      <div class="mb-4">
        <ul class="flex flex-wrap gap-3" role="tablist">
          @foreach($sections as $key => $label)
            @php $isActive = ($key === $active); @endphp
            <li>
              <a href="{{ route('admin.dashboard', array_merge(request()->except('page'), ['section' => $key])) }}" 
                 class="dashboard-tab px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $isActive ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" 
                 data-section="{{ $key }}" role="tab">
                {{ $label }}
                <span class="ml-2 inline-block {{ $isActive ? 'bg-white text-indigo-600' : 'bg-gray-100 text-gray-700' }} text-xs px-2 py-0.5 rounded">{{ $counts[$key] ?? 0 }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      </div>

      <div id="dashboard-bookings-list">
        @include('admin.bookings._list', ['bookings' => $bookings])
      </div>
    </div>

    <!-- Notes panel (editable area below bookings) - removed in favor of slider above -->
    <!-- (Kept for backward compatibility but hidden) -->
    <div class="mt-4 hidden">
      <div class="p-4 rounded-lg bg-white border shadow-sm">
        <h3 class="text-lg font-semibold mb-3">Admin Broadcast Message</h3>
        <div id="dashboard-notes" class="w-full border rounded p-3 h-32">Attention team, one of our staff members had an accident today and may be unavailable for a short time.

Please cooperate, stay alert, and ensure all duties continue smoothly during this period.</div>
      </div>
    </div>


  <script>
    (function(){
      // Booking tabs functionality
      var tabs = document.querySelectorAll('.dashboard-tab');
      var listContainer = document.getElementById('dashboard-bookings-list');

      function setActiveTab(el){
        tabs.forEach(function(t){ 
          t.classList.remove('bg-indigo-600','text-white','border-indigo-600','shadow-md'); 
          t.classList.add('bg-white','text-gray-700','border-gray-300');
          var badge = t.querySelector('span');
          if (badge) {
            badge.classList.remove('bg-white', 'text-indigo-600');
            badge.classList.add('bg-gray-100', 'text-gray-700');
          }
        });
        if (el) { 
          el.classList.add('bg-indigo-600','text-white','border-indigo-600','shadow-md'); 
          el.classList.remove('bg-white','text-gray-700','border-gray-300');
          var badge = el.querySelector('span');
          if (badge) {
            badge.classList.remove('bg-gray-100', 'text-gray-700');
            badge.classList.add('bg-white', 'text-indigo-600');
          }
        }
      }

      function attachPagination(container){
        var links = container.querySelectorAll('.pagination a');
        links.forEach(function(a){
          if (a.dataset.ajaxAttached) return; 
          a.dataset.ajaxAttached = '1';
          a.addEventListener('click', function(e){
            e.preventDefault();
            var href = a.getAttribute('href');
            if (!href) return;
            var section = document.querySelector('.dashboard-tab.bg-indigo-600')?.dataset.section || 'new';
            var url = href + (href.indexOf('?') === -1 ? '?' : '&') + 'partial=1&section=' + section;
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
              .then(function(r){ return r.text(); })
              .then(function(html){ 
                container.innerHTML = html; 
                attachPagination(container); 
                attachRowHandlers(container); 
              })
              .catch(function(err){ console.error('Pagination load failed', err); });
          });
        });
      }

      function attachRowHandlers(container){
        var rows = container.querySelectorAll('[data-booking-id]');
        rows.forEach(function(r){ 
          if (r.dataset.rowBound) return; 
          r.dataset.rowBound = '1'; 
          r.style.cursor = 'pointer';
          r.addEventListener('click', function(){ 
            var id = r.getAttribute('data-booking-id'); 
            if (!id) return; 
            window.location.href = '{{ route('admin.bookings.index') }}/' + id; 
          }); 
        });
      }

      function loadTab(url, tabEl){
        if (!listContainer) return;
        listContainer.innerHTML = '<div class="p-4 text-gray-600">Loading…</div>';
        var fetchUrl = url + (url.indexOf('?') === -1 ? '?' : '&') + 'partial=1';
        fetch(fetchUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
          .then(function(r){ return r.text(); })
          .then(function(html){
            listContainer.innerHTML = html;
            attachPagination(listContainer);
            attachRowHandlers(listContainer);
            setActiveTab(tabEl);
          })
          .catch(function(err){ 
            listContainer.innerHTML = '<div class="p-4 text-red-600">Failed to load bookings.</div>'; 
            console.error(err); 
          });
      }

      // Attach tab click handlers
      tabs.forEach(function(tab){ 
        tab.addEventListener('click', function(e){ 
          e.preventDefault(); 
          loadTab(tab.href, tab); 
        }); 
      });

      // Attach handlers to initial content
      attachPagination(listContainer);
      attachRowHandlers(listContainer);
    })();
  </script>
</div>
@endsection
