@extends('driver.layouts.app')

@section('title', 'My Invoices')

@section('content')
<div class="space-y-5">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div class="min-w-0">
            <h2 class="text-xl font-bold text-gray-900">My Invoices</h2>
            <p class="text-sm text-gray-600">Invoices sent by admin to your app. You can download PDF copies here.</p>
        </div>
        <a href="{{ route('driver.dashboard') }}" class="driver-back-btn self-start sm:self-auto">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Dashboard</span>
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow overflow-hidden border border-gray-100">
        <div class="md:hidden divide-y divide-gray-100">
            @forelse($invoices as $invoice)
                @php
                    $meta = is_array($invoice->meta) ? $invoice->meta : [];
                    $publishedAt = $meta['sent_to_app_at'] ?? null;
                @endphp
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div class="min-w-0">
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Invoice</p>
                            <p class="text-sm font-semibold text-gray-900 break-words">{{ $invoice->invoice_number }}</p>
                        </div>
                        <a href="{{ route('driver.invoices.download', $invoice) }}" class="shrink-0 inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                            <i class="fas fa-download"></i>
                            Download
                        </a>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-sm">
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Period</p>
                            <p class="text-gray-700">{{ optional($invoice->start_date)->toDateString() }} to {{ optional($invoice->end_date)->toDateString() }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Published</p>
                            <p class="text-gray-700">{{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('Y-m-d H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Total Amount</p>
                            <p class="text-gray-700">£{{ number_format((float) $invoice->total_amount, 2) }}</p>
                        </div>
                        <div>
                            <p class="text-[11px] uppercase tracking-wide text-gray-500 font-semibold">Driver Fare</p>
                            <p class="text-gray-700">£{{ number_format((float) $invoice->total_driver_fare, 2) }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-sm text-gray-500">No invoices are available yet.</div>
            @endforelse
        </div>

        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr class="text-xs uppercase tracking-wide text-gray-500">
                        <th class="px-4 py-3 font-semibold">Invoice</th>
                        <th class="px-4 py-3 font-semibold">Period</th>
                        <th class="px-4 py-3 font-semibold">Total Amount</th>
                        <th class="px-4 py-3 font-semibold">Driver Fare</th>
                        <th class="px-4 py-3 font-semibold">Published</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $invoice)
                        @php
                            $meta = is_array($invoice->meta) ? $invoice->meta : [];
                            $publishedAt = $meta['sent_to_app_at'] ?? null;
                        @endphp
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-sm text-gray-900 font-semibold">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ optional($invoice->start_date)->toDateString() }} to {{ optional($invoice->end_date)->toDateString() }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">£{{ number_format((float) $invoice->total_amount, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">£{{ number_format((float) $invoice->total_driver_fare, 2) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-700">{{ $publishedAt ? \Carbon\Carbon::parse($publishedAt)->format('Y-m-d H:i') : '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('driver.invoices.download', $invoice) }}" class="inline-flex items-center gap-2 px-3 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold rounded-lg">
                                    <i class="fas fa-download"></i>
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">No invoices are available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($invoices->hasPages())
            <div class="px-4 py-3 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
