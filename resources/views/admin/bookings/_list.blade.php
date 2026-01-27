<div>
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead>
        <tr class="text-sm text-gray-500">
          <th class="p-2">Booking</th>
          <th class="p-2">Pickup</th>
          <th class="p-2">Dropoff</th>
          <th class="p-2">Passenger</th>
          <th class="p-2">Phone</th>
          <th class="p-2">Status</th>
          <th class="p-2">Driver</th>
          @if(isset($active) && in_array($active, ['confirmed','completed']))
            <th class="p-2">Driver Response</th>
          @endif
          <th class="p-2">Driver Price</th>
          <th class="p-2">Total</th>
          <th class="p-2">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $b)
        <tr class="border-t" data-booking-id="{{ $b->id }}">
          <td class="p-2"><div class="text-sm font-medium">{{ $b->booking_code }}</div><div class="text-xs text-gray-400">{{ $b->created_at->format('Y-m-d') }}</div></td>
          <td class="p-2"><div class="text-sm">{{ $b->pickup_address ?: '-' }}</div><div class="text-xs text-gray-400">{{ optional($b->pickup_date)->format('Y-m-d') }} {{ $b->pickup_time }}</div></td>
          <td class="p-2">{{ $b->dropoff_address ?: '-' }}</td>
          <td class="p-2">{{ $b->passenger_name }}</td>
          <td class="p-2">{{ $b->phone }}</td>
          <td class="p-2"><span class="text-sm px-2 py-1 rounded bg-gray-100">{{ optional($b->status)->name }}</span></td>
          <td class="p-2">{{ $b->driver_name ?? '-' }}</td>
          @if(isset($active) && in_array($active, ['confirmed','completed']))
            <td class="p-2">
              @php
                $driverResponse = $b->meta['driver_response'] ?? null;
                $statusClass = '';
                $statusText = '';
                
                if ($driverResponse === 'accepted') {
                    $statusClass = 'bg-green-100 text-green-800';
                    $statusText = 'Accepted';
                } elseif ($driverResponse === 'declined') {
                    $statusClass = 'bg-red-100 text-red-800';
                    $statusText = 'Rejected';
                } else {
                    $statusClass = 'bg-yellow-100 text-yellow-800';
                    $statusText = 'Pending';
                }
              @endphp
              <span class="text-xs px-2 py-1 rounded-full font-medium {{ $statusClass }}">
                {{ $statusText }}
              </span>
            </td>
          @endif
          <td class="p-2">{{ $b->driver_price ? number_format($b->driver_price,2) : '-' }}</td>
          <td class="p-2">{{ $b->total_price ? number_format($b->total_price,2) : '-' }}</td>
          <td class="p-2">
            <a href="{{ route('admin.bookings.show', $b) }}?section={{ $active ?? 'new' }}" class="booking-view-button text-indigo-600 mr-2 text-sm" data-title="View Booking">View</a>
            <a href="{{ route('admin.bookings.edit', $b) }}?section={{ $active ?? 'new' }}" class="text-gray-600 text-sm">Edit</a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="{{ isset($active) && in_array($active, ['confirmed','completed']) ? '11' : '10' }}" class="p-4 text-center text-gray-600">No bookings found for this section.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $bookings->links() }}
  </div>
</div>