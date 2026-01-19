@extends('layouts.admin')

@section('title','Mileage Charges')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Mileage Charges</h1>

  @include('admin.pricing._tabs')

  @include('admin.pricing.mileage._list')
</div>
@endsection