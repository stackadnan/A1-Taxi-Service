@php $isEdit = isset($mileage) && $mileage->id; @endphp
<form id="mileage-form" method="POST" action="{{ $isEdit ? route('admin.pricing.mileage.update', $mileage) : route('admin.pricing.mileage.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  @include('admin.pricing.mileage._form')

  <div class="mt-4">
    <button type="submit" class="px-4 py-2 {{ $isEdit ? 'bg-green-600' : 'bg-indigo-600' }} text-white rounded">{{ $isEdit ? 'Save' : 'Create' }}</button>
    <button type="button" data-action="close-modal" class="px-4 py-2 border rounded text-gray-700">Cancel</button>
  </div>
</form>