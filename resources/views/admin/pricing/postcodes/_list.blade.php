<div>
  <div class="flex items-center justify-between mb-4">
    <form id="postcode-search-form" method="GET" action="{{ route('admin.pricing.postcodes.index') }}" class="flex items-center gap-2">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search postcode" class="border rounded p-2" />
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
    </form>

    <a id="postcode-create-button" href="{{ route('admin.pricing.postcodes.create') }}" data-title="Add Postcode Charge" class="px-4 py-2 bg-indigo-600 text-white rounded">Add Postcode</a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">ID</th>
          <th class="p-2">Pickup Postcode</th>
          <th class="p-2">Dropoff Postcode</th>
          <th class="p-2">Saloon</th>
          <th class="p-2">Business</th>
          <th class="p-2">MPV6</th>
          <th class="p-2">MPV8</th>
          <th class="p-2">Status</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr class="border-t">
          <td class="p-2">{{ $item->id }}</td>
          <td class="p-2">{{ $item->pickup_postcode }}</td>
          <td class="p-2">{{ $item->dropoff_postcode }}</td>
          <td class="p-2">{{ $item->saloon_price }}</td>
          <td class="p-2">{{ $item->business_price }}</td>
          <td class="p-2">{{ $item->mpv6_price }}</td>
          <td class="p-2">{{ $item->mpv8_price }}</td>
          <td class="p-2">{{ ucfirst($item->status) }}</td>
          <td class="p-2">
            @if(auth()->check() && auth()->user()->hasPermission('pricing.edit'))
            <a href="{{ route('admin.pricing.postcodes.edit', $item) }}" class="text-indigo-600 mr-2 postcode-edit-button">Edit</a>
            <form method="POST" action="{{ route('admin.pricing.postcodes.destroy', $item) }}" style="display:inline">@csrf @method('DELETE')<button type="submit" class="text-red-600" data-confirm="Delete?">Delete</button></form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td class="p-4" colspan="9">No records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->withQueryString()->links() }}
  </div>
</div>

<script>
// Attach modal open handlers for pre-rendered list (works on dedicated page)
(function(){
  var createBtn = document.getElementById('postcode-create-button');
  if (createBtn) createBtn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openPostcodeModal === 'function') { window.openPostcodeModal(createBtn.getAttribute('href'), createBtn.dataset.title || 'Add Postcode Charge'); } else { window.location = createBtn.getAttribute('href'); } });
  document.querySelectorAll('.postcode-edit-button').forEach(function(btn){ btn.addEventListener('click', function(e){ e.preventDefault(); if (typeof window.openPostcodeModal === 'function') { window.openPostcodeModal(btn.getAttribute('href'), 'Edit Postcode Charge'); } else { window.location = btn.getAttribute('href'); } }); });
})();
</script>