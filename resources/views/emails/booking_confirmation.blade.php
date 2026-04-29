<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Confirmation</title>
<style>
body, table, td { margin: 0; padding: 0; }
img { border: 0; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
a { text-decoration: none; }
</style>
</head>
<body style="margin:0;padding:0;background:#eef2f6;font-family:Arial,sans-serif;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" bgcolor="#eef2f6">
  <tr>
    <td align="center" style="padding:20px;">
      <table width="520" cellpadding="0" cellspacing="0" border="0" style="background:#ffffff;border-radius:24px;overflow:hidden;max-width:520px;">
        <tr>
          <td style="background:#008B9E;padding:24px;text-align:center;color:#ffffff;">
            <img src="https://executiveairportcars.com/design/assets/img/logo/white-logo-2.png" width="130" alt="A1 Airport Cars" style="display:block;margin:0 auto 14px auto;" />
            <div style="display:inline-block;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.25);border-radius:30px;padding:8px 18px;font-size:13px;font-weight:700;">Booking Confirmed</div>
          </td>
        </tr>
        <tr>
          <td style="padding:24px;">
            <p style="font-size:15px;font-weight:700;color:#192335;margin:0 0 16px 0;">Dear {{ $booking->passenger_name ?: 'Customer' }},</p>
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 18px 0;">Your booking is confirmed. Below is your booking reference and journey information.</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
              <tr>
                <td style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:12px;font-size:14px;color:#374151;">
                  <strong style="font-size:10px;color:#008B9E;letter-spacing:1px;display:block;margin-bottom:6px;">BOOKING REFERENCE</strong>
                  <span style="font-size:18px;font-weight:800;color:#192335;">{{ $booking->booking_code ?: 'Not provided' }}</span>
                </td>
              </tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;font-size:14px;color:#374151;">
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Passenger</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->passenger_name ?: 'Not provided' }}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Phone</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->phone ?: 'Not provided' }}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Payment method</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->payment_type ?: 'Not provided' }}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Payment status</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">
                @php
                  $paymentType = strtolower(trim((string) ($booking->payment_type ?? '')));
                  $paymentId = trim((string) ($booking->payment_id ?? ''));
                @endphp
                @if($paymentType === 'card')
                  @if($paymentId !== '')
                    Payment completed
                  @else
                    Payment pending
                  @endif
                @else
                  {{ $booking->payment_type ?: 'Not provided' }}
                @endif
              </td></tr>
              <tr><td style="padding:8px 0;">Vehicle</td><td style="padding:8px 0;" align="right">{{ $booking->vehicle_type ?: 'Not provided' }}</td></tr>
            </table>
            @if(!empty($paymentUrl) && strtolower(trim((string) ($booking->payment_type ?? ''))) === 'card')
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;">
              <tr>
                <td align="center">
                  <a href="{{ $paymentUrl }}" style="display:inline-block;padding:12px 24px;background:#008B9E;color:#ffffff;border-radius:999px;font-size:14px;font-weight:700;">Pay Now</a>
                </td>
              </tr>
            </table>
            @endif
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;font-size:14px;color:#374151;">
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Pickup</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->pickup_address ?: 'Not provided' }}</td></tr>
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Dropoff</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->dropoff_address ?: 'Not provided' }}</td></tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:18px;font-size:14px;color:#374151;">
              <tr><td style="padding:8px 0;border-bottom:1px solid #eef2e6;">Pickup Date</td><td style="padding:8px 0;border-bottom:1px solid #eef2e6;" align="right">{{ $booking->pickup_date ?: 'Not provided' }}</td></tr>
              <tr><td style="padding:8px 0;">Pickup Time</td><td style="padding:8px 0;" align="right">{{ $booking->pickup_time ?: 'Not provided' }}</td></tr>
            </table>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;padding:14px;margin-bottom:18px;font-size:14px;color:#374151;">
              <tr><td style="padding:6px 0;">Fare</td><td style="padding:6px 0;" align="right">{{ $booking->fare !== null ? '£' . number_format((float)$booking->fare,2) : 'Not provided' }}</td></tr>
              <tr><td style="padding:6px 0;">VAT</td><td style="padding:6px 0;" align="right">{{ $booking->vat !== null ? '£' . number_format((float)$booking->vat,2) : 'Not provided' }}</td></tr>
              <tr><td style="padding:12px 0 0 0;border-top:1px solid #e5e7eb;font-weight:700;">Total Fare</td><td style="padding:12px 0 0 0;border-top:1px solid #e5e7eb;font-weight:700;" align="right">{{ $booking->total_fare !== null ? '£' . number_format((float)$booking->total_fare,2) : 'Not provided' }}</td></tr>
            </table>
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 24px 0;">Thank you for booking with us. If you need any assistance, please contact our team.</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0">
              <tr>
                <td style="font-size:13px;font-weight:700;color:#192335;">Thank you.</td>
                <td align="right"><a href="{{ $booking->company_website ?: '#' }}/contact" style="font-size:12px;font-weight:700;color:#008B9E;">Contact us</a></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="background:#192335;padding:16px;text-align:center;">
            <p style="font-size:10px;color:rgba(255,255,255,0.7);margin:0;line-height:1.5;"><a href="{{ $booking->company_website ?: '#' }}/privacy" style="color:rgba(255,255,255,0.7);">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $booking->company_website ?: '#' }}/terms" style="color:rgba(255,255,255,0.7);">Refund Policy</a> &nbsp;|&nbsp; <a href="{{ $booking->company_website ?: '#' }}/terms" style="color:rgba(255,255,255,0.7);">Terms &amp; Conditions</a></p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
