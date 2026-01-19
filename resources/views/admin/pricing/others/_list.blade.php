<div>
  <div class="flex items-center justify-between mb-4">
    <form id="other-search-form" method="GET" action="{{ route('admin.pricing.others.index') }}" class="flex items-center gap-2">
      <input type="search" name="q" value="{{ $q ?? '' }}" placeholder="Search charges" class="border rounded p-2" />
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
    </form>

    <!-- <a id="other-create-button" href="{{ route('admin.pricing.others.create') }}" data-title="Add Other Charge" class="px-4 py-2 bg-indigo-600 text-white rounded">Add Charge</a> -->
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500 border-b">
          <th class="p-3">Charge Name</th>
          <th class="p-3 text-center">Pickup</th>
          <th class="p-3 text-center">Drop Off</th>
          <th class="p-3 text-center">Status</th>
          <th class="p-3 text-center">Edit Price</th>
        </tr>
      </thead>
      <tbody>
        @forelse($items as $item)
        <tr class="border-b hover:bg-gray-50">
          <td class="p-3">{{ $item->charge_name }}</td>
          <td class="p-3 text-center">{{ number_format($item->pickup_price ?? 0, 2) }}</td>
          <td class="p-3 text-center">{{ number_format($item->dropoff_price ?? 0, 2) }}</td>
          <td class="p-3 text-center">
            <span class="px-2 py-1 rounded text-xs {{ $item->active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
              {{ $item->active ? 'Active' : 'Inactive' }}
            </span>
          </td>
          <td class="p-3 text-center">
            @if(auth()->check() && auth()->user()->hasPermission('pricing.edit'))
            <a href="{{ route('admin.pricing.others.edit', $item) }}" class="text-indigo-600 hover:text-indigo-800 other-edit-button mr-2">Edit</a>
            <form method="POST" action="{{ route('admin.pricing.others.destroy', $item) }}" style="display:inline">
              @csrf 
              @method('DELETE')
              <!-- <button type="submit" class="text-red-600 hover:text-red-800" data-confirm="Are you sure you want to delete this charge?">Delete</button> -->
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td class="p-4 text-center text-gray-500" colspan="5">No records found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $items->withQueryString()->links() }}
  </div>
</div>

<script>
// Attach modal open handlers for pre-rendered list
(function(){
  var createBtn = document.getElementById('other-create-button');
  if (createBtn) createBtn.addEventListener('click', function(e){ 
    e.preventDefault(); 
    if (typeof window.openOtherModal === 'function') { 
      window.openOtherModal(createBtn.getAttribute('href'), createBtn.dataset.title || 'Add Other Charge'); 
    } else { 
      window.location = createBtn.getAttribute('href'); 
    } 
  });
  
  document.querySelectorAll('.other-edit-button').forEach(function(btn){ 
    btn.addEventListener('click', function(e){ 
      e.preventDefault(); 
      if (typeof window.openOtherModal === 'function') { 
        window.openOtherModal(btn.getAttribute('href'), 'Edit Other Charge'); 
      } else { 
        window.location = btn.getAttribute('href'); 
      } 
    }); 
  });
})();
</script>
