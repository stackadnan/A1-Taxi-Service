<?php
function e($v){ return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Passenger Information</title>
</head>
<body>

<h2>Passenger Information</h2>
<div id="booking-summary"></div>

<form id="passenger-form">

  <p>
    <label>Passenger Name</label><br>
    <input type="text" name="passenger_name" placeholder="Full Name" style="width:300px">
  </p>

  <p>
    <label>Contact Email</label><br>
    <input type="email" name="email" placeholder="Email Address" style="width:300px">
  </p>

  <p>
    <label>Mobile Number</label><br>
    <input type="tel" name="phone" placeholder="07400 123456" style="width:200px">
  </p>

  <p>
    <label>Passengers</label><br>
    <select name="passengers">
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
      <option value="6">6</option>
      <option value="7">7</option>
      <option value="8">8</option>
    </select>
  </p>

  <p>
    <label>Suitcases</label><br>
    <select name="suitcases">
      <option value="0">None</option>
      <option value="1">1</option>
      <option value="2">2</option>
      <option value="3">3</option>
      <option value="4">4</option>
      <option value="5">5</option>
    </select>
  </p>

  <p>
    <label>Pick-up Date</label><br>
    <input type="date" name="pickup_date" id="pickup_date">
  </p>

  <p>
    <label>Pick-up Time</label><br>
    <select name="pickup_time">
      <option value="">Select Pickup Time</option>
    </select>
  </p>

  <!-- Return journey (shown only when trip_type = return) -->
  <div id="return-section" style="display:none; border:1px solid #ccc; padding:10px; margin-top:10px;">
    <p><strong>Return Journey</strong></p>

    <p>
      <label>Return Pickup Date</label><br>
      <input type="date" name="return_pickup_date" id="return_pickup_date">
    </p>

    <p>
      <label>Return Pickup Time</label><br>
      <select name="return_pickup_time">
        <option value="">Select Return Pickup Time</option>
      </select>
    </p>

    <p>
      <label>Flight Number</label><br>
      <input type="text" name="flight_number" placeholder="Flight #" style="width:200px">
    </p>

    <p>
      <label>Flight Landing Time</label><br>
      <select name="flight_landing_time">
        <option value="">Select Return Flight Landing Time</option>
      </select>
    </p>
  </div>

  <p>
    <label>
      <input type="checkbox" name="meet_and_greet" value="1">
      Meet &amp; Greet Service £20 Extra
    </label>
  </p>

  <p>
    <label>Instructions for Driver (optional)</label><br>
    <textarea name="message_to_driver" rows="3" style="width:400px"></textarea>
  </p>

  <p style="font-size:12px">
    By clicking Book Now I confirm that I have read and agree to the
    <a href="#">privacy policy</a> &amp; <a href="#">terms of booking</a>.
  </p>

  <p>
    <button type="submit" name="payment_type" value="cash">Book Now on Cash</button>
    &nbsp;
    <button type="submit" name="payment_type" value="card">Book Now on Card</button>
  </p>

</form>

<br>
<a href="page2.php">Back</a>

<script>
  var bdata = {};
  try { bdata = JSON.parse(localStorage.getItem('booking_data') || '{}'); } catch(e) {}

  // Prefill pickup date from previous page
  var pickupDateEl = document.getElementById('pickup_date');
  if (bdata.pickup_date) pickupDateEl.value = bdata.pickup_date;

  // Show summary
  var summary = document.getElementById('booking-summary');
  summary.innerHTML =
    '<p><strong>Quote Ref:</strong> ' + (bdata.quote_ref || '-') + '</p>' +
    (bdata.return_ref ? '<p><strong>Return Ref:</strong> ' + bdata.return_ref + '</p>' : '') +
    '<p><strong>From:</strong> ' + (bdata.pickup || '-') + '</p>' +
    '<p><strong>To:</strong> ' + (bdata.dropoff || '-') + '</p>' +
    '<p><strong>Vehicle:</strong> ' + (bdata.vehicle_type || '-') + '</p>' +
    '<p><strong>Price:</strong> £' + (bdata.price ? Number(bdata.price).toFixed(2) : '-') + '</p>' +
    '<p><strong>Trip type:</strong> ' + (bdata.trip_type || '-') + '</p>';

  // Show return section if return journey
  if (bdata.trip_type === 'return') {
    document.getElementById('return-section').style.display = 'block';
    if (bdata.pickup_date) document.getElementById('return_pickup_date').value = bdata.pickup_date;
  }

  // Populate time dropdowns with 30-min slots (00:00 – 23:30)
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
    .forEach(function(el){ populateTimeSelect(el); });

  // Console log on load (same pattern as page1)
  console.log('=== Page 3 — Passenger Form ===');
  console.log('Quote Ref:', bdata.quote_ref || '-');
  console.log('Return Ref:', bdata.return_ref || 'N/A');
  console.log('Pickup:', bdata.pickup);
  console.log('Dropoff:', bdata.dropoff);
  console.log('Pickup Date:', bdata.pickup_date);
  console.log('Vehicle:', bdata.vehicle_type);
  console.log('Price: £' + (bdata.price ? Number(bdata.price).toFixed(2) : '-'));
  console.log('Trip type:', bdata.trip_type);

  // Form submit handler
  document.getElementById('passenger-form').addEventListener('submit', function(e) {
    e.preventDefault();
    var paymentType = document.activeElement && document.activeElement.value ? document.activeElement.value : 'cash';

    var formData = {
      quote_ref:           bdata.quote_ref,
      return_ref:          bdata.return_ref || null,
      pickup:              bdata.pickup,
      dropoff:             bdata.dropoff,
      pickup_date:         document.querySelector('[name="pickup_date"]').value,
      pickup_time:         document.querySelector('[name="pickup_time"]').value,
      passenger_name:      document.querySelector('[name="passenger_name"]').value,
      email:               document.querySelector('[name="email"]').value,
      phone:               document.querySelector('[name="phone"]').value,
      passengers:          document.querySelector('[name="passengers"]').value,
      suitcases:           document.querySelector('[name="suitcases"]').value,
      meet_and_greet:      document.querySelector('[name="meet_and_greet"]').checked,
      message_to_driver:   document.querySelector('[name="message_to_driver"]').value,
      vehicle_type:        bdata.vehicle_type,
      price:               bdata.price,
      trip_type:           bdata.trip_type,
      payment_type:        paymentType,
    };

    if (bdata.trip_type === 'return') {
      formData.return_pickup_date  = document.querySelector('[name="return_pickup_date"]').value;
      formData.return_pickup_time  = document.querySelector('[name="return_pickup_time"]').value;
      formData.flight_number       = document.querySelector('[name="flight_number"]').value;
      formData.flight_landing_time = document.querySelector('[name="flight_landing_time"]').value;
    }

    console.log('=== Book Now Clicked ===');
    console.log('Payment type:', paymentType);
    console.log('Form data:', formData);

    // TODO: submit formData to booking endpoint
    alert('Book Now (' + paymentType + ') – form data logged to console. Submit endpoint TBD.');
  });
</script>

</body>
</html>
