<div>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">Name</th>
          <th class="p-2">Phone</th>
          <th class="p-2">Email</th>
          <th class="p-2">Expiring Documents</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($drivers as $d)
        <tr class="border-t" data-driver-id="{{ $d->id }}">
          <td class="p-2">{{ $d->name }}</td>
          <td class="p-2">{{ $d->phone ?? '-' }}</td>
          <td class="p-2">{{ $d->email ?? '-' }}</td>
          <td class="p-2">
            @if(!empty($d->expiring_documents) && count($d->expiring_documents))
              <ul class="text-sm">
                @foreach($d->expiring_documents as $doc)
                  <li>
                    <span class="font-medium">{{ $doc['label'] }}:</span>
                    <span class="ml-2">{{ $doc['expiry']->format('Y-m-d') }}</span>
                    @if($doc['status'] === 'expired')
                      <span class="ml-2 text-red-600 font-semibold">Expired</span>
                    @else
                      <span class="ml-2 text-yellow-600">Expiring</span>
                    @endif
                  </li>
                @endforeach
              </ul>
            @else
              -
            @endif
          </td>
          <td class="p-2">
            <a href="{{ route('admin.drivers.show', $d) }}" class="driver-view-button text-indigo-600 mr-2 text-sm" data-title="View Driver">View</a>
            <a href="{{ route('admin.drivers.edit', $d) }}" class="driver-edit-button text-gray-600 text-sm" data-title="Edit Driver">Edit</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="p-4 text-center text-gray-600">No drivers with expiring documents found.</td>
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
// Bind view/edit handlers the same way as _list
(function(){
  var rows = document.querySelectorAll('[data-driver-id]');
  rows.forEach(function(r){
    if (r.dataset.rowBound) return; r.dataset.rowBound = '1';
    var anchors = r.querySelectorAll('a');
    anchors.forEach(function(a){ if (a.dataset.stopPropagationAttached) return; a.dataset.stopPropagationAttached='1'; a.addEventListener('click', function(e){ e.stopPropagation(); }); });
  });
})();
</script>