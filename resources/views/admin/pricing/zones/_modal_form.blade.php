<div>
  <form id="zone-form" method="POST" action="{{ isset($item) ? route('admin.pricing.zones.update', $item) : route('admin.pricing.zones.store') }}">
    @csrf
    @if(isset($item)) @method('PUT') @endif

    @include('admin.pricing.zones._form')

    <div class="mt-4 flex justify-end gap-2">
      <button type="button" data-action="close-modal" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
      <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
    </div>
  </form>


</div>