<div class="p-4">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h3 class="text-xl font-semibold">Booking: {{ $booking->booking_code }}</h3>
      <div class="text-sm text-gray-500">Created: {{ $booking->created_at->format('Y-m-d H:i') }}</div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Edit</a>
      <button data-action="close-modal" class="px-3 py-2 border rounded">Close</button>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
    <div class="space-y-3">
      <h4 class="font-semibold mb-1">Passenger</h4>
      <p class="text-sm">{{ $booking->passenger_name }}</p>
      <p class="text-xs text-gray-500">{{ $booking->phone }}{{ $booking->email ? ' — '.$booking->email : '' }}</p>

      <h4 class="mt-3 font-semibold mb-1">Pickup</h4>
      <p class="text-sm">{{ $booking->meta['pickup_address'] ?? ($booking->pickup_address ?? '-') }}</p>
      <p class="text-xs text-gray-500">{{ optional($booking->pickup_date)->format('Y-m-d') }} {{ $booking->pickup_time }}</p>

      <h4 class="mt-3 font-semibold mb-1">Dropoff</h4>
      <p class="text-sm">{{ $booking->meta['dropoff_address'] ?? ($booking->dropoff_address ?? '-') }}</p>
    </div>

    <div class="space-y-3">
      <h4 class="font-semibold mb-1">Status</h4>
      <p class="text-sm">{{ optional($booking->status)->name }}</p>

      <h4 class="mt-3 font-semibold mb-1">Extras</h4>
      <p class="text-sm">Meet & Greet: {{ $booking->meet_and_greet ? 'Yes' : 'No' }}</p>
      <p class="text-sm">Baby Seat: {{ $booking->baby_seat ? 'Yes' : 'No' }}{{ $booking->baby_seat_age ? ' — '.$booking->baby_seat_age : '' }}</p>
      <p class="text-sm">Passengers: {{ $booking->passengers_count ?? '-' }}</p>

      <h4 class="mt-3 font-semibold mb-1">Message to driver</h4>
      <p class="text-sm">{{ $booking->message_to_driver ?? '-' }}</p>

      <h4 class="mt-3 font-semibold mb-1">Message to admin</h4>
      <p class="text-sm">{{ $booking->message_to_admin ?? '-' }}</p>

      <h4 class="mt-3 font-semibold mb-1">Driver</h4>
      <p class="text-sm">{{ $booking->driver_name ?? '-' }}</p>

      <h4 class="mt-3 font-semibold mb-1">Pricing</h4>
      <p class="text-sm">{{ $booking->total_price ? '€' . number_format($booking->total_price,2) : '-' }} EUR</p>
    </div>
  </div>
</div>