<div>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">Driver</th>
          <th class="p-2">Booking</th>
          <th class="p-2">Status</th>
          <th class="p-2">Since</th>
          <th class="p-2">Track</th>
        </tr>
      </thead>
      <tbody>
        @forelse($drivers as $d)
        <tr class="border-t" data-driver-id="{{ $d->id }}">
          <td class="p-2">{{ $d->name }}</td>
          <td class="p-2">@if($d->current_booking)<a href="{{ route('admin.bookings.show', $d->current_booking) }}" class="text-indigo-600 text-sm">{{ $d->current_booking->booking_code }}</a>@else None @endif</td>
          <td class="p-2">
            @php
              $colorClass = 'bg-gray-100 text-gray-700';
              if ($d->status_color === 'green') $colorClass = 'bg-green-100 text-green-700';
              elseif ($d->status_color === 'yellow') $colorClass = 'bg-yellow-100 text-yellow-700';
              elseif ($d->status_color === 'blue') $colorClass = 'bg-blue-100 text-blue-700';
              elseif ($d->status_color === 'red') $colorClass = 'bg-red-100 text-red-700';
            @endphp
            <span class="text-sm px-2 py-1 rounded {{ $colorClass }}">{{ $d->status_label }}</span>
          </td>
          <td class="p-2">{{ $d->status_since }}</td>
          <td class="p-2">📍</td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="p-4 text-center text-gray-600">No drivers found.</td>
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

    var anchors = r.querySelectorAll('a');
    anchors.forEach(function(a){ if (a.dataset.stopPropagationAttached) return; a.dataset.stopPropagationAttached='1'; a.addEventListener('click', function(e){ e.stopPropagation(); }); });

    // row click does nothing; keep consistent with other list behavior
    r.addEventListener('click', function(e){ if (e.key === 'Enter' || e.keyCode === 13) { var id = r.getAttribute('data-driver-id'); if (!id) return; window.location.href = '/admin/drivers/' + id; } });
  });
})();
</script>