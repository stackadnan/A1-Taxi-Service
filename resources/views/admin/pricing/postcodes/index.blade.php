@extends('layouts.admin')

@section('title','Postcode Charges')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Postcode Charges</h1>

  @include('admin.pricing._tabs')

  @include('admin.pricing.postcodes._list')
</div>
@endsection