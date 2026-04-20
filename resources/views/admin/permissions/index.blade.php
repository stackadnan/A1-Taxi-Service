@extends('layouts.admin')

@section('content')
<div class="container mx-auto py-6">
  <h2 class="text-xl font-semibold mb-2">Role Permissions</h2>
  <p class="text-sm text-gray-500 mb-4">Changes here apply to every user assigned to each role.</p>

  <form method="POST" action="{{ route('admin.permissions.update') }}">
    @csrf
    @include('admin.permissions._matrix')

    <div class="mt-4">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
    </div>
  </form>
</div>
@endsection