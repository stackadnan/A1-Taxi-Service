@extends('layouts.admin')

@section('title', 'Driver Broadcast Message')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Driver Broadcast Message</h1>
      <p class="text-sm text-gray-500 mt-1">Send one message to all drivers instantly.</p>
    </div>
    <div class="inline-flex items-center px-3 py-2 rounded-lg bg-indigo-50 text-indigo-700 text-sm font-medium">
      Total Drivers: {{ $driverCount }}
    </div>
  </div>

  @if ($errors->any())
    <div class="mb-4 p-3 rounded bg-red-50 border border-red-200 text-red-700">
      <ul class="list-disc ml-5 text-sm space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form action="{{ route('admin.driver-broadcasts.store') }}" method="POST" class="border border-gray-200 rounded-lg p-4 mb-8">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input
          type="text"
          id="title"
          name="title"
          value="{{ old('title') }}"
          placeholder="Example: Service Update"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
          required
        >
      </div>

      <div>
        <label for="broadcast_type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
        <select
          id="broadcast_type"
          name="broadcast_type"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
          @php
            $selectedType = old('broadcast_type', 'general');
          @endphp
          <option value="general" {{ $selectedType === 'general' ? 'selected' : '' }}>General</option>
          <option value="alert" {{ $selectedType === 'alert' ? 'selected' : '' }}>Alert</option>
          <option value="promo" {{ $selectedType === 'promo' ? 'selected' : '' }}>Promo</option>
        </select>
      </div>
    </div>

    <div class="mb-4">
      <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
      <textarea
        id="message"
        name="message"
        rows="4"
        placeholder="Write the message that every driver should receive"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        required
      >{{ old('message') }}</textarea>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 mt-2">
      <div class="flex items-center gap-2">
        <select name="council_id" id="council_id" class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
          <option value="">Select Council</option>
          @if(isset($councils) && $councils->count() > 0)
            @foreach($councils as $council)
              <option value="{{ $council->id }}">{{ $council->council_name }}</option>
            @endforeach
          @endif
        </select>
        <button type="submit" onclick="if(!document.getElementById('council_id').value){alert('Please select a council first.');return false;}" class="px-4 py-2 bg-black text-white rounded-lg text-sm font-medium hover:bg-teal-700 transition">
          Send to council driver
        </button>
      </div>
      <button type="submit" onclick="document.getElementById('council_id').value='';" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-white transition">
        Send To All Drivers
      </button>
    </div>
  </form>

  <h2 class="text-lg font-semibold mb-3">Recent Broadcasts</h2>

  @if($broadcasts->isEmpty())
    <div class="p-6 text-center text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg">
      No driver broadcast messages yet.
    </div>
  @else
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Title</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Message</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Type</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Status</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Created By</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Created At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($broadcasts as $broadcast)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">{{ $broadcast->title }}</td>
              <td class="px-4 py-3 text-gray-700 max-w-xl">{{ $broadcast->message }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ ucfirst($broadcast->broadcast_type ?? 'general') }}</td>
              <td class="px-4 py-3 whitespace-nowrap">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $broadcast->status === 'sent' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                  {{ ucfirst($broadcast->status) }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ optional($broadcast->creator)->name ?? 'System' }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ optional($broadcast->created_at)->format('d M Y H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @if($broadcasts->hasPages())
      <div class="mt-4">
        {{ $broadcasts->links() }}
      </div>
    @endif
  @endif
</div>
@endsection
