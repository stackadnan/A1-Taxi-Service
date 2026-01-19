@extends('layouts.admin')

@section('title', 'New Driver')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">New Driver</h1>

  <form method="post" action="{{ route('admin.drivers.store') }}">
    @include('admin.drivers._form')
  </form>
</div>
@endsection