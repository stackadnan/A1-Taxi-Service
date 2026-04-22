<p>Hello {{ $driver->name }},</p>

<p>Please find attached your invoice for completed jobs between <strong>{{ $startDate }}</strong> and <strong>{{ $endDate }}</strong>.</p>

<p>
Invoice Number: <strong>{{ $invoiceNumber }}</strong><br>
Invoice Date: <strong>{{ $invoiceDate }}</strong><br>
Completed Jobs: <strong>{{ $invoiceJobs->count() }}</strong><br>
Total Amount: <strong>£{{ number_format((float) $invoiceAmountTotal, 2) }}</strong><br>
Total Driver Fare: <strong>£{{ number_format((float) $invoiceTotal, 2) }}</strong>
</p>

<p>If you have any questions, please contact the admin team.</p>

<p>Regards,<br>Airport Services Admin</p>
