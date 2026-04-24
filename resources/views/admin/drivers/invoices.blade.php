@extends('layouts.admin')

@section('title', 'Driver Invoices')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Invoices for {{ $driver->name }}</h1>
      <p class="text-sm text-gray-600">All invoices generated for this driver.</p>
    </div>
    <div class="flex gap-2">
      <a href="{{ route('admin.drivers.jobs', $driver) }}" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Generate Invoice</a>
      <a href="{{ route('admin.drivers.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Back to Drivers</a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-800">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-800">
      {{ session('error') }}
    </div>
  @endif

  <div class="mb-4">
    <form method="get" action="{{ route('admin.drivers.invoices', $driver) }}" class="flex items-center gap-2 w-full md:w-1/2">
      <input type="text" name="q" value="{{ $q }}" placeholder="Search by invoice number" class="border rounded px-3 py-2 w-full" />
      <button class="px-3 py-2 bg-indigo-600 text-white rounded">Search</button>
    </form>
  </div>

  <div class="overflow-x-auto border rounded">
    <table class="w-full text-left">
      <thead class="bg-gray-50 border-b">
        <tr class="text-sm text-gray-600">
          <th class="p-3 font-medium">Invoice #</th>
          <th class="p-3 font-medium">Invoice Date</th>
          <th class="p-3 font-medium">Period</th>
          <th class="p-3 font-medium">Jobs</th>
          <th class="p-3 font-medium">Total Amount</th>
          <th class="p-3 font-medium">Driver Fare</th>
          <th class="p-3 font-medium">Status</th>
          <th class="p-3 font-medium text-right">Action</th>
        </tr>
      </thead>
      <tbody>
        @forelse($invoices as $invoice)
          <tr class="border-t">
            <td class="p-3 text-sm text-gray-900">{{ $invoice->invoice_number }}</td>
            <td class="p-3 text-sm text-gray-900">{{ optional($invoice->invoice_date)->toDateString() ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ optional($invoice->start_date)->toDateString() ?: '-' }} to {{ optional($invoice->end_date)->toDateString() ?: '-' }}</td>
            <td class="p-3 text-sm text-gray-900">{{ (int) $invoice->jobs_count }}</td>
            <td class="p-3 text-sm text-gray-900">£{{ number_format((float) $invoice->total_amount, 2) }}</td>
            <td class="p-3 text-sm text-gray-900">£{{ number_format((float) $invoice->total_driver_fare, 2) }}</td>
            <td class="p-3 text-sm text-gray-900">{{ ucfirst((string) $invoice->status) }}</td>
            <td class="p-3 text-sm text-right">
              <a href="{{ route('admin.drivers.invoices.show', ['driver' => $driver->id, 'invoice' => $invoice->id]) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">View</a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="p-8 text-center text-gray-500">No invoices found for this driver.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($invoices->hasPages())
    <div class="mt-4">
      {{ $invoices->links() }}
    </div>
  @endif
</div>
@endsection
