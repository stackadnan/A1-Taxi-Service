<div class="bg-white rounded-lg border shadow-sm">
  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead class="bg-gray-50 border-b">
        <tr class="text-sm text-gray-500">
          <th class="p-3 font-medium">Booking</th>
          <th class="p-3 font-medium">Pickup</th>
          <th class="p-3 font-medium">Dropoff</th>
          <th class="p-3 font-medium">Passenger</th>
          <th class="p-3 font-medium">Phone</th>
          <th class="p-3 font-medium">Status</th>
          @if(!(isset($active) && $active === 'new'))
            <th class="p-3 font-medium">Driver</th>
          @endif
          @if(isset($active) && in_array($active, ['confirmed','completed']))
            <th class="p-3 font-medium">Driver Response</th>
          @endif
          @if(!(isset($active) && $active === 'new'))
            <th class="p-3 font-medium">Driver Price</th>
          @endif
          <th class="p-3 font-medium">Total</th>
          <th class="p-3 font-medium">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($bookings as $b)
        <tr class="border-t hover:bg-gray-50 transition-colors" data-booking-id="{{ $b->id }}" id="booking-row-{{ $b->id }}">
          <td class="p-3"><div class="text-sm font-medium text-gray-900">{{ $b->booking_code }}</div><div class="text-xs text-gray-500">{{ $b->created_at->format('Y-m-d') }}</div></td>
          <td class="p-3"><div class="text-sm text-gray-900">{{ $b->pickup_address ?: '-' }}</div><div class="text-xs text-gray-500">{{ optional($b->pickup_date)->format('Y-m-d') }} {{ $b->pickup_time }}</div></td>
          <td class="p-3 text-sm text-gray-900">{{ $b->dropoff_address ?: '-' }}</td>
          <td class="p-3 text-sm text-gray-900">{{ $b->passenger_name }}</td>
          <td class="p-3 text-sm text-gray-900">{{ $b->phone }}</td>
          <td class="p-3"><span class="text-sm px-2 py-1 rounded bg-gray-100 text-gray-700">{{ optional($b->status)->name }}</span></td>
          @if(!(isset($active) && $active === 'new'))
            <td class="p-3 text-sm text-gray-900" data-col="driver_name">{{ $b->driver_name ?? '-' }}</td>
          @endif
          @if(isset($active) && in_array($active, ['confirmed','completed']))
            @if(!$b->driver_id)
              <td class="p-3" data-col="driver_response"><span class="text-sm text-gray-500">-</span></td>
            @else
              <td class="p-3" data-col="driver_response">
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
          @endif
          @if(!(isset($active) && $active === 'new'))
            <td class="p-3 text-sm text-gray-900" data-col="driver_price">{{ $b->driver_price ? number_format($b->driver_price,2) : '-' }}</td>
          @endif
          <td class="p-3 text-sm text-gray-900" data-col="total_price">{{ $b->total_price ? number_format($b->total_price,2) : '-' }}</td>
          <td class="p-3">
            <a href="{{ route('admin.bookings.show', $b) }}?section={{ $active ?? 'new' }}" class="booking-view-button text-indigo-600 hover:text-indigo-800 mr-3 text-sm font-medium" data-title="View Booking">View</a>
            <a href="{{ route('admin.bookings.edit', $b) }}?section={{ $active ?? 'new' }}" class="text-gray-600 hover:text-gray-800 text-sm font-medium">Edit</a>
          </td>
        </tr>
        @empty
        @php
          $colspan = 8; // Booking, Pickup, Dropoff, Passenger, Phone, Status, Total, Actions
          if (!(isset($active) && $active === 'new')) {
            // include Driver + Driver Price
            $colspan += 2;
          }
          if (isset($active) && in_array($active, ['confirmed','completed'])) {
            // include Driver Response
            $colspan += 1;
          }
        @endphp
        <tr>
          <td colspan="{{ $colspan }}" class="p-8 text-center text-gray-500">
            <div class="flex flex-col items-center">
              <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
              </svg>
              <p class="font-medium">No bookings found for this section</p>
              <p class="text-sm text-gray-400 mt-1">Bookings will appear here when they match the selected criteria</p>
            </div>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($bookings->hasPages())
  <div class="px-6 py-4 border-t bg-gray-50">
    <div class="flex items-center justify-between">
      <div class="text-sm text-gray-700">
        Showing {{ $bookings->firstItem() }} to {{ $bookings->lastItem() }} of {{ $bookings->total() }} results
      </div>
      <div class="pagination">
        {{ $bookings->links() }}
      </div>
    </div>
  </div>
  @endif
</div>