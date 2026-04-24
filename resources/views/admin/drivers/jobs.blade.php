@extends('layouts.admin')

@section('title', 'Driver Jobs')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Jobs Assigned to <span class="text-green-600">{{ $driver->name }}</span></h1>
      <p class="text-sm text-gray-600">Filter by date range and generate an invoice draft. You can review and send it afterwards.</p>
    </div>
    <a href="{{ route('admin.drivers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Back to Drivers</a>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  <div class="mb-4 rounded border p-4 bg-gray-50">
    <form method="get" action="{{ route('admin.drivers.jobs', $driver) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3 items-end">
      <div>
        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
        <input id="start_date" type="date" name="start_date" value="{{ $startDate }}" class="w-full border rounded px-3 py-2" required>
      </div>
      <div>
        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
        <input id="end_date" type="date" name="end_date" value="{{ $endDate }}" class="w-full border rounded px-3 py-2" required>
      </div>
      <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Search</button>
      </div>
    </form>

    <form method="post" action="{{ route('admin.drivers.jobs.invoice', $driver) }}" class="mt-3">
      @csrf
      <input type="hidden" name="start_date" value="{{ $startDate }}">
      <input type="hidden" name="end_date" value="{{ $endDate }}">
      <button
        type="submit"
        class="px-4 py-2 bg-black text-white rounded hover:bg-emerald-700 {{ $invoiceJobs->count() === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
        {{ $invoiceJobs->count() === 0 ? 'disabled' : '' }}
      >
        Generate Invoice Draft
      </button>
    </form>

    <div class="mt-3 text-sm text-gray-700">
      <span class="font-semibold">Total <span class="text-red-600">{{ $invoiceJobs->count() }}</span> completed jobs between {{ $startDate }} and {{ $endDate }}</span>
      <span class="ml-4">Total Driver Fare: <span class="font-semibold">£{{ number_format((float) $invoiceTotal, 2) }}</span></span>
      <span class="ml-4">Total Amount: <span class="font-semibold">£{{ number_format((float) $invoiceAmountTotal, 2) }}</span></span>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-left">
      <thead class="bg-gray-50 border-b">
        <tr class="text-sm text-gray-500">
          <th class="p-3 font-medium">Booking ID</th>
          <th class="p-3 font-medium">Passenger</th>
          <th class="p-3 font-medium">Contact</th>
          <th class="p-3 font-medium">Pickup Date/Time</th>
          <th class="p-3 font-medium">Pickup</th>
          <th class="p-3 font-medium">Dropoff</th>
          <th class="p-3 font-medium">Amount</th>
          <th class="p-3 font-medium">Driver Fare</th>
          <th class="p-3 font-medium">Veh Type</th>
          <th class="p-3 font-medium">Status</th>
          <th class="p-3 font-medium">Driver Response</th>
          <th class="p-3 font-medium">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($jobs as $job)
          @php
            $meta = $job->meta ?? [];
            $driverResponse = strtolower((string) ($meta['driver_response'] ?? ''));
            $statusName = strtolower((string) (optional($job->status)->name ?? ''));
            $paymentType = strtolower(trim((string) ($job->payment_type ?? '')));
            $driverVisibleFare = is_numeric($meta['driver_display_price'] ?? null)
              ? round(max(0, (float) $meta['driver_display_price']), 2)
              : round((float) ($job->total_price ?? 0), 2);
            $calculatedDriverFare = (float) ($job->driver_price ?? 0);
            if ($paymentType === 'card') {
              $calculatedDriverFare = round($driverVisibleFare * 0.8, 2);
            } elseif ($paymentType === 'cash') {
              $calculatedDriverFare = round($driverVisibleFare * -0.2, 2);
            }
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
              <span class="text-sm font-semibold text-green-700">{{ $job->booking_code ?? ('#' . $job->id) }}</span>
            </td>
            <td class="p-3 text-sm text-gray-900">{{ $job->passenger_name ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->phone ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">
              {{ optional($job->pickup_date)->format('Y-m-d') ?: '-' }}
              @if($job->pickup_time)
                / <span class="font-semibold text-indigo-700">{{ $job->pickup_time }}</span>
              @endif
            </td>
            <td class="p-3 text-sm text-gray-900">{{ $job->pickup_address ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->dropoff_address ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">£{{ number_format($driverVisibleFare, 2) }}</td>
            <td class="p-3 text-sm text-gray-900">£{{ number_format($calculatedDriverFare, 2) }}</td>
            <td class="p-3 text-sm text-gray-900">{{ $job->vehicle_type ?: '-' }}</td>
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
            <td colspan="12" class="p-8 text-center text-gray-500">No jobs found for this driver in the selected date range.</td>
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
