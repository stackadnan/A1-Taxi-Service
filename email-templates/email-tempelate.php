<?php
$bookingData = [
    'quote_ref' => 'RB-' . strtoupper(substr(uniqid(), -6)),
    'company_name' => 'Quick Airport Transfers',
    'company_email' => 'bookings@quickairporttransfers.com',
    'company_phone' => '+44 (0) 123 456 789',
    'company_website' => 'https://quickairporttransfers.com',
    'logo_url' => 'assets/img/logo/white-logo-2.png',
    'theme_color' => '#008B9E',
    'theme_dark' => '#192335',
    'pickup' => 'Park Plaza London Riverbank, Albert Embankment, London, UK',
    'dropoff' => 'Southampton Port, Cunard Road, Southampton, UK',
    'pickup_date' => '21 July 2026',
    'pickup_time' => '10:00',
    'flight_number' => 'BA1234',
    'flight_landing_time' => '08:30',
    'passenger_name' => 'Anna Kishelov',
    'email' => 'annakish@gmail.com',
    'phone' => '+44 2487709020',
    'passengers' => '4',
    'suitcases' => '8',
    'vehicle_type' => 'MPV8',
    'child_seat' => 0,
    'meet_and_greet' => 0,
    'price' => '307.00',
    'payment_type' => 'cash',
    'payment_id' => '',
    'payment_link' => 'https://quickairporttransfers.com/pay/'.uniqid(),
    'message_to_driver' => 'Please wait at the arrival hall. Driver will hold a sign with your name.',
    'has_return' => 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, initial-scale=1.0">
<title>Booking Confirmed</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{background:#eef2f6;padding:10px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;}
.container{max-width:520px;margin:0 auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.05);}
.header{background:linear-gradient(135deg,#008B9E 0%,#192335 100%);padding:16px 20px;text-align:center;}
.logo{max-width:130px;height:auto;margin-bottom:5px;}
.header h1{color:#fff;font-size:18px;font-weight:700;margin:3px 0 2px;}
.content{padding:16px;}
.ref-box{background:#f8fafc;border-radius:14px;padding:8px 12px;text-align:center;margin-bottom:12px;border:1px solid #e2e8f0;}
.ref-label{font-size:8px;font-weight:700;color:#008B9E;letter-spacing:1px;}
.ref-number{font-size:14px;font-weight:800;color:#192335;}
.price-row{background:#008B9E08;border-radius:14px;padding:10px 14px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center;border:1px solid #008B9E20;}
.price-left .price-label{font-size:8px;font-weight:700;color:#008B9E;}
.price-left .price-amount{font-size:22px;font-weight:800;color:#008B9E;line-height:1;}
.price-right{display:flex;flex-direction:column;align-items:flex-end;gap:6px;}
.cash-badge{background:#192335;color:#fff;padding:3px 12px;border-radius:30px;font-size:10px;font-weight:600;}
.pay-btn{background:#008B9E;color:#fff;padding:6px 16px;border-radius:30px;font-size:11px;font-weight:700;text-decoration:none;display:inline-block;text-align:center;}
.pay-btn i{font-size:10px;margin-right:4px;}
.section{background:#f8fafc;border-radius:14px;padding:10px 12px;margin-bottom:10px;border:1px solid #e2e8f0;}
.section-title{display:flex;align-items:center;gap:6px;margin-bottom:8px;padding-bottom:5px;border-bottom:1px solid #e2e8f0;}
.section-title i{font-size:12px;color:#008B9E;}
.section-title h3{font-size:11px;font-weight:700;color:#192335;}
.location-row{display:flex;gap:8px;margin-bottom:8px;}
.location-icon{width:26px;height:26px;background:#fff;border-radius:8px;display:flex;align-items:center;justify-content:center;}
.location-icon i{font-size:11px;color:#008B9E;}
.location-text{flex:1;}
.location-label{font-size:7px;font-weight:700;color:#8a99b4;}
.location-addr{font-size:10px;font-weight:600;color:#192335;line-height:1.3;}
.route-line{margin:3px 0 3px 13px;width:2px;height:12px;background:#008B9E;}
.info-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:10px;}
.info-card{background:#f8fafc;border-radius:12px;padding:8px 10px;border:1px solid #e2e8f0;}
.info-card .label{font-size:7px;font-weight:700;color:#8a99b4;display:flex;align-items:center;gap:3px;margin-bottom:2px;}
.info-card .label i{font-size:8px;color:#008B9E;}
.info-card .value{font-size:11px;font-weight:700;color:#192335;}
.detail-row{display:flex;justify-content:space-between;align-items:center;padding:5px 0;border-bottom:1px solid #eef2f6;}
.detail-row:last-child{border-bottom:none;}
.detail-label{font-size:10px;color:#64748b;display:flex;align-items:center;gap:5px;}
.detail-label i{font-size:9px;color:#008B9E;width:14px;}
.detail-value{font-size:11px;font-weight:700;color:#192335;word-break:break-word;}
.flight-strip{background:#f1f5f9;border-radius:12px;padding:8px;margin:10px 0;display:flex;justify-content:space-between;}
.flight-strip div{flex:1;text-align:center;}
.flight-strip .label{font-size:7px;font-weight:700;color:#8a99b4;}
.flight-strip .label i{font-size:7px;}
.flight-strip .value{font-size:10px;font-weight:600;color:#192335;}
.note-box{background:#fefce8;border-left:3px solid #008B9E;border-radius:10px;padding:10px 12px;margin:10px 0;}
.note-box p{font-size:10px;color:#854d0e;margin:0;line-height:1.4;}
.note-box i{font-size:10px;margin-right:4px;color:#008B9E;}
.note-box strong{font-size:10px;}
.btn-group{display:flex;gap:8px;margin:12px 0;}
.btn{flex:1;text-align:center;padding:8px;border-radius:30px;text-decoration:none;font-size:11px;font-weight:700;}
.btn-primary{background:#008B9E;color:#fff;}
.btn-outline{background:transparent;border:1px solid #008B9E;color:#008B9E;}
.contact-row{display:flex;justify-content:center;gap:20px;padding:10px 0;border-top:1px solid #e2e8f0;border-bottom:1px solid #e2e8f0;margin:10px 0;}
.contact-item{text-align:center;text-decoration:none;}
.contact-icon{width:38px;height:38px;background:#008B9E10;border-radius:50%;display:flex;align-items:center;justify-content:center;margin-bottom:3px;}
.contact-icon i{font-size:16px;color:#008B9E;}
.contact-text{font-size:9px;font-weight:600;color:#192335;}
.social-wrap{text-align:center;padding:8px 0 4px;}
.social-wrap p{font-size:9px;font-weight:700;color:#8a99b4;margin-bottom:6px;letter-spacing:1px;}
.social-icons{display:flex;justify-content:center;gap:12px;}
.social-icons a{width:30px;height:30px;background:#f1f5f9;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;}
.social-icons a i{font-size:13px;color:#008B9E;}
.footer{background:#192335;padding:10px 16px;text-align:center;}
.footer p{color:rgba(255,255,255,0.5);font-size:8px;margin:2px 0;}
.footer a{color:#fff;text-decoration:none;}
@media(max-width:480px){
body{padding:8px;}
.content{padding:12px;}
}
</style>
</head>
<body>
<div class="container">
<div class="header">
<img src="<?php echo $bookingData['logo_url']; ?>" alt="Logo" class="logo">
<h1>✓ Booking Confirmed</h1>
</div>
<div class="content">
<div class="ref-box">
<div class="ref-label">BOOKING REFERENCE</div>
<div class="ref-number"><?php echo $bookingData['quote_ref']; ?></div>
</div>

<div class="price-row">
<div class="price-left">
<div class="price-label">TOTAL FARE</div>
<div class="price-amount">£<?php echo number_format($bookingData['price'], 2); ?></div>
</div>
<div class="price-right">
<span class="cash-badge"><i class="fas fa-money-bill-wave"></i> <?php echo strtoupper($bookingData['payment_type']); ?></span>
<?php if($bookingData['payment_type'] == 'cash'): ?>
<a href="<?php echo $bookingData['payment_link']; ?>" class="pay-btn"><i class="fas fa-credit-card"></i> Pay Now</a>
<?php endif; ?>
</div>
</div>

<div class="section">
<div class="section-title"><i class="fas fa-location-dot"></i><h3>JOURNEY</h3></div>
<div class="location-row"><div class="location-icon"><i class="fas fa-circle-dot"></i></div><div class="location-text"><div class="location-label">PICKUP</div><div class="location-addr"><?php echo nl2br($bookingData['pickup']); ?></div></div></div>
<div class="route-line"></div>
<div class="location-row"><div class="location-icon"><i class="fas fa-flag-checkered"></i></div><div class="location-text"><div class="location-label">DROPOFF</div><div class="location-addr"><?php echo nl2br($bookingData['dropoff']); ?></div></div></div>
</div>

<div class="info-grid">
<div class="info-card"><div class="label"><i class="fas fa-calendar"></i> DATE</div><div class="value"><?php echo $bookingData['pickup_date']; ?></div></div>
<div class="info-card"><div class="label"><i class="fas fa-clock"></i> TIME</div><div class="value"><?php echo $bookingData['pickup_time']; ?></div></div>
</div>

<div class="section">
<div class="section-title"><i class="fas fa-user-circle"></i><h3>PASSENGER DETAILS</h3></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-user"></i> Name</div><div class="detail-value"><?php echo $bookingData['passenger_name']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-envelope"></i> Email</div><div class="detail-value" style="font-size:10px;"><?php echo $bookingData['email']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-phone"></i> Phone</div><div class="detail-value"><?php echo $bookingData['phone']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-users"></i> Passengers</div><div class="detail-value"><?php echo $bookingData['passengers']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-suitcase"></i> Luggage</div><div class="detail-value"><?php echo $bookingData['suitcases']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-car"></i> Vehicle</div><div class="detail-value"><?php echo $bookingData['vehicle_type']; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-baby-carriage"></i> Baby Seat</div><div class="detail-value"><?php echo $bookingData['child_seat'] ? 'Yes' : 'No'; ?></div></div>
<div class="detail-row"><div class="detail-label"><i class="fas fa-handshake"></i> Meet & Greet</div><div class="detail-value"><?php echo $bookingData['meet_and_greet'] ? 'Yes (+£20)' : 'No'; ?></div></div>
</div>

<div class="flight-strip">
<div><div class="label"><i class="fas fa-plane"></i> FLIGHT NO</div><div class="value"><?php echo $bookingData['flight_number'] ?: '—'; ?></div></div>
<div><div class="label"><i class="fas fa-hourglass-half"></i> FLIGHT TIME</div><div class="value"><?php echo $bookingData['flight_landing_time'] ?: '—'; ?></div></div>
</div>

<?php if($bookingData['message_to_driver']): ?>
<div class="note-box">
<p><i class="fas fa-pen"></i> <strong>Note to Driver:</strong><br><?php echo nl2br($bookingData['message_to_driver']); ?></p>
</div>
<?php endif; ?>

<div class="btn-group">
<a href="<?php echo $bookingData['company_website']; ?>/manage?ref=<?php echo urlencode($bookingData['quote_ref']); ?>" class="btn btn-primary"><i class="fas fa-calendar-check"></i> Manage Booking</a>
<a href="https://maps.google.com/dir/<?php echo urlencode($bookingData['pickup']); ?>/<?php echo urlencode($bookingData['dropoff']); ?>" class="btn btn-outline"><i class="fas fa-map-marked-alt"></i> View Route</a>
</div>

<div class="contact-row">
<a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $bookingData['company_phone']); ?>" class="contact-item"><div class="contact-icon"><i class="fab fa-whatsapp"></i></div><div class="contact-text">WhatsApp</div></a>
<a href="tel:<?php echo $bookingData['company_phone']; ?>" class="contact-item"><div class="contact-icon"><i class="fas fa-phone-alt"></i></div><div class="contact-text">Call Us</div></a>
<a href="mailto:<?php echo $bookingData['company_email']; ?>" class="contact-item"><div class="contact-icon"><i class="fas fa-envelope"></i></div><div class="contact-text">Email</div></a>
</div>

<div class="social-wrap">
<p>CONNECT WITH US</p>
<div class="social-icons">
<a href="#"><i class="fab fa-facebook-f"></i></a>
<a href="#"><i class="fab fa-twitter"></i></a>
<a href="#"><i class="fab fa-instagram"></i></a>
<a href="#"><i class="fab fa-linkedin-in"></i></a>
</div>
</div>
</div>

<div class="footer">
<p>© <?php echo date('Y'); ?> <?php echo $bookingData['company_name']; ?>. All rights reserved.</p>
<p><a href="<?php echo $bookingData['company_website']; ?>/privacy">Privacy Policy</a> | <a href="<?php echo $bookingData['company_website']; ?>/terms">Terms of Service</a></p>
</div>
</div>
</body>
</html>