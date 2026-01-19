@extends('layouts.admin')

@section('title','Edit User')

@section('content')
<div class="container mx-auto p-4">
  <h1 class="text-2xl font-bold mb-4">Edit User</h1>
  <form method="POST" action="{{ route('admin.users.update', $user) }}">
    @csrf
    @method('PUT')
    @include('admin.users._form')

  </form>
</div>
@endsection