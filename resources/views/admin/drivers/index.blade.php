@extends('layouts.admin')

@section('title', 'Drivers')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Drivers</h1>
    <a id="driver-create-button" data-title="Add Driver" href="{{ route('admin.drivers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">New Driver</a>
  </div>

  <div class="mb-4">
    <style>
      /* Keep active tab hover consistent */
      .driver-tab.bg-indigo-600.text-white:hover { background-color: rgb(79 70 229) !important; color: white !important; border-color: rgb(79 70 229) !important; }
      /* Keep tabs consistent and responsive */
      .driver-tab { min-width: 88px; padding-left: 0.75rem; padding-right: 0.75rem; display: inline-flex; align-items: center; justify-content: center; }
      .driver-tabs-row { display:flex; flex-wrap:wrap; gap:0.75rem; align-items:center; }
    </style>

    <div class="flex items-center justify-between">
      <div class="driver-tabs-row w-full">
        @php $activeTab = request('tab', 'active'); @endphp
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['tab' => 'active'])) }}" class="driver-tab px-5 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $activeTab=='active' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}">Active</a>
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['tab' => 'inactive'])) }}" class="driver-tab px-5 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $activeTab=='inactive' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}">Inactive</a>
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['tab' => 'documents'])) }}" class="driver-tab px-5 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $activeTab=='documents' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}">Documents</a>
        <a href="?{{ http_build_query(array_merge(request()->except('page'), ['tab' => 'status'])) }}" class="driver-tab px-5 py-2 rounded-lg border text-sm font-medium focus:outline-none transition-all {{ $activeTab=='status' ? 'bg-indigo-600 text-white border-indigo-600 shadow-md' : 'bg-white text-gray-700 border-gray-300 hover:bg-indigo-50 hover:border-indigo-500 hover:text-indigo-700 hover:shadow-sm' }}">Track Driver</a>
        </div>
      </div>

    <div class="mt-2">
      <h2 id="drivers-tab-title" class="text-lg font-semibold mb-2">
        @if($activeTab == 'active')
          Active Drivers
        @elseif($activeTab == 'inactive')
          Inactive Drivers
        @elseif($activeTab == 'status')
          Driver Status
        @else
          Drivers - Expiring / Expired Documents
        @endif
      </h2>
    </div>

    <div class="mt-4">
      <form method="get" action="{{ route('admin.drivers.index') }}" class="flex items-center gap-2 w-full md:w-1/2">
        <input type="text" name="q" value="{{ request('q') }}" placeholder="Search drivers by name, phone or plate" class="border rounded px-3 py-2" />
        <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
      </form>
    </div>



  <div id="drivers-container" class="mt-6 w-full">
    @if($activeTab == 'documents')
      @include('admin.drivers._documents', ['drivers' => $drivers])
    @elseif($activeTab == 'status')
      @include('admin.drivers._status', ['drivers' => $drivers])
    @else
      @include('admin.drivers._list', ['drivers' => $drivers])
    @endif
  </div>

  @include('admin.pricing._modals')

  <script>
  (function(){
    var createBtn = document.getElementById('driver-create-button');
    if (createBtn) createBtn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openPostcodeModal === 'function') { window.openPostcodeModal(createBtn.getAttribute('href'), createBtn.dataset.title || 'Add Driver'); } else { window.location = createBtn.getAttribute('href'); } });

    // Attach edit handlers inside list
    var container = document.getElementById('drivers-container');
    function bindDriverListActions(container){
      container.querySelectorAll('.driver-edit-button').forEach(function(btn){ if (btn.dataset.bound) return; btn.dataset.bound='1'; btn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openPostcodeModal === 'function') { window.openPostcodeModal(btn.getAttribute('href'), btn.dataset.title || 'Edit Driver'); } else { window.location = btn.getAttribute('href'); } }); });
      container.querySelectorAll('.driver-view-button').forEach(function(btn){ if (btn.dataset.bound) return; btn.dataset.bound='1'; btn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openPostcodeModal === 'function') { window.openPostcodeModal(btn.getAttribute('href'), btn.dataset.title || 'View Driver'); } else { window.location = btn.getAttribute('href'); } }); });
      container.querySelectorAll('form[action][method]').forEach(function(f){ if (f.dataset.deleteAttached !== '1' && f.querySelector('input[name="_method"]') && f.querySelector('input[name="_method"]').value.toUpperCase()==='DELETE') { if (typeof window.attachDeleteHandler === 'function') window.attachDeleteHandler(f); } });
    }
    if (container) bindDriverListActions(container);

    // listen for modal success and refresh drivers list
    window.refreshDrivers = function(){
      var url = '{{ route('admin.drivers.index') }}?partial=1';
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){ return r.text(); }).then(function(html){ var cont = document.getElementById('drivers-container'); if (cont) { cont.innerHTML = html; bindDriverListActions(cont); } if (typeof window.showToast === 'function') window.showToast('Drivers updated'); }).catch(function(){ if (typeof window.showToast === 'function') window.showToast('Failed to refresh drivers'); });
    };

    window.addEventListener('modal:success', function(e){ try { if (e && e.detail && e.detail.driver) window.refreshDrivers(); } catch(e){ console.warn('modal:success handler failed', e); } });
  })();
  </script>
</div>
@endsection
