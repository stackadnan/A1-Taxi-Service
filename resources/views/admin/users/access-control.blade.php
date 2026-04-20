@extends('layouts.admin')

@section('title','User Access Control')

@section('content')
<div class="container mx-auto py-6">
  <div class="flex items-start justify-between gap-4 mb-4">
    <div>
      <h2 class="text-xl font-semibold mb-2">User Access Control</h2>
      <p class="text-sm text-gray-500">Use the checkboxes to grant or remove access for every role. The change applies to all users assigned to that role.</p>
    </div>
    <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border rounded text-gray-700 bg-white">Back to Users</a>
  </div>

  <form method="POST" action="{{ route('admin.permissions.update') }}">
    @csrf
    @include('admin.permissions._matrix')

    <div class="mt-4">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
    </div>
  </form>
</div>
@endsection