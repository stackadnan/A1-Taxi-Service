<div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm">From Zone</label>
      <select name="from_zone_id" class="w-full border rounded p-2">
        <option value="">Select from zone</option>
        @foreach($zones as $z)
        <option value="{{ $z->id }}" @if(isset($item) && $item->from_zone_id == $z->id) selected @endif>{{ $z->zone_name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm">To Zone</label>
      <select name="to_zone_id" class="w-full border rounded p-2">
        <option value="">Select to zone</option>
        @foreach($zones as $z)
        <option value="{{ $z->id }}" @if(isset($item) && $item->to_zone_id == $z->id) selected @endif>{{ $z->zone_name }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="block text-sm">Saloon Price</label>
      <input type="text" name="saloon_price" value="{{ $item->saloon_price ?? '' }}" class="w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm">Business Price</label>
      <input type="text" name="business_price" value="{{ $item->business_price ?? '' }}" class="w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm">MPV6 Price</label>
      <input type="text" name="mpv6_price" value="{{ $item->mpv6_price ?? '' }}" class="w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm">MPV8 Price</label>
      <input type="text" name="mpv8_price" value="{{ $item->mpv8_price ?? '' }}" class="w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm">Pricing Mode</label>
      <select name="pricing_mode" class="w-full border rounded p-2">
        <option value="">Select mode</option>
        <option value="flat" @if(isset($item) && $item->pricing_mode == 'flat') selected @endif>Flat</option>
        <option value="distance" @if(isset($item) && $item->pricing_mode == 'distance') selected @endif>Distance</option>
        <option value="zone" @if(isset($item) && $item->pricing_mode == 'zone') selected @endif>Zone</option>
      </select>
    </div>

    <div>
      <label class="block text-sm">Status</label>
      <select name="status" class="w-full border rounded p-2">
        <option value="active" @if(isset($item) && $item->status == 'active') selected @endif>Active</option>
        <option value="inactive" @if(isset($item) && $item->status == 'inactive') selected @endif>Inactive</option>
      </select>
    </div>
  </div>
</div>