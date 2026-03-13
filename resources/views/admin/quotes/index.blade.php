@extends('layouts.admin')

@section('title', 'Quotes')

@section('content')
<div class="bg-white p-6 rounded shadow">

  {{-- Header --}}
  <div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-semibold">Quote Requests</h1>
    <span class="text-sm text-gray-500">All quotes submitted via the public frontend</span>
  </div>

  {{-- Stats Cards --}}
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-indigo-50 border border-indigo-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-indigo-700">{{ $totalCount }}</div>
      <div class="text-xs text-indigo-500 mt-1">Total Quotes</div>
    </div>
    <div class="bg-green-50 border border-green-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-green-700">{{ $todayCount }}</div>
      <div class="text-xs text-green-500 mt-1">Today</div>
    </div>
    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-blue-700">{{ $oneWayCount }}</div>
      <div class="text-xs text-blue-500 mt-1">One-Way</div>
    </div>
    <div class="bg-purple-50 border border-purple-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-purple-700">{{ $returnCount }}</div>
      <div class="text-xs text-purple-500 mt-1">Return</div>
    </div>
  </div>

  {{-- Filters --}}
  <form method="GET" action="{{ route('admin.quotes.index') }}" class="flex flex-wrap gap-3 mb-6 items-end">
    <div class="flex-1 min-w-[200px]">
      <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
      <input type="text" name="search" value="{{ request('search') }}"
             placeholder="Ref, address, IP…"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Vehicle</label>
      <select name="vehicle_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All</option>
        @foreach(['saloon','business','mpv6','mpv8'] as $vt)
          <option value="{{ $vt }}" {{ request('vehicle_type') === $vt ? 'selected' : '' }}>{{ ucfirst($vt) }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Trip Type</label>
      <select name="trip_type" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All</option>
        <option value="one-way" {{ request('trip_type') === 'one-way' ? 'selected' : '' }}>One-Way</option>
        <option value="return"  {{ request('trip_type') === 'return'  ? 'selected' : '' }}>Return</option>
      </select>
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Pickup Date</label>
      <input type="date" name="date" value="{{ request('date') }}"
             class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div class="flex gap-2">
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Filter</button>
      @if(request()->hasAny(['search','vehicle_type','trip_type','date']))
        <a href="{{ route('admin.quotes.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Clear</a>
      @endif
    </div>
  </form>

  {{-- Table --}}
  @if($quotes->isEmpty())
    <div class="text-center py-16 text-gray-400">
      <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
      </svg>
      <p class="text-sm">No quote requests found.</p>
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Quote Ref</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Pickup</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Dropoff</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Pickup Date</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Vehicle</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Price</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Trip</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Linked Ref</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Source IP</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Source URL</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Submitted</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($quotes as $quote)
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="font-mono text-indigo-700 font-semibold text-xs bg-indigo-50 px-2 py-1 rounded">{{ $quote->quote_ref }}</span>
              </td>
              <td class="px-4 py-3 max-w-[200px]">
                <span class="block truncate text-gray-700" title="{{ $quote->pickup_address }}">{{ $quote->pickup_address }}</span>
              </td>
              <td class="px-4 py-3 max-w-[200px]">
                <span class="block truncate text-gray-700" title="{{ $quote->dropoff_address }}">{{ $quote->dropoff_address }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ $quote->pickup_date }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="capitalize text-gray-700">{{ $quote->vehicle_type }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap font-semibold text-gray-800">
              £  {{ number_format($quote->price, 2) }}
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                @if($quote->trip_type === 'return')
                  <span class="bg-purple-100 text-purple-700 text-xs font-medium px-2 py-0.5 rounded-full">Return</span>
                @else
                  <span class="bg-blue-100 text-blue-700 text-xs font-medium px-2 py-0.5 rounded-full">One-Way</span>
                @endif
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                @if($quote->linked_quote_ref)
                  <span class="font-mono text-xs text-gray-500">{{ $quote->linked_quote_ref }}</span>
                @else
                  <span class="text-gray-300">—</span>
                @endif
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-500 text-xs font-mono">{{ $quote->source_ip }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-500 text-xs font-mono">{{ $quote->source_url }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-500 text-xs">{{ $quote->created_at->format('d M Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{-- Pagination --}}
    @if($quotes->hasPages())
      <div class="mt-4">
        {{ $quotes->links() }}
      </div>
    @endif

    <p class="text-xs text-gray-400 mt-3">
      Showing {{ $quotes->firstItem() }}–{{ $quotes->lastItem() }} of {{ $quotes->total() }} quote requests
    </p>
  @endif

</div>
@endsection
