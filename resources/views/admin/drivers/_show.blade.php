<div class="bg-white p-6 rounded shadow">
  <div class="flex justify-between items-center mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Driver: {{ $driver->name }}</h1>
      <div class="text-sm text-gray-500">Last active: {{ optional($driver->last_active_at)->format('Y-m-d H:i') }}</div>
    </div>
    <div class="flex items-center gap-2">
      @if(! (request()->ajax() || request()->get('partial')))
        <a href="{{ route('admin.drivers.edit', $driver) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Edit</a>
        <a href="{{ route('admin.drivers.index') }}" class="px-3 py-2 border rounded">Back</a>
      @else
        <!-- <button type="button" data-action="close-modal" class="px-3 py-2 border rounded">Close</button> -->
      @endif
    </div>
  </div>

  <div class="grid grid-cols-2 gap-6">
    <div>
      <h3 class="font-semibold">Contact</h3>
      <p>{{ $driver->phone }}</p>
      <p class="text-sm text-gray-500">{{ $driver->email }}</p>

      <h3 class="mt-4 font-semibold">Vehicle</h3>
      <p>{{ $driver->vehicle_make }} {{ $driver->vehicle_model }} — {{ $driver->vehicle_plate }}</p>

      <h3 class="mt-4 font-semibold">Coverage Area</h3>
      <p>{{ $driver->coverage_area ?? '-' }}</p>

      <h3 class="mt-4 font-semibold">Car</h3>
      <p>Type: {{ $driver->car_type ?? '-' }} — Color: {{ $driver->car_color ?? '-' }}</p>
      <p>Make & Model: {{ $driver->vehicle_make ?? '-' }} {{ $driver->vehicle_model ?? '' }} — Plate: {{ $driver->vehicle_plate ?? '-' }}</p>
    </div>

    <div>
      <div class="flex flex-col items-end">
        <div class="mb-4">
          @if($driver->driver_picture)
            <a href="{{ asset('storage/' . $driver->driver_picture) }}" target="_blank" title="View full image">
              <img src="{{ asset('storage/' . $driver->driver_picture) }}" alt="{{ $driver->name }}" class="h-24 w-24 object-cover rounded border" />
            </a>
          @else
            <div class="h-24 w-24 rounded-full bg-gray-100 flex items-center justify-center text-xl font-semibold text-gray-600 border">{{ strtoupper(substr($driver->name, 0, 1)) }}</div>
          @endif
        </div>

        <div class="text-right">
          <h3 class="font-semibold">Status & Stats</h3>
          <p>Status: {{ $driver->status ?? 'active' }}</p>
          <p>Time slot: {{ $driver->time_slot ?? '-' }}</p>
          <p>Rating: {{ $driver->rating ?? '-' }}</p>
          <p>Total assigned: {{ $driver->total_assigned }}</p>
          <p>Total completed: {{ $driver->total_completed }}</p>
        </div>
      </div>
    </div>
  </div>
</div>
