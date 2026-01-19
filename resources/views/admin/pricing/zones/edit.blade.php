@extends('layouts.admin')

@section('title','Edit Zone Pricing')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <h1 class="text-2xl font-semibold mb-4">Edit Zone Pricing</h1>

  @include('admin.pricing._tabs')

  <div>
    @include('admin.pricing.zones._modal_form', ['zones' => $zones, 'item' => $item])
  </div>
</div>
@endsection