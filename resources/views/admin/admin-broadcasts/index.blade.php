@extends('layouts.admin')

@section('title', 'Admin Broadcast Message')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <div>
      <h1 class="text-2xl font-semibold">Admin Broadcast Message</h1>
      <p class="text-sm text-gray-500 mt-1">Manage broadcast messages shown on the admin panel header.</p>
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

  <form action="{{ route('admin.admin-broadcasts.store') }}" method="POST" class="border border-gray-200 rounded-lg p-4 mb-8">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
      <div class="md:col-span-2">
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title</label>
        <input
          type="text"
          id="title"
          name="title"
          value="{{ old('title') }}"
          placeholder="Example: System Notice"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
          required
        >
      </div>

      <div>
        <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">Schedule</label>
        <input
          type="datetime-local"
          id="scheduled_at"
          name="scheduled_at"
          value="{{ old('scheduled_at') }}"
          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        >
      </div>
    </div>

    <div class="mb-4">
      <label for="message" class="block text-sm font-medium text-gray-700 mb-1">Message</label>
      <textarea
        id="message"
        name="message"
        rows="4"
        placeholder="Write the broadcast message for admin panel"
        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400"
        required
      >{{ old('message') }}</textarea>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">
        Save Broadcast Message
      </button>
    </div>
  </form>

  <h2 class="text-lg font-semibold mb-3">Recent Admin Broadcasts</h2>

  @if($broadcasts->isEmpty())
    <div class="p-6 text-center text-sm text-gray-500 border border-dashed border-gray-300 rounded-lg">
      No admin broadcast messages yet.
    </div>
  @else
    <div class="overflow-x-auto border border-gray-200 rounded-lg">
      <table class="min-w-full text-sm">
        <thead>
          <tr class="bg-gray-50 border-b border-gray-200 text-left">
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Title</th>
            <th class="px-4 py-3 font-semibold text-gray-600">Message</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Scheduled At</th>
            <th class="px-4 py-3 font-semibold text-gray-600 whitespace-nowrap">Created At</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @foreach($broadcasts as $broadcast)
            <tr class="hover:bg-gray-50">
              <td class="px-4 py-3 whitespace-nowrap font-medium text-gray-800">{{ $broadcast->title }}</td>
              <td class="px-4 py-3 text-gray-700 max-w-xl">{{ $broadcast->message }}</td>
              <td class="px-4 py-3 whitespace-nowrap text-gray-600">{{ optional($broadcast->scheduled_at)->format('d M Y H:i') ?: '-' }}</td>
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
