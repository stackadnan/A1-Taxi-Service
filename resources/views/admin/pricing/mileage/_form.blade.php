<div class="mb-5 text-sm text-red-700 bg-blue-100 border border-red-300 px-4 py-2 rounded">
Note: Mileage ranges must not overlap. Ensure the next range starts strictly greater than the previous range's end.

<br> 
For example:
<br> 
 (0–10.99 then 10–20) ❌

<br>

 (0–10.99 then 11–20) ✅
</div>


@if($errors->has('overlap'))
  <div class="mb-3 text-red-600">{{ $errors->first('overlap') }}</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div>
    <label class="block text-sm font-medium text-gray-700">Start Mile</label>
    <input type="number" step="0.01" name="start_mile" value="{{ old('start_mile', $mileage->start_mile ?? '') }}" class="mt-1 block w-full border rounded p-2" required>
    @error('start_mile')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">End Mile (optional)</label>
    <input type="number" step="0.01" name="end_mile" value="{{ old('end_mile', $mileage->end_mile ?? '') }}" class="mt-1 block w-full border rounded p-2">
    @error('end_mile')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror

    <label class="block text-sm font-medium text-gray-700 mt-4">Saloon Price</label>
    <input type="number" step="0.01" name="saloon_price" value="{{ old('saloon_price', $mileage->saloon_price ?? '') }}" class="mt-1 block w-full border rounded p-2">
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Business Price</label>
    <input type="number" step="0.01" name="business_price" value="{{ old('business_price', $mileage->business_price ?? '') }}" class="mt-1 block w-full border rounded p-2">

    <label class="block text-sm font-medium text-gray-700 mt-4">MPV6 Price</label>
    <input type="number" step="0.01" name="mpv6_price" value="{{ old('mpv6_price', $mileage->mpv6_price ?? '') }}" class="mt-1 block w-full border rounded p-2">

    <label class="block text-sm font-medium text-gray-700 mt-4">MPV8 Price</label>
    <input type="number" step="0.01" name="mpv8_price" value="{{ old('mpv8_price', $mileage->mpv8_price ?? '') }}" class="mt-1 block w-full border rounded p-2">

    <label class="flex items-center gap-2 mt-4"><input type="checkbox" name="is_fixed_charge" value="1" {{ old('is_fixed_charge', $mileage->is_fixed_charge ?? false) ? 'checked' : '' }}> Fixed charge</label>

    <label class="block text-sm font-medium text-gray-700 mt-4">Status</label>
    <select name="status" class="mt-1 block w-full border rounded p-2">
      <option value="active" {{ old('status', $mileage->status ?? 'active') === 'active' ? 'selected' : '' }}>Active</option>
      <option value="inactive" {{ old('status', $mileage->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
  </div>
</div>