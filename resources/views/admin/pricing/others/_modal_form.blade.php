<form id="other-charge-form" method="POST" action="{{ isset($item) ? route('admin.pricing.others.update', $item) : route('admin.pricing.others.store') }}" class="space-y-4">
  @csrf
  @if(isset($item))
    @method('PUT')
  @endif

  <div>
    <label class="block text-sm font-medium mb-1">Charge Name</label>
    <input type="text" name="charge_name" value="{{ old('charge_name', $item->charge_name ?? '') }}" required class="w-full border rounded p-2" />
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium mb-1">Pickup Price (£)</label>
      <input type="number" step="0.01" name="pickup_price" value="{{ old('pickup_price', $item->pickup_price ?? '0.00') }}" class="w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm font-medium mb-1">Drop Off Price (£)</label>
      <input type="number" step="0.01" name="dropoff_price" value="{{ old('dropoff_price', $item->dropoff_price ?? '0.00') }}" class="w-full border rounded p-2" />
    </div>
  </div>

  <div>
    <label class="block text-sm font-medium mb-1">Active Status</label>
    <select name="active" required class="w-full border rounded p-2">
      <option value="1" {{ old('active', $item->active ?? 1) == 1 ? 'selected' : '' }}>Active</option>
      <option value="0" {{ old('active', $item->active ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>

  <div class="flex justify-end gap-2">
    <button type="button" data-action="close-modal" class="px-4 py-2 border rounded">Cancel</button>
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
  </div>
</form>
