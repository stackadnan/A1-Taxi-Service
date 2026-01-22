<div>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">Name</th>
          <th class="p-2">Phone</th>
          <th class="p-2">Email</th>
          <th class="p-2">Coverage</th>
          <th class="p-2">Car Type</th>
          <th class="p-2">Status</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($drivers as $d)
        <tr class="border-t" data-driver-id="{{ $d->id }}">
          <td class="p-2">{{ $d->name }}</td>
          <td class="p-2">{{ $d->phone ?? '-' }}</td>
          <td class="p-2">{{ $d->email ?? '-' }}</td>
          <td class="p-2">{{ $d->coverage_area ?? '-' }}</td>
          <td class="p-2">{{ $d->car_type ?? '-' }}</td>
          <td class="p-2"><span class="text-sm px-2 py-1 rounded bg-gray-100">{{ $d->status ?? 'active' }}</span></td>
          <td class="p-2">
            <a href="{{ route('admin.drivers.show', $d) }}" class="driver-view-button text-indigo-600 mr-2 text-sm" data-title="View Driver">View</a>
            <a href="{{ route('admin.drivers.edit', $d) }}" class="driver-edit-button text-gray-600 text-sm" data-title="Edit Driver">Edit</a>
            <form action="{{ route('admin.drivers.destroy', $d) }}" method="post" class="inline">@csrf @method('delete') <button type="submit" data-confirm="Delete this driver?" class="text-red-600 text-sm ml-2">Delete</button></form>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="7" class="p-4 text-center text-gray-600">No drivers found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $drivers->links() }}
  </div>
</div>

<script>
(function(){
  var rows = document.querySelectorAll('[data-driver-id]');
  rows.forEach(function(r){
    if (r.dataset.rowBound) return;
    r.dataset.rowBound = '1';

    // prevent anchor clicks bubbling up to row
    var anchors = r.querySelectorAll('a');
    anchors.forEach(function(a){ if (a.dataset.stopPropagationAttached) return; a.dataset.stopPropagationAttached='1'; a.addEventListener('click', function(e){ e.stopPropagation(); }); });

    // row click does nothing to avoid accidental navigation — use the View/Edit/Delete buttons instead
    r.addEventListener('click', function(e){
      // intentionally no-op; clicking a row will not navigate
      // but allow keyboard accessibility: if user presses Enter while focused on a row, navigate
      if (e.key === 'Enter' || e.keyCode === 13) {
        var id = r.getAttribute('data-driver-id'); if (!id) return; window.location.href = '/admin/drivers/' + id;
      }
    });
  });
})();
</script>
