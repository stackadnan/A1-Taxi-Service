<?php
$headTitle = 'Passenger Information';
$img = 'assets/img/bg-header-banner.jpg';
$Title = 'Home';
$Title2 = 'Passenger Information';
$SubTitle = 'Complete Your Booking';

$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (str_contains($host, 'executiveairportcars.com')) {
  $api_url = 'https://admin.executiveairportcars.com/api/quote';
} else {
  $api_url = 'http://localhost/AirportServices/public/api/quote';
}
?>

@include('partials.layouts.layoutsTop')

<section class="contact-section-1 fix section-padding pb-0">
  <div class="container">
    <div class="contact-wrapper-area">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="section-title">
            <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
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
              <img src="assets/img/sub-icon.png" alt="icon-img" class="wow fadeInUp">
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
                        <span>Flight Number</span>
                        <input type="text" name="flight_number" placeholder="Flight #">
                      </div>
                    </div>

                    <div class="col-lg-6">
                      <div class="form-clt">
                        <span>Flight Landing Time</span>
                        <select name="flight_landing_time">
                          <option value="">Select Return Flight Landing Time</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-lg-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="meet_and_greet" value="1" id="meet_and_greet">
                    <label class="form-check-label" for="meet_and_greet">
                      Meet &amp; Greet Service Â£20 Extra
                    </label>
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
  var API_URL = <?php echo json_encode($api_url); ?>;

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

  document.querySelectorAll('select[name="pickup_time"], select[name="return_pickup_time"], select[name="flight_landing_time"]')
    .forEach(function(el) { populateTimeSelect(el); });

  document.querySelectorAll('button[data-payment]').forEach(function(btn) {
    btn.addEventListener('click', function() {
      selectedPaymentType = btn.getAttribute('data-payment') || 'cash';
      document.getElementById('payment_type').value = selectedPaymentType;
    });
  });

  console.log('=== Page 3 â€” Passenger Form ===');
  console.log('Quote Ref:', bdata.quote_ref || '-');
  console.log('Return Ref:', bdata.return_ref || 'N/A');
  console.log('Pickup:', bdata.pickup);
  console.log('Dropoff:', bdata.dropoff);
  console.log('Pickup Date:', bdata.pickup_date);
  console.log('Vehicle:', bdata.vehicle_type);
  console.log('Price: Â£' + (bdata.price ? Number(bdata.price).toFixed(2) : '-'));
  console.log('Trip type:', bdata.trip_type);

  document.getElementById('passenger-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var submitButton = e.submitter || document.activeElement;
    var form = e.currentTarget;
    var status = document.getElementById('booking-submit-status');

    function showStatus(message, type) {
      if (!status) return;
      status.className = 'alert alert-' + (type || 'info') + ' mb-4';
      status.textContent = message;
      status.classList.remove('d-none');
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
      meet_and_greet: document.querySelector('[name="meet_and_greet"]').checked,
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
      formData.flight_number = document.querySelector('[name="flight_number"]').value;
      formData.flight_landing_time = document.querySelector('[name="flight_landing_time"]').value;
    }

    console.log('=== Book Now Clicked ===');
    console.log('Payment type:', selectedPaymentType);
    console.log('Form data:', formData);

    if (submitButton) {
      submitButton.disabled = true;
      submitButton.dataset.originalText = submitButton.textContent;
      submitButton.textContent = 'Submitting...';
    }

    showStatus('Submitting your booking...', 'info');

    fetch(API_URL.replace('/quote', '/booking/save'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(formData)
    })
    .then(function(r) { return r.json().then(function(json) { return { ok: r.ok, json: json }; }); })
    .then(function(result) {
      if (!result.ok || !result.json || !result.json.success) {
        throw new Error((result.json && result.json.message) || 'Could not submit booking.');
      }

      try {
        localStorage.setItem('booking_result', JSON.stringify(result.json));
      } catch (storageError) {}

      form.classList.add('d-none');
      showStatus('Booking submitted successfully. Reference: ' + (result.json.booking_refs && result.json.booking_refs.length ? result.json.booking_refs.join(', ') : 'pending'), 'success');

      if (document.getElementById('booking-summary')) {
        document.getElementById('booking-summary').innerHTML = '<div class="form-clt mb-0"><span>Status</span><input type="text" readonly value="Booking submitted successfully"></div>';
      }
    })
    .catch(function(error) {
      showStatus(error.message || 'Could not submit booking.', 'danger');
      if (submitButton) {
        submitButton.disabled = false;
      }
    })
    .finally(function() {
      if (submitButton) {
        submitButton.textContent = submitButton.dataset.originalText || 'Book Now';
      }
    });
  });

  function renderSummary() {
    var summary = document.getElementById('booking-summary');
    var rows = [];

    rows.push(summaryInput('Quote Ref', bdata.quote_ref || '-'));

    if (bdata.return_ref) {
      rows.push(summaryInput('Return Ref', bdata.return_ref));
    }

    rows.push(summaryInput('From', bdata.pickup || '-'));
    rows.push(summaryInput('To', bdata.dropoff || '-'));
    rows.push(summaryInput('Vehicle', bdata.vehicle_type || '-'));
    rows.push(summaryInput('Price', bdata.price ? ('Â£' + Number(bdata.price).toFixed(2)) : '-'));
    rows.push(summaryInput('Trip Type', bdata.trip_type || '-'));

    summary.innerHTML = rows.join('');
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



