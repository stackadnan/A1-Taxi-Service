<?php
$headTitle = 'Thank You';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Thank You';
$SubTitle = 'Booking Confirmed';

$confirmation = is_array($bookingConfirmation ?? null) ? $bookingConfirmation : [];
$paymentType = strtolower((string) ($confirmation['payment_type'] ?? ''));
$paymentLabel = $paymentType === 'card' ? 'Card' : 'Cash';
?>

@include('partials.layouts.layoutsTop')

<section class="contact-section-1 fix section-padding pb-0">
  <div class="container">
    <div class="contact-wrapper-area">
      <div class="row justify-content-center">
        <div class="col-lg-8">
          <div class="contact-content text-center">
            <div class="section-title">
              <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
              <span class="wow fadeInUp" data-wow-delay=".2s">booking complete</span>
              <h2 class="wow fadeInUp" data-wow-delay=".4s">Thank you for your booking</h2>
            </div>

            <div class="contact-form-items mt-4">
              <div class="form-clt mb-3">
                <span>Booking Reference</span>
                <input type="text" readonly value="{{ $confirmation['booking_code'] ?? '-' }}">
              </div>
              @if(!empty($confirmation['return_booking_code']))
              <div class="form-clt mb-3">
                <span>Return Booking Reference</span>
                <input type="text" readonly value="{{ $confirmation['return_booking_code'] }}">
              </div>
              @endif
              <div class="form-clt mb-3">
                <span>Passenger Name</span>
                <input type="text" readonly value="{{ $confirmation['passenger_name'] ?? '-' }}">
              </div>
              <div class="form-clt mb-3">
                <span>Email</span>
                <input type="text" readonly value="{{ $confirmation['email'] ?? '-' }}">
              </div>
              <div class="form-clt mb-0">
                <span>Payment Type</span>
                <input type="text" readonly value="{{ $paymentLabel }}">
              </div>
              @if(!empty($confirmation['payment_id']))
              <div class="form-clt mb-0 mt-3">
                <span>Payment ID</span>
                <input type="text" readonly value="{{ $confirmation['payment_id'] }}">
              </div>
              @endif
            </div>

            <p class="mt-4 mb-4">
              Your booking has been saved successfully. Keep your booking reference safe for any future updates.
            </p>

            <a href="manage-booking" class="theme-btn">Manage Booking</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@include('partials.layouts.layoutsBottom')
