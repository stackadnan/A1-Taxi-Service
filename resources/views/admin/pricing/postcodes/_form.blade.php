<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
  <div>
    <label class="block text-sm font-medium text-gray-700">Pickup Postcode</label>
    <input type="text" name="pickup_postcode" value="{{ old('pickup_postcode', $postcode->pickup_postcode ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    @error('pickup_postcode')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Dropoff Postcode</label>
    <input type="text" name="dropoff_postcode" value="{{ old('dropoff_postcode', $postcode->dropoff_postcode ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    @error('dropoff_postcode')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Saloon Price</label>
    <input type="number" step="0.01" name="saloon_price" value="{{ old('saloon_price', $postcode->saloon_price ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('saloon_price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Business Price</label>
    <input type="number" step="0.01" name="business_price" value="{{ old('business_price', $postcode->business_price ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('business_price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">MPV6 Price</label>
    <input type="number" step="0.01" name="mpv6_price" value="{{ old('mpv6_price', $postcode->mpv6_price ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('mpv6_price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">MPV8 Price</label>
    <input type="number" step="0.01" name="mpv8_price" value="{{ old('mpv8_price', $postcode->mpv8_price ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('mpv8_price')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" class="mt-1 block w-full border rounded p-2">
      <option value="active" {{ old('status', $postcode->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $postcode->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>

  <div class="flex items-center justify-end gap-2">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">{{ isset($isEdit) && $isEdit ? 'Update' : 'Create' }}</button>
    <button type="button" data-action="close-modal" class="px-4 py-2 bg-white border rounded">Cancel</button>
  </div>
</div>