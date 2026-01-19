@extends('layouts.admin')

@section('title','User')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">{{ $user->name }}</h1>

  <div class="bg-white border rounded p-4">
    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Roles:</strong> {{ $user->roles->pluck('name')->join(', ') }}</p>
  </div>

  <div class="mt-4">
    <a href="{{ route('admin.users.edit', $user) }}" class="px-3 py-2 bg-green-600 text-white rounded">Edit</a>
    <a href="{{ route('admin.users.index') }}" class="ml-2">Back</a>
  </div>
</div>
@endsection