<p>Dear {{ $booking->passenger_name ?: 'Customer' }},</p>

<p>Your booking has been completed.</p>

<p>
Booking Reference: <strong>{{ $booking->booking_code ?? ('#' . $booking->id) }}</strong><br>
Pickup: <strong>{{ $booking->pickup_address ?: '-' }}</strong><br>
Dropoff: <strong>{{ $booking->dropoff_address ?: '-' }}</strong><br>
Pickup Date: <strong>{{ optional($booking->pickup_date)->format('Y-m-d') ?: '-' }}</strong><br>
Pickup Time: <strong>{{ $booking->pickup_time ?: '-' }}</strong><br>
Price: <strong>{{ $booking->total_price !== null ? '£' . number_format((float)$booking->total_price, 2) : '-' }}</strong>
</p>

<p>Thank you for choosing Airport Services. We hope to serve you again soon.</p>

<p>Regards,<br>A1 Airport Transfers</p>
