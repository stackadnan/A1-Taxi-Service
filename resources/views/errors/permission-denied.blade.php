@extends('layouts.admin')

@section('title', 'Permission Denied')

@section('content')
<div class="max-w-xl mx-auto mt-6 bg-white border rounded-lg shadow-sm p-6">
  <h1 class="text-xl font-semibold text-gray-900 mb-2">Permission Denied</h1>
  <p class="text-gray-600 mb-4">{{ $permissionMessage ?? 'You do not have permission to view or edit this section.' }}</p>
  <div class="flex items-center gap-3">
    <button type="button" onclick="window.history.back();" class="px-4 py-2 bg-indigo-600 text-white rounded">Go Back</button>
    <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 border rounded text-gray-700">Dashboard</a>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var message = @json($permissionMessage ?? 'You do not have permission to view or edit this section.');
    if (typeof window.showAlert === 'function') {
      window.showAlert('Permission Denied', message);
    } else {
      alert(message);
    }
  });
</script>
@endsection