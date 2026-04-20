@extends('layouts.admin')

@section('title', 'Driver Jobs')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Jobs for {{ $driver->name }}</h1>
      <p class="text-sm text-gray-600">Showing bookings assigned to this driver.</p>
    </div>
    <a href="{{ route('admin.drivers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Back to Drivers</a>
  </div>

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
          <th class="p-3 font-medium">Driver Response</th>
          <th class="p-3 font-medium">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($jobs as $job)
          @php
            $meta = $job->meta ?? [];
            $driverResponse = strtolower((string) ($meta['driver_response'] ?? ''));
            $statusName = strtolower((string) (optional($job->status)->name ?? ''));
            $inRoute = filter_var($meta['in_route'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $arrivedAtPickup = filter_var($meta['arrived_at_pickup'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $pobMarked = !empty($meta['pob_marked_at']);
            $completedAt = !empty($meta['completed_at']);

            if ($statusName === 'completed' || $completedAt) {
              $responseClass = 'bg-emerald-100 text-emerald-800';
              $responseText = 'Completed';
            } elseif ($statusName === 'pob' || $pobMarked) {
              $responseClass = 'bg-indigo-100 text-indigo-800';
              $responseText = 'POB';
            } elseif ($arrivedAtPickup) {
              $responseClass = 'bg-sky-100 text-sky-800';
              $responseText = 'Arrived';
            } elseif ($inRoute) {
              $responseClass = 'bg-purple-100 text-purple-800';
              $responseText = 'In Route';
            } elseif ($driverResponse === 'accepted') {
              $responseClass = 'bg-green-100 text-green-800';
              $responseText = 'Accepted';
            } elseif ($driverResponse === 'declined') {
              $responseClass = 'bg-red-100 text-red-800';
              $responseText = 'Rejected';
            } else {
              $responseClass = 'bg-yellow-100 text-yellow-800';
              $responseText = 'Pending';
            }
          @endphp
          <tr class="border-t">
            <td class="p-3">
              <div class="text-sm font-medium text-gray-900">{{ $job->booking_code ?? ('#' . $job->id) }}</div>
              <div class="text-xs text-gray-500">{{ optional($job->created_at)->format('Y-m-d') }}</div>
            </td>
            <td class="p-3 text-sm text-gray-900">{{ $job->pickup_address ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->dropoff_address ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->passenger_name ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->phone ?: '-' }}</td>
            <td class="p-3">
              <span class="text-xs px-2 py-1 rounded-full font-medium bg-gray-100 text-gray-700">
                {{ optional($job->status)->name ?? '-' }}
              </span>
            </td>
            <td class="p-3">
              <span class="text-xs px-2 py-1 rounded-full font-medium {{ $responseClass }}">
                {{ $responseText }}
              </span>
            </td>
            <td class="p-3">
              <a href="{{ route('admin.bookings.edit', $job) }}?section=confirmed&readonly=1" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium">View Booking</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="p-8 text-center text-gray-500">No jobs found for this driver.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($jobs->hasPages())
    <div class="mt-4">
      {{ $jobs->links() }}
    </div>
  @endif
</div>
@endsection
