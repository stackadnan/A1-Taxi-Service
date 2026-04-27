<p>Dear {{ $booking->passenger_name ?: 'Customer' }},</p>

<p>Your assigned driver details are below:</p>

<p>
Driver Name: <strong>{{ $driver->name ?: '-' }}</strong><br>
Driver Phone: <strong>{{ $driver->phone ?: '-' }}</strong><br>
Vehicle: <strong>{{ trim(($driver->vehicle_make ?: '') . ' ' . ($driver->vehicle_model ?: '')) ?: '-' }}</strong><br>
Plate Number: <strong>{{ $driver->vehicle_plate ?: '-' }}</strong><br>
</p>

<p>
Booking Reference: <strong>{{ $booking->booking_code ?? ('#' . $booking->id) }}</strong><br>
Pickup: <strong>{{ $booking->pickup_address ?: '-' }}</strong><br>
Dropoff: <strong>{{ $booking->dropoff_address ?: '-' }}</strong><br>
Pickup Date: <strong>{{ optional($booking->pickup_date)->format('Y-m-d') ?: '-' }}</strong><br>
Pickup Time: <strong>{{ $booking->pickup_time ?: '-' }}</strong>
</p>

<p>Thank you for choosing us.</p>

<p>Regards,<br>A1 Airport Transfers</p>
