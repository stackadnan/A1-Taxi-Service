@extends('driver.layouts.app')

@section('title', 'Expired & Expiring Documents')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
  <h1 class="text-2xl font-bold mb-4">Expired & Expiring Documents</h1>

  @php
    $docs = $documentIssues ?? [];
  @endphp

  @if(count($docs) === 0)
    <div class="text-green-600">All your documents are up to date. Thank you!</div>
  @else
    <div class="space-y-3">
      <p class="text-sm text-gray-700">Please update the following documents to continue receiving assignments. <span class="text-xs text-yellow-600">Items marked "Expiring" will expire within 15 days.</span></p>
      <div class="mt-4 border rounded p-4 bg-red-50">
        @foreach($docs as $doc)
          <div class="mb-3">
            <div class="font-medium text-gray-800">{{ $doc['label'] }}</div>
            @if($doc['status'] === 'expired')
              <div class="text-sm text-red-600">Expired on {{ $doc['expiry'] }}</div>
            @else
              <div class="text-sm text-yellow-600">Expires on {{ $doc['expiry'] }} &nbsp; <span class="ml-2 text-xs font-medium">Expiring</span></div>
            @endif
          </div>
        @endforeach
      </div>

      <div class="mt-4">
        <a href="{{ route('driver.profile.edit') ?? '#' }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Update Documents</a>
        <span class="ml-3 text-sm text-gray-600">If you need assistance, contact support.</span>
      </div>
    </div>
  @endif
</div>
@endsection
