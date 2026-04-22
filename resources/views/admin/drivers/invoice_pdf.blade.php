<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Driver Invoice</title>
    <style>
        body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111827; }
        .topbar { width: 100%; margin-bottom: 18px; }
        .topbar td { vertical-align: top; }
        .right { text-align: right; }
        .section-title { font-size: 13px; font-weight: 700; margin: 12px 0 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; font-weight: 700; }
        .text-right { text-align: right; }
        .totals-row td { font-weight: 700; background: #f9fafb; }
        .meta-block { margin-bottom: 10px; }
        .meta-line { margin: 0 0 2px; }
    </style>
</head>
<body>
    <table class="topbar">
        <tr>
            <td>
                <div class="meta-block">
                    <p class="meta-line">info@executiveairporttransfers.co.uk</p>
                    <p class="meta-line">+44 1582 555444</p>
                </div>
                <div class="meta-block">
                    <p class="meta-line"><strong>Invoice #:</strong> {{ $invoiceNumber }}</p>
                    <p class="meta-line"><strong>Invoice Date:</strong> {{ $invoiceDate }}</p>
                    <p class="meta-line"><strong>Period:</strong> {{ $startDate }} to {{ $endDate }}</p>
                </div>
            </td>
            <td class="right">
                <p class="meta-line"><strong>Payment for:</strong></p>
                <p class="meta-line">{{ $driver->name }}</p>
            </td>
        </tr>
    </table>

    <p class="section-title">Jobs summary</p>

    <table>
        <thead>
            <tr>
                <th>Booking ID</th>
                <th>Pickup Date/Time</th>
                <th>Total Fare £</th>
                <th>Booking Type</th>
                <th>Partial received by driver</th>
                <th class="text-right">Driver Fare £</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoiceJobs as $job)
                @php
                    $totalFare = (float) data_get($job, 'total_price', 0);
                    $driverPrice = (float) data_get($job, 'driver_price', 0);
                    $partialReceived = (float) data_get($job, 'partial_received_by_driver', 0);
                    $driverFare = (float) data_get($job, 'driver_fare', $driverPrice - $partialReceived);
                    $bookingType = (string) data_get($job, 'booking_type', '-');
                @endphp
                <tr>
                    <td>{{ data_get($job, 'booking_code', data_get($job, 'booking_id', '-')) }}</td>
                    <td>{{ data_get($job, 'pickup_date', '-') }} / {{ data_get($job, 'pickup_time', '-') }}</td>
                    <td>£{{ number_format($totalFare, 2) }}</td>
                    <td>{{ $bookingType !== '' ? ucfirst(str_replace('_', ' ', $bookingType)) : '-' }}</td>
                    <td>£{{ number_format($partialReceived, 2) }}</td>
                    <td class="text-right">£{{ number_format($driverFare, 2) }}</td>
                </tr>
            @endforeach
            <tr class="totals-row">
                <td colspan="5" class="text-right">Total Driver Fare</td>
                <td class="text-right">£{{ number_format((float) $invoiceTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table style="margin-top: 10px; width: 100%; border-collapse: collapse;">
        <tr>
            <td style="border: none; text-align: right; padding: 0;">
                <strong>Total Amount: £{{ number_format((float) $invoiceAmountTotal, 2) }}</strong>
            </td>
        </tr>
    </table>
</body>
</html>
