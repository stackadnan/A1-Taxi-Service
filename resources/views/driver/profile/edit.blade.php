@extends('driver.layouts.app')

@section('title', 'Edit Profile')

@section('content')
<div class="bg-white rounded-lg shadow p-6 max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Edit Profile & Documents</h1>

  @if(session('success'))
    <div class="mb-4 text-green-600">{{ session('success') }}</div>
  @endif

  <form method="POST" action="{{ route('driver.profile.update') }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input name="name" class="mt-1 block w-full border rounded p-2" value="{{ old('name', $driver->name) }}">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Phone</label>
        <input name="phone" class="mt-1 block w-full border rounded p-2" value="{{ old('phone', $driver->phone) }}">
      </div>
      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input name="email" class="mt-1 block w-full border rounded p-2" value="{{ old('email', $driver->email) }}">
      </div>

      {{-- Documents --}}
      <div class="col-span-1 md:col-span-2">
        <h3 class="text-lg font-semibold mt-4">Driver Documents</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
          <div>
            <label class="block text-sm font-medium text-gray-700">Driving License</label>
            <input type="file" name="driving_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
            <input type="date" name="driving_license_expiry" value="{{ old('driving_license_expiry', optional($driver->driving_license_expiry)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2">
            @if($driver->driving_license)
              <a href="{{ asset('storage/' . $driver->driving_license) }}" class="text-xs text-blue-600">View</a>
            @endif
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Private Hire Drivers License</label>
            <input type="file" name="private_hire_drivers_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
            <input type="date" name="private_hire_drivers_license_expiry" value="{{ old('private_hire_drivers_license_expiry', optional($driver->private_hire_drivers_license_expiry)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2">
            @if($driver->private_hire_drivers_license)
              <a href="{{ asset('storage/' . $driver->private_hire_drivers_license) }}" class="text-xs text-blue-600">View</a>
            @endif
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle Insurance</label>
            <input type="file" name="private_hire_vehicle_insurance" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
            <input type="date" name="private_hire_vehicle_insurance_expiry" value="{{ old('private_hire_vehicle_insurance_expiry', optional($driver->private_hire_vehicle_insurance_expiry)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2">
            @if($driver->private_hire_vehicle_insurance)
              <a href="{{ asset('storage/' . $driver->private_hire_vehicle_insurance) }}" class="text-xs text-blue-600">View</a>
            @endif
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle License</label>
            <input type="file" name="private_hire_vehicle_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
            <input type="date" name="private_hire_vehicle_license_expiry" value="{{ old('private_hire_vehicle_license_expiry', optional($driver->private_hire_vehicle_license_expiry)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2">
            @if($driver->private_hire_vehicle_license)
              <a href="{{ asset('storage/' . $driver->private_hire_vehicle_license) }}" class="text-xs text-blue-600">View</a>
            @endif
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle MOT</label>
            <input type="file" name="private_hire_vehicle_mot" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
            <input type="date" name="private_hire_vehicle_mot_expiry" value="{{ old('private_hire_vehicle_mot_expiry', optional($driver->private_hire_vehicle_mot_expiry)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2">
            @if($driver->private_hire_vehicle_mot)
              <a href="{{ asset('storage/' . $driver->private_hire_vehicle_mot) }}" class="text-xs text-blue-600">View</a>
            @endif
          </div>

        </div>
      </div>
    </div>

    <div class="mt-4">
      <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
      <a href="{{ route('driver.documents.expired') }}" class="ml-3 text-sm text-gray-600">Back</a>
    </div>
  </form>
</div>
@endsection
