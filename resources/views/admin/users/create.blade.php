@extends('layouts.admin')

@section('title','Create User')

@section('content')
<div class="container mx-auto p-4">
  <form method="POST" action="{{ route('admin.users.store') }}">
    @csrf

    @include('admin.users._form')


  </form>
</div>
@endsection