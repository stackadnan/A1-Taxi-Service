@extends('layouts.admin')

@section('content')
  <h1 class="text-2xl font-semibold mb-4">Zone Charges</h1>
  @include('admin.pricing._tabs')
  @include('admin.pricing.zones._list')
@endsection