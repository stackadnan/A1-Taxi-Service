@extends('layouts.admin')

@section('title','Users')

@section('content')
<div class="container mx-auto p-4">
  <div class="mb-4">
    <div class="flex items-start justify-between">
      <div>
        <h1 class="text-2xl font-bold">Users management</h1>
        <p class="text-sm text-gray-500">View and edit users here</p>
      </div>

      <div class="flex items-center gap-4">
        <form method="GET" class="mb-0">
          <input type="search" name="q" value="{{ request('q') }}" placeholder="Search by name or email" class="border p-2 rounded w-64">
        </form>
        <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Add User</a>
      </div>
    </div>
  </div>

  <div class="overflow-auto bg-white border rounded">
    <table class="min-w-full divide-y">
      <thead class="bg-gray-50">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">ID</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Email</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Name</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Role</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">IP</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Status</th>
          <th class="px-4 py-3 text-left text-xs font-medium text-gray-600">Date Created</th>
          <th class="px-4 py-3 text-center text-xs font-medium text-gray-600">Edit</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y">
        @foreach($users as $user)
        <tr>
          <td class="px-4 py-3 text-sm text-gray-700">{{ $user->id }}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{ $user->email }}</td>
          <td class="px-4 py-3 text-sm text-gray-700">{{ $user->name }}</td>
          <td class="px-4 py-3 text-sm text-gray-700">
            @if($user->hasRole('Super Admin'))
              Admin
            @else
              {{ $user->roles->pluck('name')->first() ?? '-' }}
            @endif
          </td>
          <td class="px-4 py-3 text-sm text-gray-700">{{ $user->last_login_ip ?? '-' }}</td>
          <td class="px-4 py-3 text-sm">
            @if($user->is_active)
              <span class="text-green-600">Active</span>
            @else
              <span class="text-gray-500">Inactive</span>
            @endif
          </td>
          <td class="px-4 py-3 text-sm text-gray-500">{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
          <td class="px-4 py-3 text-center">
            <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center justify-center h-8 w-8 rounded border text-gray-600 hover:bg-gray-100" title="Edit">
              <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6-6 3 3-6 6H9v-3z"/></svg>
            </a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $users->links() }}
  </div>
</div>
@endsection