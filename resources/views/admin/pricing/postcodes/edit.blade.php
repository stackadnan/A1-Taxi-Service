@extends('layouts.admin')

@section('title','Edit Postcode Charge')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Edit Postcode Charge</h1>

  <form method="POST" action="{{ route('admin.pricing.postcodes.update', $postcode) }}">
    @csrf
    @method('PUT')
    @include('admin.pricing.postcodes._form')
  </form>
</div>
@endsection