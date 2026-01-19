@extends('layouts.admin')

@section('title', 'Booking ' . ($booking->booking_code ?? ''))

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Booking: {{ $booking->booking_code }}</h1>
      <div class="text-sm text-gray-500">Created: {{ $booking->created_at->format('Y-m-d H:i') }}</div>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.bookings.edit', $booking) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Edit</a>
      <a href="{{ route('admin.bookings.index') }}" class="px-3 py-2 border rounded">Back</a>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-6">
    <div>
      <h3 class="font-semibold">Passenger</h3>
      <p>{{ $booking->passenger_name }}</p>
      <p class="text-sm text-gray-500">{{ $booking->phone }} {{ $booking->email ? ' — '.$booking->email : '' }}</p>

      <h3 class="mt-4 font-semibold">Pickup</h3>
      <p>{{ $booking->meta['pickup_address'] ?? ($booking->pickup_address ?? '-') }}</p>
      <p class="text-sm text-gray-500">{{ optional($booking->pickup_date)->format('Y-m-d') }} {{ $booking->pickup_time }}</p>

      <h3 class="mt-4 font-semibold">Dropoff</h3>
      <p>{{ $booking->meta['dropoff_address'] ?? ($booking->dropoff_address ?? '-') }}</p>
    </div>

    <div>
      <h3 class="font-semibold">Status</h3>
      <p>{{ optional($booking->status)->name }}</p>

      <h3 class="mt-4 font-semibold">Extras</h3>
      <p>Meet & Greet: {{ $booking->meet_and_greet ? 'Yes' : 'No' }}</p>
      <p>Baby Seat: {{ $booking->baby_seat ? 'Yes' : 'No' }}{{ $booking->baby_seat_age ? ' — '.$booking->baby_seat_age : '' }}</p>
      <p>Passengers: {{ $booking->meta['passengers'] ?? '-' }}</p>
      <p class="mt-4">Message to driver: {{ $booking->message_to_driver }}</p>
    </div>
  </div>
</div>
@endsection