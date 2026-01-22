@php $isEdit = isset($driver) && $driver->id; @endphp
<form id="driver-form" method="POST" action="{{ $isEdit ? route('admin.drivers.update', $driver) : route('admin.drivers.store') }}" enctype="multipart/form-data">
  @csrf
  @if($isEdit) @method('PUT') @endif

  <div class="max-h-[60vh] overflow-y-auto px-2 pr-4" style="max-height:60vh; overflow:auto; -webkit-overflow-scrolling: touch;">
    
    {{-- Driver Documents Section --}}
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Driver Documents</h3>
      <div class="grid grid-cols-2 gap-4">
        
        {{-- Driving License --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Driving License</label>
          <input type="file" name="driving_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
          @if($isEdit && $driver->driving_license)
            <a href="{{ asset('storage/' . $driver->driving_license) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View Current</a>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
          <input type="date" name="driving_license_expiry" value="{{ old('driving_license_expiry', $driver->driving_license_expiry ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        {{-- Private Hire Drivers License --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Private Hire Drivers License</label>
          <input type="file" name="private_hire_drivers_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
          @if($isEdit && $driver->private_hire_drivers_license)
            <a href="{{ asset('storage/' . $driver->private_hire_drivers_license) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View Current</a>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
          <input type="date" name="private_hire_drivers_license_expiry" value="{{ old('private_hire_drivers_license_expiry', $driver->private_hire_drivers_license_expiry ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        {{-- Private Hire Vehicle Insurance --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle Insurance</label>
          <input type="file" name="private_hire_vehicle_insurance" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
          @if($isEdit && $driver->private_hire_vehicle_insurance)
            <a href="{{ asset('storage/' . $driver->private_hire_vehicle_insurance) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View Current</a>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
          <input type="date" name="private_hire_vehicle_insurance_expiry" value="{{ old('private_hire_vehicle_insurance_expiry', $driver->private_hire_vehicle_insurance_expiry ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        {{-- Private Hire Vehicle License --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle License</label>
          <input type="file" name="private_hire_vehicle_license" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
          @if($isEdit && $driver->private_hire_vehicle_license)
            <a href="{{ asset('storage/' . $driver->private_hire_vehicle_license) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View Current</a>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
          <input type="date" name="private_hire_vehicle_license_expiry" value="{{ old('private_hire_vehicle_license_expiry', $driver->private_hire_vehicle_license_expiry ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        {{-- Private Hire Vehicle MOT --}}
        <div>
          <label class="block text-sm font-medium text-gray-700">Private Hire Vehicle MOT</label>
          <input type="file" name="private_hire_vehicle_mot" class="mt-1 block w-full border rounded p-2 text-sm" accept=".pdf,.jpg,.jpeg,.png">
          @if($isEdit && $driver->private_hire_vehicle_mot)
            <a href="{{ asset('storage/' . $driver->private_hire_vehicle_mot) }}" target="_blank" class="text-xs text-blue-600 hover:underline">View Current</a>
          @endif
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Expiry Date</label>
          <input type="date" name="private_hire_vehicle_mot_expiry" value="{{ old('private_hire_vehicle_mot_expiry', $driver->private_hire_vehicle_mot_expiry ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

      </div>
    </div>

    {{-- Driver Info Section --}}
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Driver Info</h3>
      <div class="grid grid-cols-2 gap-4">
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Full Name <span class="text-red-500">*</span></label>
          <input type="text" name="name" value="{{ old('name', $driver->name ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Phone Number</label>
          <input type="text" name="phone" value="{{ old('phone', $driver->phone ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Email Address</label>
          <input type="email" name="email" value="{{ old('email', $driver->email ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Council</label>
          <select name="council_id" class="mt-1 block w-full border rounded p-2">
            <option value="">Select an Option</option>
            <option value="">Luton Borough Council</option>
            <option value="">Bradford (BD7 1PU)</option>
            <option value="">Birmingham (B6 5RQ)</option>
            <option value="">Manchester (M60 2LA) </option>
            @if(isset($councils) && count($councils) > 0)
              @foreach($councils as $council)
                <option value="{{ $council->id }}" {{ old('council_id', $driver->council_id ?? '') == $council->id ? 'selected' : '' }}>
                  {{ $council->council_name }}
                </option>
              @endforeach
            @endif
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Driver Lives</label>
          <input type="text" name="driver_lives" value="{{ old('driver_lives', $driver->driver_lives ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., London">
        </div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Driver Address</label>
          <textarea name="driver_address" rows="2" class="mt-1 block w-full border rounded p-2">{{ old('driver_address', $driver->driver_address ?? '') }}</textarea>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Driver License Number</label>
          <input type="text" name="license_number" value="{{ old('license_number', $driver->license_number ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Working Hours</label>
          <input type="text" name="working_hours" value="{{ old('working_hours', $driver->working_hours ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., 24/7, 9-5">
        </div>

        {{-- Bank Details --}}
        <div class="col-span-2">
          <h4 class="text-sm font-semibold text-gray-700 mt-2 mb-2">Bank Details</h4>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Bank Name</label>
          <input type="text" name="bank_name" value="{{ old('bank_name', $driver->bank_name ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Account Title</label>
          <input type="text" name="account_title" value="{{ old('account_title', $driver->account_title ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Sort Code</label>
          <input type="text" name="sort_code" value="{{ old('sort_code', $driver->sort_code ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="XX-XX-XX">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Account Number</label>
          <input type="text" name="account_number" value="{{ old('account_number', $driver->account_number ?? '') }}" class="mt-1 block w-full border rounded p-2">
        </div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Driver's Picture</label>
          <input type="file" name="driver_picture" class="mt-1 block w-full border rounded p-2 text-sm" accept="image/*">
          @if($isEdit && $driver->driver_picture)
            <div class="mt-2">
              <img src="{{ asset('storage/' . $driver->driver_picture) }}" alt="Driver" class="h-20 w-20 object-cover rounded">
            </div>
          @endif
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Password</label>
          <input type="password" name="password" class="mt-1 block w-full border rounded p-2" autocomplete="new-password">
          @if($isEdit)
            <small class="text-gray-500">Leave blank to keep current password</small>
          @endif
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
          <input type="password" name="password_confirmation" class="mt-1 block w-full border rounded p-2" autocomplete="new-password">
        </div>

      </div>
    </div>

    {{-- Vehicle Info Section --}}
    <div class="mb-6">
      <h3 class="text-lg font-semibold text-gray-800 mb-4 border-b pb-2">Vehicle Info</h3>
      <div class="grid grid-cols-2 gap-4">
        
        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Type</label>
          <select name="car_type" class="mt-1 block w-full border rounded p-2">
            <option value="">Select Type</option>
            <option value="Saloon" {{ old('car_type', $driver->car_type ?? '') == 'Saloon' ? 'selected' : '' }}>Saloon</option>
            <option value="Business" {{ old('car_type', $driver->car_type ?? '') == 'Business' ? 'selected' : '' }}>Business</option>
            <option value="MPV6" {{ old('car_type', $driver->car_type ?? '') == 'MPV6' ? 'selected' : '' }}>MPV6</option>
            <option value="MPV8" {{ old('car_type', $driver->car_type ?? '') == 'MPV8' ? 'selected' : '' }}>MPV8</option>
          </select>
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Make</label>
          <input type="text" name="vehicle_make" value="{{ old('vehicle_make', $driver->vehicle_make ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Toyota">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Model</label>
          <input type="text" name="vehicle_model" value="{{ old('vehicle_model', $driver->vehicle_model ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Prius">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Colour</label>
          <input type="text" name="car_color" value="{{ old('car_color', $driver->car_color ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., Black / Green">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Passenger Capacity</label>
          <input type="number" name="passenger_capacity" value="{{ old('passenger_capacity', $driver->passenger_capacity ?? '') }}" class="mt-1 block w-full border rounded p-2" min="1" max="20">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Luggage Capacity</label>
          <input type="number" name="luggage_capacity" value="{{ old('luggage_capacity', $driver->luggage_capacity ?? '') }}" class="mt-1 block w-full border rounded p-2" min="0" max="50">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle Registration Number</label>
          <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate', $driver->vehicle_plate ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., AB12 CDE">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Vehicle License Number</label>
          <input type="text" name="vehicle_license_number" value="{{ old('vehicle_license_number', $driver->vehicle_license_number ?? '') }}" class="mt-1 block w-full border rounded p-2">
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
          <label class="block text-sm font-medium text-gray-700">Time Slot</label>
          <input type="text" name="time_slot" value="{{ old('time_slot', $driver->time_slot ?? '') }}" class="mt-1 block w-full border rounded p-2" placeholder="e.g., 24/7, 9-5">
        </div>

        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select name="status" class="mt-1 block w-full border rounded p-2">
            <option value="active" {{ old('status', $driver->status ?? 'active') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $driver->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            <option value="suspended" {{ old('status', $driver->status ?? '') == 'suspended' ? 'selected' : '' }}>Suspended</option>
          </select>
        </div>

        <div class="col-span-2">
          <label class="block text-sm font-medium text-gray-700">Vehicle Pictures (Multiple)</label>
          <input type="file" name="vehicle_pictures[]" class="mt-1 block w-full border rounded p-2 text-sm" accept="image/*" multiple>
          @if($isEdit && $driver->vehicle_pictures && is_array($driver->vehicle_pictures))
            <div class="mt-2 flex gap-2 flex-wrap">
              @foreach($driver->vehicle_pictures as $pic)
                <img src="{{ asset('storage/' . $pic) }}" alt="Vehicle" class="h-20 w-20 object-cover rounded">
              @endforeach
            </div>
          @endif
        </div>

      </div>
    </div>

  </div>

  <div class="mt-4 flex items-center gap-2 border-t pt-4">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save</button>
    <button type="button" data-action="close-modal" class="ml-2 px-4 py-2 border rounded hover:bg-gray-100">Cancel</button>
  </div>
</form>