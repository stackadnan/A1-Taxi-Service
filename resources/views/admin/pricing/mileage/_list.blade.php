<div>
  <div class="flex items-center justify-between mb-4">
    <form id="mileage-search-form" method="GET" action="{{ route('admin.pricing.mileage.index') }}" class="flex items-center gap-2">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search miles" class="border rounded p-2" />
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
    </form>

    @if($items->total() < 10)
      <a id="mileage-create-button" href="{{ route('admin.pricing.mileage.create') }}" data-title="Add Mileage Charge" class="px-4 py-2 bg-indigo-600 text-white rounded">Add Mileage</a>
    @else
      <button id="mileage-create-button" disabled title="Maximum of 10 mileage charges reached" class="px-4 py-2 bg-indigo-600 text-white rounded opacity-50 cursor-not-allowed">Add Mileage</button>
    @endif
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">ID</th>
          <th class="p-2">Start Mile</th>
          <th class="p-2">End Mile</th>
          <th class="p-2">Saloon</th>
          <th class="p-2">Business</th>
          <th class="p-2">MPV6</th>
          <th class="p-2">MPV8</th>
          <th class="p-2">Fixed</th>
          <th class="p-2">Status</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr class="border-t">
          <td class="p-2">{{ $item->id }}</td>
          <td class="p-2">{{ $item->start_mile }}</td>
          <td class="p-2">{{ $item->end_mile ?? '-' }}</td>
          <td class="p-2">{{ $item->saloon_price }}</td>
          <td class="p-2">{{ $item->business_price }}</td>
          <td class="p-2">{{ $item->mpv6_price }}</td>
          <td class="p-2">{{ $item->mpv8_price }}</td>
          <td class="p-2">{{ $item->is_fixed_charge ? 'Yes' : 'No' }}</td>
          <td class="p-2">{{ ucfirst($item->status) }}</td>
          <td class="p-2">
            @if(auth()->check() && auth()->user()->hasPermission('pricing.edit'))
            <a href="{{ route('admin.pricing.mileage.edit', $item) }}" class="text-indigo-600 mr-2 mileage-edit-button">Edit</a>
            <form method="POST" action="{{ route('admin.pricing.mileage.destroy', $item) }}" style="display:inline">@csrf @method('DELETE')
              <!-- <button type="submit" class="text-red-600" data-confirm="Delete?">Delete</button> -->
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td class="p-4" colspan="10">No records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->withQueryString()->links() }}
  </div>
</div>

<script>
// Attach modal open handlers for pre-rendered mileage list
(function(){
  var createBtn = document.getElementById('mileage-create-button');
  function updateCreateButton() {
    try {
      var tbody = document.querySelector('#mileage-container tbody');
      if (!tbody) return;
      var rows = Array.from(tbody.querySelectorAll('tr'));
      // exclude the 'No records found' placeholder row which has colspan
      var valid = rows.filter(function(r){ return !r.querySelector('td[colspan]'); });
      if (valid.length >= 10) {
        if (createBtn) {
          createBtn.disabled = true;
          createBtn.classList.add('opacity-50');
          createBtn.classList.add('cursor-not-allowed');
          createBtn.setAttribute('title', 'Maximum of 10 mileage charges reached');
        }
      } else {
        if (createBtn) {
          createBtn.disabled = false;
          createBtn.classList.remove('opacity-50');
          createBtn.classList.remove('cursor-not-allowed');
          createBtn.removeAttribute('title');
        }
      }
    } catch(e){ console.warn('updateCreateButton failed', e); }
  }

  if (createBtn) createBtn.addEventListener('click', function(e){
    if (createBtn.disabled) { e.preventDefault(); return; }
    e.preventDefault(); if (typeof window.openMileageModal === 'function') { window.openMileageModal(createBtn.getAttribute('href')); } else { window.location = createBtn.getAttribute('href'); }
  });

  document.querySelectorAll('.mileage-edit-button').forEach(function(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openMileageModal === 'function') { window.openMileageModal(btn.getAttribute('href')); } else { window.location = btn.getAttribute('href'); } }); });

  // run once on load
  updateCreateButton();

  // also expose a function so the outer loader can call it after injecting (defensive)
  window._updateMileageCreateButton = updateCreateButton;
})();
</script>