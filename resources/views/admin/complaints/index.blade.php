@extends('layouts.admin')

@section('title', 'Complainet / Lost Found')

@section('content')
<div class="bg-white p-6 rounded shadow space-y-6">
  <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
    <div>
      <h1 class="text-2xl font-semibold">Complainet / Lost Found</h1>
      <p class="text-sm text-gray-500 mt-1">Track customer complaint and lost item submissions</p>
    </div>
  </div>

  @if(session('status'))
    <div class="rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
      {{ session('status') }}
    </div>
  @endif

  <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
    <div class="bg-slate-50 border border-slate-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-slate-700">{{ $totalCount }}</div>
      <div class="text-xs text-slate-500 mt-1">Total</div>
    </div>
    <div class="bg-blue-50 border border-blue-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-blue-700">{{ $newCount }}</div>
      <div class="text-xs text-blue-600 mt-1">New</div>
    </div>
    <div class="bg-amber-50 border border-amber-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-amber-700">{{ $pendingCount }}</div>
      <div class="text-xs text-amber-600 mt-1">Pending</div>
    </div>
    <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-4 text-center">
      <div class="text-2xl font-bold text-emerald-700">{{ $resolvedCount }}</div>
      <div class="text-xs text-emerald-600 mt-1">Resolved</div>
    </div>
  </div>

  <form method="GET" action="{{ route('admin.complaints.index') }}" class="flex flex-wrap gap-3 items-end">
    <div class="flex-1 min-w-[220px]">
      <label class="block text-xs font-medium text-gray-600 mb-1">Search</label>
      <input type="text" name="search" value="{{ request('search') }}" placeholder="Booking id, name, email"
             class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
    </div>
    <div>
      <label class="block text-xs font-medium text-gray-600 mb-1">Status</label>
      <select name="status" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
        <option value="">All</option>
        <option value="new" {{ request('status') === 'new' ? 'selected' : '' }}>New</option>
        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
        <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
      </select>
    </div>
    <div class="flex gap-2">
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Filter</button>
      @if(request()->hasAny(['search','status']))
        <a href="{{ route('admin.complaints.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Clear</a>
      @endif
    </div>
  </form>

  @if($complaints->isEmpty())
    <div class="text-center py-16 text-gray-400">
      <p class="text-sm">No complainet/lost found records found.</p>
    </div>
  @else
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Booking Id</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Name</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Email</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Conern</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Lost Found</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Stats</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Edit</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($complaints as $complaint)
            @php
              $status = $complaint->status;
              $statusClass = match ($status) {
                'new' => 'bg-blue-100 text-blue-700',
                'pending' => 'bg-amber-100 text-amber-700',
                'resolved' => 'bg-emerald-100 text-emerald-700',
                default => 'bg-gray-100 text-gray-700',
              };
            @endphp
            <tr class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 whitespace-nowrap">{{ $complaint->booking_id ?: '—' }}</td>
              <td class="px-4 py-3 text-gray-700">{{ $complaint->name }}</td>
              <td class="px-4 py-3 text-gray-700">{{ $complaint->email }}</td>
              <td class="px-4 py-3 max-w-[280px]"><span class="block truncate" title="{{ $complaint->concern }}">{{ $complaint->concern }}</span></td>
              <td class="px-4 py-3 max-w-[280px]"><span class="block truncate" title="{{ $complaint->lost_found }}">{{ $complaint->lost_found }}</span></td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium capitalize {{ $statusClass }}">{{ $status }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <a href="{{ route('admin.complaints.edit', $complaint) }}" class="inline-flex items-center px-3 py-2 rounded  text-black text-xs font-medium hover:bg-slate-800 transition">Edit</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($complaints->hasPages())
      <div class="mt-4">
        {{ $complaints->links() }}
      </div>
    @endif
  @endif
</div>
@endsection
