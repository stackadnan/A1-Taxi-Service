@extends('layouts.admin')

@section('title', 'Driver ' . ($driver->name ?? ''))

@section('content')
  @include('admin.drivers._show')
@endsection