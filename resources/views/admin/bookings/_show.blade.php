<div class="p-4">
  <h3 class="font-semibold">{{ $booking->booking_code }}</h3>
  <p>{{ $booking->passenger_name }} — {{ $booking->phone }}</p>
  <p class="text-sm text-gray-500">Status: {{ optional($booking->status)->name }}</p>
</div>