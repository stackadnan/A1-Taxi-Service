@extends('driver.layouts.app')

@section('title', 'Job Details')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-bold">Booking #{{ $job->booking_code ?? $job->id }}</h1>
      <p class="text-sm text-gray-600">{{ optional($job->pickup_date)->format('M d, Y') }} @if($job->pickup_time) at {{ $job->pickup_time }}@endif</p>
    </div>
    <a href="{{ url()->previous() ?: route('driver.dashboard') }}" class="px-3 py-2 bg-gray-200 rounded">Back</a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <h3 class="text-lg font-semibold mb-2">Passenger & Contact</h3>
      <div class="space-y-2 text-sm text-gray-800">
        <div><strong>Passenger:</strong> {{ $job->passenger_name ?? '-' }}</div>
        <div><strong>Phone:</strong> {{ $job->phone ?? '-' }}</div>
        <div><strong>Email:</strong> {{ $job->email ?? '-' }}</div>
      </div>

      <h3 class="text-lg font-semibold mt-4 mb-2">Trip</h3>
      <div class="space-y-2 text-sm text-gray-800">
        <div><strong>Pickup:</strong> {{ $job->pickup_address ?? '-' }}</div>
        <div><strong>Dropoff:</strong> {{ $job->dropoff_address ?? '-' }}</div>
        <div><strong>Pickup Date:</strong> {{ optional($job->pickup_date)->format('Y-m-d') ?? '-' }}</div>
        <div><strong>Pickup Time:</strong> {{ $job->pickup_time ?? '-' }}</div>
      </div>

      <h3 class="text-lg font-semibold mt-4 mb-2">Flight & Extras</h3>
      <div class="space-y-2 text-sm text-gray-800">
        <div><strong>Flight Number:</strong> {{ $job->flight_number ?? ($job->meta['flight_number'] ?? '-') }}</div>
        <div><strong>Flight Time:</strong> {{ $job->meta['flight_time'] ?? '-' }}</div>
        <div><strong>Passengers:</strong> {{ $job->passengers_count ?? '-' }}</div>
        <div><strong>Luggage:</strong> {{ $job->luggage_count ?? ($job->meta['luggage'] ?? '-') }}</div>
        <div><strong>Baby seat:</strong> {{ ($job->baby_seat ? 'Yes' : 'No') }} {{ $job->baby_seat_age ? '(' . $job->baby_seat_age . ')' : '' }}</div>
      </div>

    </div>

    <div>
      <h3 class="text-lg font-semibold mb-2">Pricing & Notes</h3>
      <div class="space-y-2 text-sm text-gray-800">
        <div><strong>Price:</strong> {{ $job->driver_price ? '€' . number_format($job->driver_price,2) : '-' }}</div>
        <div><strong>Notes to driver:</strong> {{ $job->message_to_driver ?? '-' }}</div>
      </div>

      <h3 class="text-lg font-semibold mt-4 mb-2">Status</h3>
      <div class="text-sm text-gray-800">
        <div><strong>Status:</strong> {{ optional($job->status)->name ?? '-' }}</div>
        <div><strong>Driver response:</strong> {{ $job->meta['driver_response'] ?? '-' }}</div>
      </div>

    </div>
  </div>
</div>
@endsection
