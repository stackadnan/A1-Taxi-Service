@extends('layouts.admin')

@section('title', 'Review Approval')

@section('content')
<div class="bg-white p-6 rounded shadow space-y-6">
  <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Review Approval</h1>
      <p class="text-sm text-gray-500 mt-1">Pending = 1, Approved = 2, Rejected = 0</p>
    </div>
    <span class="text-sm text-gray-500">Bookings queued for customer review follow-up</span>
  </div>

  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-slate-700">{{ $totalCount }}</div>
      <div class="text-xs text-slate-500 mt-1">Total Requests</div>
    </div>
    <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-amber-700">{{ $pendingCount }}</div>
      <div class="text-xs text-amber-600 mt-1">Pending</div>
    </div>
    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-emerald-700">{{ $approvedCount }}</div>
      <div class="text-xs text-emerald-600 mt-1">Approved</div>
    </div>
    <div class="bg-rose-50 border border-rose-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-rose-700">{{ $rejectedCount }}</div>
      <div class="text-xs text-rose-600 mt-1">Rejected</div>
    </div>
  </div>

  <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[220px]">
      <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking id, name, phone"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
      <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All</option>
        <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Pending</option>
        <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Approved</option>
        <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Rejected</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="px-4 py-2 text-white rounded-lg text-sm font-medium transition" style="background-color: #4F46E5;">Filter</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.reviews.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium transition" style="background-color: #E5E7EB; color: #334155;">Clear</a>
      @endif
    </div>
  </form>

  @if($bookings->isEmpty())
    <div class="text-center py-16 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-sm">No review approval requests found.</p>
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Booking Id</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Name</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Pickup Date</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Time</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Driver Name</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">View Job</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Approve</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Reject</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($bookings as $booking)
            @php
              $reviewStatus = (int) ($booking->review_status ?? -1);
              $statusLabel = match ($reviewStatus) {
                1 => 'Pending',
                2 => 'Approved',
                0 => 'Rejected',
                default => 'Unknown',
              };
              $statusStyle = match ($reviewStatus) {
                1 => 'background-color: #FEF3C7; color: #B45309;',
                2 => 'background-color: #D1FAE5; color: #047857;',
                0 => 'background-color: #FEE2E2; color: #B91C1C;',
                default => 'background-color: #F3F4F6; color: #4B5563;',
              };
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="font-mono text-indigo-700 font-semibold text-xs bg-indigo-50 px-2 py-1 rounded inline-block">{{ $booking->booking_code }}</div>
                <div class="mt-2"><span class="text-[11px] font-medium px-2 py-0.5 rounded-full" style="{{ $statusStyle }}">{{ $statusLabel }}</span></div>
                <!-- <div class="mt-2"><span class="text-[11px] font-medium px-2 py-0.5 rounded-full" style="{{ $statusStyle }}">{{ $statusLabel }} ({{ $reviewStatus }})</span></div> -->
              </td>
              <td class="px-4 py-3">
                <span class="block text-gray-700">{{ $booking->passenger_name }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ optional($booking->pickup_date)->format('d M Y') ?: '-' }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $booking->pickup_time ?: '-' }}</td>
              <td class="px-4 py-3 text-gray-700">{{ $booking->driver->name ?? ($booking->driver_name ?: '—') }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <a href="{{ route('admin.bookings.show', $booking) }}" class="inline-flex items-center px-3 py-2 rounded text-white text-xs font-medium transition" style="background-color: #334155;">View Job</a>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                @if($reviewStatus === 1)
                  <form method="POST" action="{{ route('admin.reviews.approve', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded text-white text-xs font-medium transition" style="background-color: #059669;" onclick="return confirm('Approve this review request and send the email now?')">Approve</button>
                  </form>
                @elseif($reviewStatus === 2)
                  <span class="inline-flex items-center px-3 py-2 rounded text-xs font-medium" style="background-color: #D1FAE5; color: #047857;">Approved</span>
                @else
                  <span class="inline-flex items-center px-3 py-2 rounded text-gray-600 text-xs font-medium" style="background-color: #F3F4F6; color: #4B5563;">—</span>
                @endif
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                @if($reviewStatus === 1)
                  <form method="POST" action="{{ route('admin.reviews.reject', $booking) }}" class="inline">
                    @csrf
                    <button type="submit" class="px-3 py-2 rounded text-white text-xs font-medium transition" style="background-color: #DC2626;" onclick="return confirm('Reject this review request?')">Reject</button>
                  </form>
                @elseif($reviewStatus === 0)
                  <span class="inline-flex items-center px-3 py-2 rounded text-xs font-medium" style="background-color: #FEE2E2; color: #B91C1C;">Rejected</span>
                @else
                  <span class="inline-flex items-center px-3 py-2 rounded text-gray-600 text-xs font-medium" style="background-color: #F3F4F6; color: #4B5563;">—</span>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($bookings->hasPages())
      <div class="mt-4">
        {{ $bookings->links() }}
      </div>
    @endif

    <p class="text-xs text-gray-400 mt-3">
      Showing {{ $bookings->firstItem() }}–{{ $bookings->lastItem() }} of {{ $bookings->total() }} review approval requests
    </p>
  @endif
</div>
@endsection
