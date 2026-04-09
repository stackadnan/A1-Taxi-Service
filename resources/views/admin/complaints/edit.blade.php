@extends('layouts.admin')

@section('title', 'Edit Complainet / Lost Found')

@section('content')
<div class="max-w-4xl mx-auto bg-white p-6 rounded shadow space-y-6">
  <div class="flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Edit Complainet / Lost Found</h1>
    <a href="{{ route('admin.complaints.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition">Back</a>
  </div>

  @if($errors->any())
    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
      <ul class="list-disc pl-4 space-y-1">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-5">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Booking Id</label>
        <input type="text" name="booking_id" value="{{ old('booking_id', $complaint->booking_id) }}"
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
        <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
          <option value="new" {{ old('status', $complaint->status) === 'new' ? 'selected' : '' }}>New</option>
          <option value="pending" {{ old('status', $complaint->status) === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="resolved" {{ old('status', $complaint->status) === 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Name</label>
        <input type="text" name="name" value="{{ old('name', $complaint->name) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
        <input type="email" name="email" value="{{ old('email', $complaint->email) }}" required
               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">
      </div>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Conern</label>
      <textarea name="concern" rows="4" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('concern', $complaint->concern) }}</textarea>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700 mb-1">Lost Product Info</label>
      <textarea name="lost_found" rows="4" required
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400">{{ old('lost_found', $complaint->lost_found) }}</textarea>
    </div>

    <div class="flex justify-end">
      <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition">Save Changes</button>
    </div>
  </form>
</div>
@endsection
