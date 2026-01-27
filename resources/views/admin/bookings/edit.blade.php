@extends('layouts.admin')

@section('title', 'Edit Booking ' . ($booking->booking_code ?? ''))

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-semibold">Edit Booking: {{ $booking->booking_code }}</h1>
    <a href="{{ route('admin.bookings.index', array_merge(request()->except('page'), ['section' => request()->get('section')])) }}" class="px-3 py-2 border rounded">Back</a>
  </div>

  @include('admin.bookings._edit')
</div>
@endsection