<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Booking Cancelled</title>
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
            <div style="display:inline-block;background:rgba(255,255,255,0.18);border:1px solid rgba(255,255,255,0.25);border-radius:30px;padding:8px 18px;font-size:13px;font-weight:700;">Booking Cancelled</div>
          </td>
        </tr>
        <tr>
          <td style="padding:24px;">
            <p style="font-size:15px;font-weight:700;color:#192335;margin:0 0 16px 0;">Dear {{ $booking->passenger_name ?: 'Customer' }},</p>
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 18px 0;">Your booking reference <span style="background:#008B9E;color:#ffffff;padding:4px 8px;border-radius:6px;font-weight:700;">{{ $booking->booking_code ?? ('#' . $booking->id) }}</span> is cancelled. If you have already paid for the booking you will receive a refund according to our refund policy.</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-top:1px solid #e2e8f0;margin:0 0 18px 0;"></table>
            <p style="font-size:14px;color:#374151;line-height:1.7;margin:0 0 24px 0;">If you have any further queries please do not hesitate to contact us.</p>
            <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
              <tr>
                <td style="font-size:13px;font-weight:700;color:#192335;">Thank you.</td>
                <td align="right"><a href="{{ $booking->company_website ?: '#' }}/contact" style="font-size:12px;font-weight:700;color:#008B9E;">Contact us</a></td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td style="background:#192335;padding:16px;text-align:center;">
            <p style="font-size:10px;color:rgba(255,255,255,0.7);margin:0;line-height:1.5;"><a href="{{ $booking->company_website ?: '#' }}/privacy" style="color:rgba(255,255,255,0.7);">Privacy Policy</a> &nbsp;|&nbsp; <a href="{{ $booking->company_website ?: '#' }}/terms" style="color:rgba(255,255,255,0.7);">Cancellation Policy</a> &nbsp;|&nbsp; <a href="{{ $booking->company_website ?: '#' }}/terms" style="color:rgba(255,255,255,0.7);">Terms &amp; Conditions</a></p>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
</body>
</html>
