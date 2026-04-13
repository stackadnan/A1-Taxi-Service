<?php
$headTitle = 'Passenger Information';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Passenger Information';
$SubTitle = 'Complete Your Booking';

$stripePublishableKey = (function () {
  $defaultKey = (string) config('services.stripe.key', '');
  $tables = [
    'executiveairport_database.admin_settings',
    'admin_settings',
  ];

  foreach ($tables as $table) {
    try {
      $row = \Illuminate\Support\Facades\DB::table($table)->first();
      if (!$row) {
        continue;
      }

      $misc = [];
      $rawMisc = $row->misc ?? null;

      if (is_array($rawMisc)) {
        $misc = $rawMisc;
      } elseif (is_string($rawMisc) && trim($rawMisc) !== '') {
        $decoded = json_decode($rawMisc, true);
        if (is_array($decoded)) {
          $misc = $decoded;
        }
      }

      $dbKey = trim((string) ($misc['stripe_public_key'] ?? ''));
      if ($dbKey !== '') {
        return $dbKey;
      }
    } catch (\Throwable $e) {
      continue;
    }
  }

  return $defaultKey;
})();

$configuredVatPercentage = (function () {
  $tables = [
    'executiveairport_database.admin_settings',
    'admin_settings',
  ];

  foreach ($tables as $table) {
    try {
      $row = \Illuminate\Support\Facades\DB::table($table)->first();
      if (!$row) {
        continue;
      }

      $misc = [];
      $rawMisc = $row->misc ?? null;

      if (is_array($rawMisc)) {
        $misc = $rawMisc;
      } elseif (is_string($rawMisc) && trim($rawMisc) !== '') {
        $decoded = json_decode($rawMisc, true);
        if (is_array($decoded)) {
          $misc = $decoded;
        }
      }

      $vat = $misc['vat_percentage'] ?? 0;
      if (is_numeric($vat)) {
        return max(0, min(100, (float) $vat));
      }
    } catch (\Throwable $e) {
      continue;
    }
  }

  return 0.0;
})();

?>

@include('partials.layouts.layoutsTop')

<section class="contact-section-1 fix section-padding pb-0">
  <div class="container">
    <div class="contact-wrapper-area">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="section-title">
            <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
            <span class="wow fadeInUp" data-wow-delay=".2s">booking summary</span>
            <h4 class="wow fadeInUp" data-wow-delay=".4s">Your Journey</h4>
          </div>

          <div id="booking-summary" class="contact-form-items mt-4">
            <div class="form-clt mb-0">
              <span>Status</span>
              <input type="text" readonly value="Loading booking details...">
            </div>
          </div>

          <a href="quote-results" class="theme-btn mt-4">Back To Quotes</a>
        </div>

        <div class="col-lg-8">
          <div class="contact-content">
            <div class="section-title">
              <img src="{{ \App\Support\GalleryPath::path('i/2') }}" alt="icon-img" class="wow fadeInUp">
              <span class="wow fadeInUp" data-wow-delay=".2s">passenger details</span>
              <h2 class="wow fadeInUp" data-wow-delay=".4s">Passenger Information</h2>
            </div>

            <div id="booking-submit-status" class="alert alert-info d-none mb-4" role="alert"></div>

            <form id="passenger-form" class="contact-form-items mt-4">
              <input type="hidden" name="payment_type" id="payment_type" value="cash">

              <div class="row g-4">
                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Passenger Name</span>
                    <input type="text" name="passenger_name" placeholder="Full Name" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Contact Email</span>
                    <input type="email" name="email" placeholder="Email Address" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Mobile Number</span>
                    <input type="tel" name="phone" placeholder="07400 123456" required>
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <div class="form-clt">
                    <span>Passengers</span>
                    <select name="passengers" required>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                      <option value="6">6</option>
                      <option value="7">7</option>
                      <option value="8">8</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-3 col-md-6">
                  <div class="form-clt">
                    <span>Suitcases</span>
                    <select name="suitcases" required>
                      <option value="0">None</option>
                      <option value="1">1</option>
                      <option value="2">2</option>
                      <option value="3">3</option>
                      <option value="4">4</option>
                      <option value="5">5</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Pick-up Date</span>
                    <input type="date" name="pickup_date" id="pickup_date" required>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Pick-up Time</span>
                    <select name="pickup_time" required>
                      <option value="">Select Pickup Time</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Flight Number</span>
                    <input type="text" name="flight_number" placeholder="Flight #">
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Flight Time</span>
                    <select name="flight_time">
                      <option value="">Select Flight Time</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Meet &amp; Greet</span>
                    <select name="meet_and_greet">
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-6">
                  <div class="form-clt">
                    <span>Baby Seat</span>
                    <select name="baby_seat" id="baby_seat_select">
                      <option value="0">No</option>
                      <option value="1">Yes</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-6 d-none" id="baby-seat-age-wrapper">
                  <div class="form-clt">
                    <span>Baby Seat Age</span>
                    <select name="baby_seat_age">
                      <option value="">Select child seat</option>
                      <option value="0-1">0 to 1 Years</option>
                      <option value="1-3">1 to 3 Years</option>
                      <option value="3-5">3 to 5 Years</option>
                      <option value="5-12">5 to 12 Years</option>
                    </select>
                  </div>
                </div>

                <div class="col-lg-12 d-none" id="return-section">
                  <div class="section-title mb-3">
                    <span>return journey</span>
                    <h4>Return Details</h4>
                  </div>

                  <div class="row g-4">
                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Pickup Date</span>
                        <input type="date" name="return_pickup_date" id="return_pickup_date">
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Pickup Time</span>
                        <select name="return_pickup_time">
                          <option value="">Select Return Pickup Time</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Flight Number</span>
                        <input type="text" name="return_flight_number" placeholder="Flight #">
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Flight Time</span>
                        <select name="return_flight_time">
                          <option value="">Select Return Flight Time</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Meet &amp; Greet</span>
                        <select name="return_meet_and_greet">
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Return Baby Seat</span>
                        <select name="return_baby_seat">
                          <option value="0">No</option>
                          <option value="1">Yes</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-clt">
                    <span>Instructions for Driver (optional)</span>
                    <textarea name="message_to_driver" rows="4" placeholder="Write any pickup instructions"></textarea>
                  </div>
                </div>

                <div class="col-lg-12">
                  <p class="mb-0">
                    By clicking Book Now I confirm that I have read and agree to the
                    <a href="#">privacy policy</a> and <a href="#">terms of booking</a>.
                  </p>
                </div>

                <div class="col-lg-12">
                  <div class="d-flex align-items-center gap-3 flex-wrap">
                    <button type="submit" class="theme-btn" data-payment="cash">Book Now On Cash</button>
                    <button type="submit" class="theme-btn bg-color" data-payment="card">Book Now On Card</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  var bdata = {};
  var selectedPaymentType = 'cash';
  var bookingSubmitUrl = <?php echo json_encode(route('booking.submit')); ?>;
  var stripePublishableKey = <?php echo json_encode($stripePublishableKey); ?>;
  var bookingVatPercentage = Number(<?php echo json_encode((float) $configuredVatPercentage); ?>);
  var stripeJsLoader = null;

  try {
    bdata = JSON.parse(localStorage.getItem('booking_data') || '{}');
  } catch (e) {
    bdata = {};
  }

  var pickupDateEl = document.getElementById('pickup_date');
  if (bdata.pickup_date) pickupDateEl.value = bdata.pickup_date;

  renderSummary();

  if (bdata.trip_type === 'return') {
    document.getElementById('return-section').classList.remove('d-none');
    var returnDateEl = document.getElementById('return_pickup_date');
    if (returnDateEl) returnDateEl.required = true;
    var returnTimeEl = document.querySelector('[name="return_pickup_time"]');
    if (returnTimeEl) returnTimeEl.required = true;
    if (bdata.pickup_date) document.getElementById('return_pickup_date').value = bdata.pickup_date;
  }

  function populateTimeSelect(selectEl) {
    for (var h = 0; h < 24; h++) {
      for (var m = 0; m < 60; m += 30) {
        var hh = h.toString().padStart(2, '0');
        var mm = m.toString().padStart(2, '0');
        var opt = document.createElement('option');
        opt.value = hh + ':' + mm;
        opt.textContent = hh + ':' + mm;
        selectEl.appendChild(opt);
      }
    }
  }

  document.querySelectorAll('select[name="pickup_time"], select[name="flight_time"], select[name="return_pickup_time"], select[name="return_flight_time"]')
    .forEach(function(el) { populateTimeSelect(el); });

  (function() {
    var babySeatSelect = document.getElementById('baby_seat_select');
    var babySeatAgeWrapper = document.getElementById('baby-seat-age-wrapper');
    var babySeatAgeSelect = document.querySelector('[name="baby_seat_age"]');

    if (!babySeatSelect || !babySeatAgeWrapper || !babySeatAgeSelect) return;

    function toggleBabySeatAge() {
      var enabled = babySeatSelect.value === '1';
      babySeatAgeWrapper.classList.toggle('d-none', !enabled);
      babySeatAgeSelect.required = enabled;
      if (!enabled) {
        babySeatAgeSelect.value = '';
      }
    }

    babySeatSelect.addEventListener('change', toggleBabySeatAge);
    toggleBabySeatAge();
  })();

  document.querySelectorAll('button[data-payment]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      selectedPaymentType = btn.getAttribute('data-payment') || 'cash';
      document.getElementById('payment_type').value = selectedPaymentType;
    });
  });

  console.log('=== Page 3 — Passenger Form ===');
  console.log('Booking ID:', bdata.quote_ref || '-');
  console.log('Return Booking ID:', bdata.return_ref || 'N/A');
  console.log('Pickup:', bdata.pickup);
  console.log('Dropoff:', bdata.dropoff);
  console.log('Pickup Date:', bdata.pickup_date);
  console.log('Vehicle:', bdata.vehicle_type);
  console.log('Price: £' + (bdata.price ? Number(bdata.price).toFixed(2) : '-'));
  console.log('Trip type:', bdata.trip_type);

  function setStatus(message, type) {
    var status = document.getElementById('booking-submit-status');
    if (!status) return;
    status.className = 'alert alert-' + (type || 'info') + ' mb-4';
    status.textContent = message;
    status.classList.remove('d-none');
  }

  function loadStripeJs() {
    if (window.Stripe) {
      return Promise.resolve(window.Stripe);
    }

    if (stripeJsLoader) {
      return stripeJsLoader;
    }

    stripeJsLoader = new Promise(function(resolve, reject) {
      var script = document.createElement('script');
      script.src = 'https://js.stripe.com/v3/';
      script.async = true;
      script.onload = function() {
        if (window.Stripe) {
          resolve(window.Stripe);
        } else {
          reject(new Error('Stripe.js failed to initialize.'));
        }
      };
      script.onerror = function() {
        reject(new Error('Unable to load Stripe.js.'));
      };
      document.head.appendChild(script);
    });

    return stripeJsLoader;
  }

  function redirectToStripeCheckout(sessionId, fallbackUrl) {
    if (!sessionId || !stripePublishableKey) {
      if (fallbackUrl) {
        window.location.href = fallbackUrl;
        return;
      }

      setStatus('Card payment could not be started. Please try again.', 'danger');
      return;
    }

    setStatus('Redirecting to secure card checkout...', 'info');

    loadStripeJs()
      .then(function(Stripe) {
        var stripe = Stripe(stripePublishableKey);
        return stripe.redirectToCheckout({ sessionId: sessionId });
      })
      .then(function(result) {
        if (result && result.error) {
          if (fallbackUrl) {
            window.location.href = fallbackUrl;
            return;
          }
          setStatus(result.error.message || 'Unable to redirect to card checkout.', 'danger');
        }
      })
      .catch(function(err) {
        if (fallbackUrl) {
          window.location.href = fallbackUrl;
          return;
        }
        setStatus('Unable to open Stripe checkout: ' + err.message, 'danger');
      });
  }

  document.getElementById('passenger-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var submitButton = e.submitter || document.activeElement;
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';
    if (submitButton) {
      submitButton.disabled = true;
      submitButton.dataset.originalText = submitButton.textContent;
      submitButton.textContent = 'Submitting...';
    }

    var formData = {
      quote_ref: bdata.quote_ref,
      return_ref: bdata.return_ref || null,
      pickup: bdata.pickup,
      dropoff: bdata.dropoff,
      pickup_date: document.querySelector('[name="pickup_date"]').value,
      pickup_time: document.querySelector('[name="pickup_time"]').value,
      passenger_name: document.querySelector('[name="passenger_name"]').value,
      email: document.querySelector('[name="email"]').value,
      phone: document.querySelector('[name="phone"]').value,
      passengers: document.querySelector('[name="passengers"]').value,
      suitcases: document.querySelector('[name="suitcases"]').value,
      flight_number: document.querySelector('[name="flight_number"]').value,
      flight_time: document.querySelector('[name="flight_time"]').value,
      meet_and_greet: document.querySelector('[name="meet_and_greet"]').value === '1',
      baby_seat: document.querySelector('[name="baby_seat"]').value === '1',
      baby_seat_age: document.querySelector('[name="baby_seat_age"]').value,
      message_to_driver: document.querySelector('[name="message_to_driver"]').value,
      vehicle_type: bdata.vehicle_type,
      price: bdata.price,
      trip_type: bdata.trip_type,
      payment_type: selectedPaymentType,
      source_url: window.location.href
    };

    if (bdata.trip_type === 'return') {
      formData.return_pickup_date = document.querySelector('[name="return_pickup_date"]').value;
      formData.return_pickup_time = document.querySelector('[name="return_pickup_time"]').value;
      formData.return_flight_number = document.querySelector('[name="return_flight_number"]').value;
      formData.return_flight_time = document.querySelector('[name="return_flight_time"]').value;
      formData.return_meet_and_greet = document.querySelector('[name="return_meet_and_greet"]').value === '1';
      formData.return_baby_seat = document.querySelector('[name="return_baby_seat"]').value === '1';
    }

    console.log('=== Book Now Clicked ===');
    console.log('Payment type:', selectedPaymentType);
    console.log('Form data:', formData);

    setStatus('Submitting your booking...', 'info');

    fetch(bookingSubmitUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify(formData)
    })
    .then(function(response) {
      return response.json().then(function(payload) {
        return {
          ok: response.ok,
          payload: payload
        };
      });
    })
    .then(function(result) {
      if (!result.ok || !result.payload.success) {
        var errorMessage = (result.payload && result.payload.message) ? result.payload.message : 'Could not complete your booking.';
        setStatus(errorMessage, 'danger');
        return;
      }

      try {
        localStorage.setItem('booking_result', JSON.stringify(result.payload));
      } catch (storageError) {}

      var isCardPayment = selectedPaymentType === 'card';
      if (isCardPayment && result.payload.stripe_session_id) {
        redirectToStripeCheckout(result.payload.stripe_session_id, result.payload.redirect_url || '');
        return;
      }

      if (result.payload.redirect_url) {
        window.location.href = result.payload.redirect_url;
        return;
      }

      document.getElementById('passenger-form').classList.add('d-none');
      setStatus(
        'Booking submitted successfully. Reference: ' +
          ((result.payload.booking_refs && result.payload.booking_refs.length) ? result.payload.booking_refs.join(', ') : 'pending'),
        'success'
      );

      var summary = document.getElementById('booking-summary');
      if (summary) {
        summary.innerHTML = '<div class="form-clt mb-0"><span>Status</span><input type="text" readonly value="Booking submitted successfully"></div>';
      }
    })
    .catch(function(err) {
      setStatus('Request failed: ' + err.message, 'danger');
    })
    .finally(function() {
      if (submitButton) {
        submitButton.disabled = false;
        submitButton.textContent = submitButton.dataset.originalText || 'Book Now';
      }
    });
  });

  function renderSummary() {
    var summary = document.getElementById('booking-summary');
    var rows = [];
    var originalPrice = toNumericPrice(bdata.price);
    var vatAmount = originalPrice !== null ? ((originalPrice * bookingVatPercentage) / 100) : null;
    var totalWithVat = originalPrice !== null ? (originalPrice + (vatAmount || 0)) : null;

    rows.push(summaryInput('Booking ID', bdata.quote_ref || '-'));

    if (bdata.return_ref) {
      rows.push(summaryInput('Return Booking ID', bdata.return_ref));
    }

    rows.push(summaryInput('From', bdata.pickup || '-'));
    rows.push(summaryInput('To', bdata.dropoff || '-'));
    rows.push(summaryInput('Vehicle', bdata.vehicle_type || '-'));
    rows.push(summaryInput('Original Price', originalPrice !== null ? formatMoney(originalPrice) : '-'));
    rows.push(summaryInput('VAT (' + bookingVatPercentage.toFixed(2) + '%)', vatAmount !== null ? formatMoney(vatAmount) : '-'));
    rows.push(summaryInput('Total Price (Incl. VAT)', totalWithVat !== null ? formatMoney(totalWithVat) : '-'));
    rows.push(summaryInput('Trip Type', bdata.trip_type || '-'));

    summary.innerHTML = rows.join('');
  }

  function toNumericPrice(value) {
    if (value === null || value === undefined || value === '') {
      return null;
    }

    var num = Number(value);
    return Number.isFinite(num) ? num : null;
  }

  function formatMoney(value) {
    return '£' + Number(value).toFixed(2);
  }

  function summaryInput(label, value) {
    return '<div class="form-clt mb-3">' +
      '<span>' + escapeHtml(label) + '</span>' +
      '<input type="text" readonly value="' + escapeHtml(value) + '">' +
      '</div>';
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }
</script>

@include('partials.layouts.layoutsBottom')



