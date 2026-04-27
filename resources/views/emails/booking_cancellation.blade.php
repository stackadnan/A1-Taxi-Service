<p>Dear {{ $booking->passenger_name ?: 'Customer' }},</p>

<p>Your booking has been cancelled.</p>

<p>
Booking Reference: <strong>{{ $booking->booking_code ?? ('#' . $booking->id) }}</strong><br>
Pickup: <strong>{{ $booking->pickup_address ?: '-' }}</strong><br>
Dropoff: <strong>{{ $booking->dropoff_address ?: '-' }}</strong><br>
Pickup Date: <strong>{{ optional($booking->pickup_date)->format('Y-m-d') ?: '-' }}</strong><br>
Pickup Time: <strong>{{ $booking->pickup_time ?: '-' }}</strong>
</p>

<p>If you have any questions, please contact support.</p>

<p>Regards,<br>Airport Services</p>
