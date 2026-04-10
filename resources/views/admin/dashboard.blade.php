@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-8">
  <!-- Top charts -->
  <div class="mb-6 grid grid-cols-1 xl:grid-cols-2 gap-4">
    <div class="p-4 rounded-lg bg-white border shadow-sm" data-default-subtitle="Distribution of where bookings came from">
      <h3 class="font-semibold mb-1 text-lg">Booking Source (This Month)</h3>
      <p class="chart-subtitle text-xs text-gray-500 mb-3">Distribution of where bookings came from</p>
      <div class="h-72">
        <canvas id="bookingSourceChart"></canvas>
      </div>
    </div>

    <div class="p-4 rounded-lg bg-white border shadow-sm" data-default-subtitle="Jobs grouped by airport names in pickup/dropoff">
      <h3 class="font-semibold mb-1 text-lg">Airport Jobs (This Month)</h3>
      <p class="chart-subtitle text-xs text-gray-500 mb-3">Jobs grouped by airport names in pickup/dropoff</p>
      <div class="h-72">
        <canvas id="airportJobsChart"></canvas>
      </div>
    </div>
  </div>

  <!-- Panels side-by-side -->
  <div class="mb-6 grid grid-cols-1 md:grid-cols-2 gap-4">
    <div class="p-4 rounded-lg bg-white border shadow-sm">
      <h3 class="font-semibold mb-3 text-lg text-red-600">Urgent Attention <span class="ml-2 inline-block bg-red-600 text-white text-xs px-2 py-0.5 rounded">{{ $urgentAttentionCount ?? 0 }}</span></h3>
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
            @forelse(($urgentAttentionItems ?? collect()) as $item)
              <tr class="border-t">
                <td class="px-2 py-3">{{ $item['booking_id'] }}</td>
                <td class="px-2 py-3">{{ $item['pickup'] }}</td>
                <td class="px-2 py-3">{{ $item['dropoff'] }}</td>
                <td class="px-2 py-3">{{ $item['pickup_display'] }}</td>
                <td class="px-2 py-3"><span class="text-xs px-2 py-1 rounded {{ $item['status_class'] }}">{{ $item['status_label'] }}</span></td>
              </tr>
            @empty
              <tr class="border-t">
                <td colspan="5" class="px-2 py-3 text-center text-gray-500">No urgent bookings right now.</td>
              </tr>
            @endforelse
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
          @forelse($drivers as $d)
            <tr class="border-t">
              <td class="px-2 py-3">{{ $d->name }}</td>
              <td class="px-2 py-3">@if($d->current_booking){{ $d->current_booking->booking_code }}@else None @endif</td>
              <td class="px-2 py-3">
                @php
                  $bgColor = 'bg-gray-100 text-gray-700';
                  if ($d->status_color === 'green') $bgColor = 'bg-green-100 text-green-700';
                  elseif ($d->status_color === 'yellow') $bgColor = 'bg-yellow-100 text-yellow-700';
                  elseif ($d->status_color === 'orange') $bgColor = 'bg-orange-100 text-orange-700';
                  elseif ($d->status_color === 'blue') $bgColor = 'bg-blue-100 text-blue-700';
                  elseif ($d->status_color === 'purple') $bgColor = 'bg-purple-100 text-purple-700';
                  elseif ($d->status_color === 'red') $bgColor = 'bg-red-100 text-red-700';
                @endphp
                <span class="text-xs px-2 py-1 rounded {{ $bgColor }}">{{ $d->status_label }}</span>
              </td>
              <td class="px-2 py-3">{{ $d->status_since }}</td>
              <td class="px-2 py-3">
                @if($d->current_booking && in_array($d->status_label, ['POB','In Route','Arrived']))
                  <a href="{{ route('admin.drivers.track', ['driver' => $d->id, 'booking' => $d->current_booking->id]) }}" class="text-indigo-600 hover:text-indigo-800">📍</a>
                @else
                  <span class="text-gray-400">📍</span>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="5" class="px-4 py-3 text-center text-gray-600">No drivers available</td>
            </tr>
          @endforelse
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
                 class="dashboard-tab tab-link px-4 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $isActive ? 'bg-indigo-600 text-white border-indigo-600 shadow-md active' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}" 
                 data-section="{{ $key }}" role="tab">
                {{ $label }}
                <span class="ml-2 inline-block {{ $isActive ? 'bg-white text-indigo-600' : 'bg-gray-100 text-gray-700' }} text-xs px-2 py-0.5 rounded">{{ $counts[$key] ?? 0 }}</span>
              </a>
            </li>
          @endforeach
        </ul>
      </div>
    </div>

    <div id="dashboard-bookings-list">
      @include('admin.bookings._list', ['bookings' => $bookings, 'active' => $active])
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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    (function(){
      if (typeof Chart === 'undefined') return;

      var sourceCtx = document.getElementById('bookingSourceChart');
      var airportCtx = document.getElementById('airportJobsChart');
      var sourceChart = null;
      var airportChart = null;

      var initialSource = @json($bookingSourceChart ?? ['labels' => [], 'values' => [], 'is_dummy' => false]);
      var initialAirport = @json($airportJobsChart ?? ['labels' => [], 'values' => [], 'is_dummy' => false]);

      function setSubTitle(chartId, isDummy) {
        var chartEl = document.getElementById(chartId);
        if (!chartEl) return;
        var panel = chartEl.closest('.p-4.rounded-lg');
        if (!panel) return;

        var sub = panel.querySelector('.chart-subtitle');
        if (!sub) return;
        if (isDummy) {
          sub.textContent = 'Showing sample data until enough live bookings are available';
          sub.classList.remove('text-gray-500');
          sub.classList.add('text-amber-600');
        } else {
          sub.textContent = panel.dataset.defaultSubtitle || sub.textContent;
          sub.classList.remove('text-amber-600');
          sub.classList.add('text-gray-500');
        }
      }

      function applySourceData(payload) {
        if (!sourceChart || !payload) return;
        sourceChart.data.labels = payload.labels || [];
        sourceChart.data.datasets[0].data = payload.values || [];
        sourceChart.update();
        setSubTitle('bookingSourceChart', !!payload.is_dummy);
      }

      function applyAirportData(payload) {
        if (!airportChart || !payload) return;
        airportChart.data.labels = payload.labels || [];
        airportChart.data.datasets[0].data = payload.values || [];
        airportChart.update();
        setSubTitle('airportJobsChart', !!payload.is_dummy);
      }

      if (sourceCtx) {
        sourceChart = new Chart(sourceCtx, {
          type: 'doughnut',
          data: {
            labels: initialSource.labels || [],
            datasets: [{
              data: initialSource.values || [],
              backgroundColor: ['#2563eb','#7c3aed','#0891b2','#16a34a','#f59e0b','#dc2626','#6b7280','#14b8a6'],
              borderWidth: 1
            }]
          },
          options: {
            maintainAspectRatio: false,
            plugins: {
              legend: { position: 'bottom' }
            }
          }
        });
        setSubTitle('bookingSourceChart', !!initialSource.is_dummy);
      }

      if (airportCtx) {
        airportChart = new Chart(airportCtx, {
          type: 'bar',
          data: {
            labels: initialAirport.labels || [],
            datasets: [{
              label: 'Jobs',
              data: initialAirport.values || [],
              backgroundColor: '#4f46e5',
              borderRadius: 6,
              maxBarThickness: 36
            }]
          },
          options: {
            maintainAspectRatio: false,
            scales: {
              y: {
                beginAtZero: true,
                ticks: { precision: 0 }
              }
            },
            plugins: {
              legend: { display: false }
            }
          }
        });
        setSubTitle('airportJobsChart', !!initialAirport.is_dummy);
      }

      function refreshChartData() {
        fetch('{{ route('admin.dashboard.chart-data') }}', {
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          credentials: 'same-origin'
        })
          .then(function(r){ return r.json(); })
          .then(function(json){
            if (!json) return;
            applySourceData(json.bookingSourceChart);
            applyAirportData(json.airportJobsChart);
          })
          .catch(function(err){ console.warn('Chart refresh failed', err); });
      }

      setInterval(refreshChartData, 60000);
    })();

    (function(){
      // Booking tabs functionality
      var tabs = document.querySelectorAll('.dashboard-tab');
      var listContainer = document.getElementById('dashboard-bookings-list');

      function setActiveTab(el){
        tabs.forEach(function(t){ 
          t.classList.remove('bg-indigo-600','text-white','border-indigo-600','shadow-md','active'); 
          t.classList.add('bg-white','text-gray-700','border-gray-300');
          var badge = t.querySelector('span');
          if (badge) {
            badge.classList.remove('bg-white', 'text-indigo-600');
            badge.classList.add('bg-gray-100', 'text-gray-700');
          }
        });
        if (el) { 
          el.classList.add('bg-indigo-600','text-white','border-indigo-600','shadow-md','active'); 
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
            
            var activeTab = document.querySelector('.dashboard-tab.active') || 
                           document.querySelector('.dashboard-tab.bg-indigo-600') ||
                           document.querySelector('.tab-link.active');
            var section = activeTab ? activeTab.dataset.section : '{{ $active ?? "new" }}';
            
            var url = href;
            var hasQuery = url.indexOf('?') !== -1;
            var separator = hasQuery ? '&' : '?';
            
            if (url.indexOf('partial=1') === -1) {
              url += separator + 'partial=1';
              separator = '&';
            }
            
            if (url.indexOf('section=') === -1) {
              url += separator + 'section=' + section;
            }
            
            if (!listContainer) return;
            listContainer.innerHTML = '<div class="p-4 text-gray-600">Loading…</div>';
            
            fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
              .then(function(r){ return r.text(); })
              .then(function(html){ 
                listContainer.innerHTML = html; 
                attachPagination(listContainer); 
                attachBookingViewButtons(listContainer); 
              })
              .catch(function(err){ 
                console.error('Pagination load failed', err);
                listContainer.innerHTML = '<div class="p-4 text-red-600">Failed to load bookings.</div>'; 
              });
          });
        });
      }

      function attachBookingViewButtons(container){
        var buttons = container.querySelectorAll('.booking-view-button');
        buttons.forEach(function(btn){
          if (btn.dataset.bound) return; btn.dataset.bound='1';
          btn.addEventListener('click', function(e){
            e.preventDefault();
            if (typeof window.openPostcodeModal === 'function') {
              window.openPostcodeModal(btn.getAttribute('href'), btn.dataset.title || 'View Booking');
            } else {
              window.location = btn.getAttribute('href');
            }
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
            attachBookingViewButtons(listContainer);
            setActiveTab(tabEl);
          })
          .catch(function(err){ 
            listContainer.innerHTML = '<div class="p-4 text-red-600">Failed to load bookings.</div>'; 
            console.error(err); 
          });
      }

      tabs.forEach(function(tab){ 
        tab.addEventListener('click', function(e){ 
          e.preventDefault(); 
          loadTab(tab.href, tab); 
        }); 
      });

      attachPagination(listContainer);
      attachBookingViewButtons(listContainer);

      window.refreshBookingList = function() {
        var activeTab = document.querySelector('.dashboard-tab.active') || 
                       document.querySelector('.dashboard-tab.bg-indigo-600') ||
                       document.querySelector('.tab-link.active');
        if (activeTab) {
          loadTab(activeTab.href, activeTab);
        }

        try {
          fetch('{{ route('admin.bookings.counts') }}', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
            credentials: 'same-origin'
          })
            .then(function(r){ return r.json(); })
            .then(function(json){
              if (json && json.counts) {
                Object.keys(json.counts).forEach(function(k){
                  var el = document.querySelector('[data-count-for="' + k + '"]');
                  if (el) el.textContent = json.counts[k] || 0;
                });
              }
            })
            .catch(function(){ /* ignore */ });
        } catch(e){ console.warn('Failed to refresh booking counts', e); }
      };
    })();
  </script>
</div>
@endsection
