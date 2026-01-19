@extends('layouts.admin')

@section('title', 'Drivers')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Drivers</h1>
    <a id="driver-create-button" data-title="Add Driver" href="{{ route('admin.drivers.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg">New Driver</a>
  </div>

  <div class="mb-4 flex gap-2 items-center">
    <form method="get" action="{{ route('admin.drivers.index') }}" class="flex items-center gap-2">
      <input type="text" name="q" value="{{ request('q') }}" placeholder="Search drivers by name, phone or plate" class="border rounded px-3 py-2" />
      <button class="px-3 py-2 bg-gray-100 border rounded">Search</button>
    </form>
  </div>

  <div id="drivers-container">
    @include('admin.drivers._list', ['drivers' => $drivers])
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
