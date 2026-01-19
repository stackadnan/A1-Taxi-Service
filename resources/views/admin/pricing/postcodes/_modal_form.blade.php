@php $isEdit = isset($postcode) && $postcode->id; @endphp
<form id="postcode-create-form" method="POST" action="{{ $isEdit ? route('admin.pricing.postcodes.update', $postcode) : route('admin.pricing.postcodes.store') }}">
  @csrf
  @if($isEdit) @method('PUT') @endif

  @include('admin.pricing.postcodes._form')
</form>