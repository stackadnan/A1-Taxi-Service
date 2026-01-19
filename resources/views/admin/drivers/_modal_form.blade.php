@php $isEdit = isset($driver) && $driver->id; @endphp
<form id="driver-form" method="POST" action="{{ $isEdit ? route('admin.drivers.update', $driver) : route('admin.drivers.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Name</label>
      <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input type="email" name="email" value="{{ old('email', $driver->email ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Vehicle Plate</label>
      <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate', $driver->vehicle_plate ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Make</label>
      <input type="text" name="vehicle_make" value="{{ old('vehicle_make', $driver->vehicle_make ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Model</label>
      <input type="text" name="vehicle_model" value="{{ old('vehicle_model', $driver->vehicle_model ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Coverage Area</label>
      <input type="text" name="coverage_area" value="{{ old('coverage_area', $driver->coverage_area ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Heathrow, Zone 1">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Badge Number</label>
      <input type="text" name="badge_number" value="{{ old('badge_number', $driver->badge_number ?? '') }}" class="mt-1 block w-full border rounded p-2">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Car type</label>
      <input type="text" name="car_type" value="{{ old('car_type', $driver->car_type ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Saloon, MPV6">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Car color</label>
      <input type="text" name="car_color" value="{{ old('car_color', $driver->car_color ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Black / Green">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Time slot</label>
      <input type="text" name="time_slot" value="{{ old('time_slot', $driver->time_slot ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., 24/7, 9-5">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Password</label>
      <input type="password" name="password" class="mt-1 block w-full border rounded p-2" autocomplete="new-password">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
      <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2" autocomplete="new-password">
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Status</label>
      <select name="status" class="mt-1 block w-full border rounded p-2">
        <option value="active" {{ (old('status', $driver->status ?? '')=='active') ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ (old('status', $driver->status ?? '')=='inactive') ? 'selected' : '' }}>Inactive</option>
        <option value="suspended" {{ (old('status', $driver->status ?? '')=='suspended') ? 'selected' : '' }}>Suspended</option>
      </select>
    </div>
  </div>

  <div class="mt-4 flex items-center gap-2">
    <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
    <button type="button" data-action="close-modal" class="ml-2 px-4 py-2 border rounded">Cancel</button>
  </div>
</form>