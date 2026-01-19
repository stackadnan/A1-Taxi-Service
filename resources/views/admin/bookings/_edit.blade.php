<form id="booking-edit-form" method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="space-y-4">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Passenger name</label>
      <input type="text" name="passenger_name" value="{{ old('passenger_name',$booking->passenger_name) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input type="text" name="phone" value="{{ old('phone',$booking->phone) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input type="email" name="email" value="{{ old('email',$booking->email) }}" class="mt-1 block w-full border rounded p-2">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Vehicle type</label>
      <select name="vehicle_type" class="mt-1 block w-full border rounded p-2">
        <option value="">(Auto)</option>
        @foreach($vehicleTypes as $vt)
          <option value="{{ $vt }}" {{ (old('vehicle_type',$booking->vehicle_type) == $vt) ? 'selected' : '' }}>{{ $vt }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Pickup date</label>
      <input type="date" name="pickup_date" value="{{ old('pickup_date', optional($booking->pickup_date)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Pickup time</label>
      <input type="time" name="pickup_time" value="{{ old('pickup_time', $booking->pickup_time) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Pickup address</label>
    <input type="text" name="pickup_address" value="{{ old('pickup_address', $booking->pickup_address) }}" class="mt-1 block w-full border rounded p-2">
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700">Dropoff address</label>
    <input type="text" name="dropoff_address" value="{{ old('dropoff_address', $booking->dropoff_address) }}" class="mt-1 block w-full border rounded p-2">
  </div>

  <div class="grid grid-cols-3 gap-4">
    <div>
      <label class="inline-flex items-center">
        <input type="hidden" name="meet_and_greet" value="0">
        <input type="checkbox" name="meet_and_greet" value="1" {{ (old('meet_and_greet', $booking->meet_and_greet) ? 'checked' : '') }}>
        <span class="ml-2">Meet &amp; Greet</span>
      </label>
    </div>
    <div>
      <label class="inline-flex items-center">
        <input type="hidden" name="baby_seat" value="0">
        <input type="checkbox" name="baby_seat" value="1" {{ (old('baby_seat', $booking->baby_seat) ? 'checked' : '') }}>
        <span class="ml-2">Baby seat</span>
      </label>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Baby seat age</label>
      <input type="text" name="baby_seat_age" value="{{ old('baby_seat_age', $booking->baby_seat_age) }}" class="mt-1 block w-full border rounded p-2">
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Message to driver</label>
    <textarea name="message_to_driver" class="mt-1 block w-full border rounded p-2">{{ old('message_to_driver',$booking->message_to_driver) }}</textarea>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" class="mt-1 block w-full border rounded p-2">
      <option value="">(Keep current)</option>
      @foreach($statuses as $st)
        <option value="{{ $st->name }}" {{ (old('status') == $st->name) ? 'selected' : '' }}>{{ $st->name }}</option>
      @endforeach
    </select>
  </div>

  <div class="pt-4">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update booking</button>
  </div>
</form>