@extends('layouts.admin')

@section('title', 'Invoice Preview')

@section('content')
<div class="bg-white p-6 rounded shadow">
  <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6 text-sm text-gray-700">
    <div>
      <p>info@executiveairporttransfers.co.uk</p>
      <p>+44 1582 555444</p>
    </div>
    <div class="text-left md:text-right">
      <p class="font-semibold">Payment for:</p>
      <p>{{ $driver->name }}</p>
    </div>
  </div>

  <div class="flex items-center justify-between mb-4">
    <div>
      <h1 class="text-2xl font-semibold">Invoice Preview</h1>
      <p class="text-sm text-gray-600">Edit the draft, then send by email or directly to the driver app.</p>
    </div>
    <a href="{{ route('admin.drivers.jobs', ['driver' => $driver->id, 'start_date' => optional($invoice->start_date)->toDateString(), 'end_date' => optional($invoice->end_date)->toDateString()]) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200">Back to Jobs</a>
  </div>

  <form method="post" action="{{ route('admin.drivers.invoices.update_draft', ['driver' => $driver->id, 'invoice' => $invoice->id]) }}" id="invoice-draft-form">
    @csrf

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
      <div class="rounded border p-4 bg-gray-50">
        <p class="text-sm"><span class="font-semibold">Invoice #:</span> {{ $invoice->invoice_number }}</p>
        <p class="text-sm"><span class="font-semibold">Invoice Date:</span> {{ optional($invoice->invoice_date)->toDateString() }}</p>
        <p class="text-sm"><span class="font-semibold">Period:</span> {{ optional($invoice->start_date)->toDateString() }} to {{ optional($invoice->end_date)->toDateString() }}</p>
        <p class="text-sm"><span class="font-semibold">Status:</span> {{ ucfirst((string) $invoice->status) }}</p>
      </div>
      <div class="rounded border p-4 bg-gray-50">
        <p class="text-sm"><span class="font-semibold">Driver:</span> {{ $driver->name }}</p>
        <p class="text-sm"><span class="font-semibold">Email:</span> {{ $driver->email ?: 'No email set' }}</p>
        <p class="text-sm"><span class="font-semibold">Jobs:</span> {{ (int) $invoice->jobs_count }}</p>
        <p class="text-sm"><span class="font-semibold">Total Amount:</span> £{{ number_format((float) $invoiceAmountTotal, 2) }}</p>
        <p class="text-sm"><span class="font-semibold">Total Driver Fare:</span> <span id="summary-driver-fare">£{{ number_format((float) $invoiceTotal, 2) }}</span></p>
        <p class="text-sm"><span class="font-semibold">Partial Received Total:</span> <span id="summary-partial-total">£0.00</span></p>
        @if($invoice->sent_at)
          <p class="text-sm"><span class="font-semibold">Sent At:</span> {{ $invoice->sent_at->format('Y-m-d H:i:s') }}</p>
        @endif
      </div>
    </div>

    <div class="mb-4 text-sm text-gray-600">Jobs summary</div>
    <div class="overflow-x-auto border rounded">
      <table class="w-full text-left">
        <thead class="bg-gray-50 border-b">
          <tr class="text-sm text-gray-600">
            <th class="p-3 font-medium">Booking ID</th>
            <th class="p-3 font-medium">Pickup Date/Time</th>
            <th class="p-3 font-medium">Total Fare £</th>
            <th class="p-3 font-medium">Booking Type</th>
            <th class="p-3 font-medium">Partial received by driver</th>
            <th class="p-3 font-medium text-right">Driver Fare £</th>
          </tr>
        </thead>
        <tbody>
          @forelse($lineItems as $index => $item)
            @php
              $totalPrice = (float) data_get($item, 'total_price', 0);
              $driverPrice = (float) data_get($item, 'driver_price', 0);
              $partialReceived = (float) data_get($item, 'partial_received_by_driver', 0);
              $driverFare = (float) data_get($item, 'driver_fare', $driverPrice - $partialReceived);
              $bookingType = (string) data_get($item, 'booking_type', '-');
            @endphp
            <tr class="border-t">
              <td class="p-3 text-sm text-gray-900">
                {{ data_get($item, 'booking_code', data_get($item, 'booking_id', '-')) }}
                <input type="hidden" name="line_items[{{ $index }}][booking_id]" value="{{ (int) data_get($item, 'booking_id', 0) }}">
              </td>
              <td class="p-3 text-sm text-gray-900">{{ data_get($item, 'pickup_date', '-') }} / {{ data_get($item, 'pickup_time', '-') }}</td>
              <td class="p-3 text-sm text-gray-900">£{{ number_format($totalPrice, 2) }}</td>
              <td class="p-3 text-sm text-gray-900">{{ $bookingType !== '' ? ucfirst(str_replace('_', ' ', $bookingType)) : '-' }}</td>
              <td class="p-3 text-sm text-gray-900">
                <input
                  type="number"
                  step="0.01"
                  min="0"
                  name="line_items[{{ $index }}][partial_received_by_driver]"
                  value="{{ number_format($partialReceived, 2, '.', '') }}"
                  class="partial-input w-32 border rounded px-2 py-1"
                  data-driver-price="{{ number_format($driverPrice, 2, '.', '') }}"
                >
              </td>
              <td class="p-3 text-sm text-gray-900 text-right">£<span class="line-driver-fare">{{ number_format($driverFare, 2) }}</span></td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-8 text-center text-gray-500">No invoice line items found.</td>
            </tr>
          @endforelse
          <tr class="border-t bg-gray-50">
            <td colspan="5" class="p-3 text-sm font-semibold text-right text-gray-700">Total</td>
            <td class="p-3 text-sm font-semibold text-right text-gray-900">£<span id="invoice-driver-total">{{ number_format((float) $invoiceTotal, 2) }}</span></td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="mt-5 flex flex-wrap gap-2">
      <button
        type="submit"
        formaction="{{ route('admin.drivers.invoices.send', ['driver' => $driver->id, 'invoice' => $invoice->id]) }}"
        class="inline-flex items-center px-4 py-2 bg-black text-white rounded hover:bg-gray-800 disabled:opacity-60 disabled:cursor-not-allowed"
        data-loading-label="{{ $invoice->status === 'sent' ? 'Resending email...' : 'Sending email...' }}"
      >
        <span data-btn-label>{{ $invoice->status === 'sent' ? 'Resend Email to Driver' : 'Send Email to Driver' }}</span>
      </button>
      <button
        type="submit"
        formaction="{{ route('admin.drivers.invoices.send_app', ['driver' => $driver->id, 'invoice' => $invoice->id]) }}"
        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 disabled:opacity-60 disabled:cursor-not-allowed"
        data-loading-label="Sending to driver app..."
      >
        <span data-btn-label>Send to Driver App</span>
      </button>
    </div>
  </form>
</div>

<script>
  (function () {
    const form = document.getElementById('invoice-draft-form');
    if (!form) {
      return;
    }

    const partialInputs = Array.from(form.querySelectorAll('.partial-input'));
    const totalFareEl = document.getElementById('invoice-driver-total');
    const summaryDriverFareEl = document.getElementById('summary-driver-fare');
    const summaryPartialTotalEl = document.getElementById('summary-partial-total');

    function parseAmount(value) {
      const numeric = parseFloat(value);
      return Number.isFinite(numeric) ? numeric : 0;
    }

    function formatAmount(value) {
      return value.toFixed(2);
    }

    function recalculateTotals() {
      let driverFareTotal = 0;
      let partialTotal = 0;

      partialInputs.forEach(function (input) {
        const row = input.closest('tr');
        if (!row) {
          return;
        }

        const driverPrice = parseAmount(input.getAttribute('data-driver-price'));
        const partialReceived = parseAmount(input.value);
        const lineDriverFare = driverPrice - partialReceived;

        partialTotal += partialReceived;
        driverFareTotal += lineDriverFare;

        const lineFareEl = row.querySelector('.line-driver-fare');
        if (lineFareEl) {
          lineFareEl.textContent = formatAmount(lineDriverFare);
        }
      });

      if (totalFareEl) {
        totalFareEl.textContent = formatAmount(driverFareTotal);
      }
      if (summaryDriverFareEl) {
        summaryDriverFareEl.textContent = '£' + formatAmount(driverFareTotal);
      }
      if (summaryPartialTotalEl) {
        summaryPartialTotalEl.textContent = '£' + formatAmount(partialTotal);
      }
    }

    partialInputs.forEach(function (input) {
      input.addEventListener('input', recalculateTotals);
      input.addEventListener('change', recalculateTotals);
    });

    recalculateTotals();

    form.addEventListener('submit', function (event) {
      const submitter = event.submitter;
      const allSubmitButtons = form.querySelectorAll('button[type="submit"]');

      allSubmitButtons.forEach(function (button) {
        button.disabled = true;
      });

      if (!submitter) {
        return;
      }

      const labelEl = submitter.querySelector('[data-btn-label]');
      const loadingLabel = submitter.getAttribute('data-loading-label') || 'Processing...';

      if (labelEl) {
        labelEl.textContent = loadingLabel;
      }

      if (!submitter.querySelector('svg[data-spinner="1"]')) {
        submitter.insertAdjacentHTML('afterbegin', '<svg data-spinner="1" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>');
      }
    });
  })();
</script>
@endsection
