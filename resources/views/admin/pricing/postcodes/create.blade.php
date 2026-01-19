@extends('layouts.admin')

@section('title','Create Postcode Charge')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Create Postcode Charge</h1>

  @include('admin.pricing.postcodes._modal_form')
</div>
@endsection