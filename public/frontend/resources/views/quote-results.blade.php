<?php
// Auto-detect API URL based on current host.
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
if (str_contains($host, 'executiveairportcars.com')) {
  $api_url = 'https://admin.executiveairportcars.com/api/quote';
} else {
  $api_url = 'http://localhost/AirportServices/public/api/quote';
}

$headTitle = 'Quote Results';
$img = 'assets/img/bg-header-banner.jpg';
$Title = 'Home';
$Title2 = 'Quote Results';
$SubTitle = 'Choose Your Vehicle';
?>

@include('partials.layouts.layoutsTop')

<section class="about-section fix section-padding quote-results-page">
  <div class="container">
    <div class="contact-wrapper-area">
      <div class="row g-4">
        <div class="col-lg-4">
          <div class="section-title mb-4">
            <span>Your Journey</span>
            <h4>Trip Summary</h4>
          </div>
          <div id="route-summary" class="contact-form-items">
            <div class="form-clt mb-0">
              <span>Loading</span>
              <input type="text" readonly value="Loading route details...">
            </div>
          </div>
          <a href="./" class="theme-btn mt-4">Modify Journey</a>
        </div>

        <div class="col-lg-8">
          <div class="section-title mb-4">
            <span>Instant Results</span>
            <h2>Available Vehicle Quotes</h2>
          </div>

          <div id="quote-status" class="alert alert-info mb-4">
            <i class="fa-solid fa-spinner fa-spin me-2"></i> Fetching prices...
          </div>

          <div id="results"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<script>
  var API_URL = @json($api_url);

  var VEHICLES = [
    {
      key: 'saloon',
      label: 'Saloon',
      tag: 'Affordable',
      image: 'assets/img/car/saloon.png',
      desc: 'Toyota Prius, Ford Mondeo, VW Passat or similar.',
      seats: 4,
      suitcases: 2,
      cabin: 2
    },
    {
      key: 'business',
      label: 'Business Class',
      tag: 'Luxury',
      image: 'assets/img/car/executive.png',
      desc: 'Mercedes E Class, BMW 5 Series or similar.',
      seats: 4,
      suitcases: 2,
      cabin: 2
    },
    {
      key: 'mpv6',
      label: 'MPV6',
      tag: 'Family',
      image: 'assets/img/car/mpv6.png',
      desc: 'VW Sharan, Seat Alhambra, Ford Galaxy or similar.',
      seats: 6,
      suitcases: 3,
      cabin: 2
    },
    {
      key: 'mpv8',
      label: 'MPV8',
      tag: 'Group',
      image: 'assets/img/car/mpv8.png',
      desc: 'Mercedes V Class, VW Transporter or similar.',
      seats: 8,
      suitcases: 8,
      cabin: 8
    }
  ];

  var quoteData = {};
  try {
    quoteData = JSON.parse(localStorage.getItem('quote_data') || '{}');
  } catch (err) {
    quoteData = {};
  }

  renderRouteSummary(quoteData);

  if (!quoteData.pickup_lat || !quoteData.dropoff_lat) {
    showStatus('No quote data found. Please start from the home page and submit the journey form again.', 'error');
    document.getElementById('results').innerHTML = '<div class="alert alert-warning mb-0">No journey details available to price.</div>';
  } else {
    fetchQuoteResults();
  }

  document.getElementById('results').addEventListener('click', function(evt) {
    var button = evt.target.closest('button[data-book]');
    if (!button || button.disabled) return;

    var vehicle = button.getAttribute('data-vehicle') || '';
    var tripType = button.getAttribute('data-trip') || 'one-way';
    var price = Number(button.getAttribute('data-price') || 0);

    handleBook(vehicle, price, tripType);
  });

  function fetchQuoteResults() {
    fetch(API_URL, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        pickup_lat: quoteData.pickup_lat,
        pickup_lon: quoteData.pickup_lon,
        dropoff_lat: quoteData.dropoff_lat,
        dropoff_lon: quoteData.dropoff_lon,
        pickup_postcode: quoteData.pickup_postcode || '',
        dropoff_postcode: quoteData.dropoff_postcode || '',
        pickup_address: quoteData.pickup,
        dropoff_address: quoteData.dropoff,
        date: quoteData.date,
        distance_miles: quoteData.distance_miles || null,
        source_url: quoteData.source_url
      })
    })
    .then(function(r) { return r.json(); })
    .then(function(resp) {
      if (!resp || !resp.success) {
        showStatus('Error: ' + ((resp && resp.message) || 'Could not calculate quote.'), 'error');
        document.getElementById('results').innerHTML = '<div class="alert alert-warning mb-0">No pricing returned for this route.</div>';
        return;
      }

      showStatus('Quotes generated successfully. Select One-Way or Return to continue.', 'success');
      renderVehicleCards(resp);
    })
    .catch(function(err) {
      showStatus('Request failed: ' + err.message, 'error');
      document.getElementById('results').innerHTML = '<div class="alert alert-warning mb-0">Unable to fetch prices right now. Please try again.</div>';
    });
  }

  function renderVehicleCards(resp) {
    var html = '<div class="row g-4">';

    VEHICLES.forEach(function(vehicle) {
      var rawPrice = resp.pricing ? resp.pricing[vehicle.key + '_price'] : resp[vehicle.key + '_price'];
      var oneWayNumeric = toNumberOrNull(rawPrice);
      var returnNumeric = oneWayNumeric === null ? null : oneWayNumeric * 2;
      var oneWayText = formatPrice(oneWayNumeric);

      html += '<div class="col-md-6">';
      html += '  <div class="car-rentals-items mt-0">';
      html += '      <div class="car-image"><img src="' + vehicle.image + '" alt="' + escapeHtml(vehicle.label) + '"></div>';
      html += '      <div class="car-content">';
      html += '          <div class="post-cat">' + escapeHtml(vehicle.tag) + '</div>';
      html += '          <h4>' + escapeHtml(vehicle.label) + '</h4>';
      html += '          <h6>' + escapeHtml(vehicle.desc) + '</h6>';
      html += '          <div class="icon-items">';
      html += '              <ul>';
      html += '                  <li><i class="fa-solid fa-users"></i> Seats: ' + vehicle.seats + '</li>';
      html += '                  <li><i class="fa-solid fa-suitcase"></i> Suitcases: ' + vehicle.suitcases + '</li>';
      html += '                  <li><i class="fa-solid fa-suitcase-rolling"></i> Cabin: ' + vehicle.cabin + '</li>';
      html += '              </ul>';
      html += '          </div>';
      html += '          <h4 class="mb-3">' + oneWayText + '</h4>';
      html += '          <div class="row g-2 quote-results-actions">';
      html += '              <div class="col-sm-6">';
      html += '                  <button type="button" class="theme-btn quote-results-btn w-100" data-book="1" data-vehicle="' + escapeHtml(vehicle.label) + '" data-trip="one-way" data-price="' + (oneWayNumeric === null ? '' : oneWayNumeric.toFixed(2)) + '" ' + (oneWayNumeric === null ? 'disabled' : '') + '>One-Way</button>';
      html += '              </div>';
      html += '              <div class="col-sm-6">';
      html += '                  <button type="button" class="theme-btn bg-color quote-results-btn w-100" data-book="1" data-vehicle="' + escapeHtml(vehicle.label) + '" data-trip="return" data-price="' + (returnNumeric === null ? '' : returnNumeric.toFixed(2)) + '" ' + (returnNumeric === null ? 'disabled' : '') + '>Return</button>';
      html += '              </div>';
      html += '          </div>';
      html += '      </div>';
      html += '  </div>';
      html += '</div>';
    });

    html += '</div>';
    html += '<p class="mt-4 mb-0"><small>Pricing type: ' + escapeHtml(resp.pricing_type || '-') + '</small></p>';

    document.getElementById('results').innerHTML = html;
  }

  function renderRouteSummary(data) {
    var summary = document.getElementById('route-summary');

    if (!data || !data.pickup || !data.dropoff) {
      summary.innerHTML = '<div class="alert alert-warning mb-0">Journey details were not found.</div>';
      return;
    }

    var dateText = data.date ? escapeHtml(data.date) : 'Not specified';
    var miles = Number(data.distance_miles);
    var distance = (data.distance_miles !== null && data.distance_miles !== undefined && data.distance_miles !== '' && Number.isFinite(miles))
      ? miles.toFixed(2) + ' miles'
      : 'Calculated at checkout';

    summary.innerHTML =
      '<div class="form-clt mb-3"><span>From</span><input type="text" readonly value="' + escapeHtml(data.pickup) + '"></div>' +
      '<div class="form-clt mb-3"><span>To</span><input type="text" readonly value="' + escapeHtml(data.dropoff) + '"></div>' +
      '<div class="form-clt mb-3"><span>Pickup Date</span><input type="text" readonly value="' + dateText + '"></div>' +
      '<div class="form-clt mb-0"><span>Distance</span><input type="text" readonly value="' + escapeHtml(distance) + '"></div>';
  }

  function showStatus(message, type) {
    var status = document.getElementById('quote-status');
    var iconClass = 'fa-circle-check';

    status.className = 'alert mb-4';

    if (type === 'error') {
      status.classList.add('alert-danger');
      iconClass = 'fa-circle-exclamation';
    } else if (type === 'loading') {
      status.classList.add('alert-info');
      iconClass = 'fa-spinner fa-spin';
    } else {
      status.classList.add('alert-success');
    }

    status.innerHTML = '<i class="fa-solid ' + iconClass + ' me-2"></i>' + escapeHtml(message);
  }

  function toNumberOrNull(value) {
    if (value === null || value === undefined || value === '') return null;
    var n = Number(value);
    return Number.isFinite(n) ? n : null;
  }

  function formatPrice(value) {
    return value === null ? 'N/A' : '&pound;' + value.toFixed(2);
  }

  function normalizeDateForApi(dateText) {
    var raw = String(dateText || '').trim();
    var parts = raw.split('-');

    // Convert dd-mm-yyyy from the home datepicker to yyyy-mm-dd for backend DATE columns.
    if (parts.length === 3 && parts[0].length === 2 && parts[1].length === 2 && parts[2].length === 4) {
      return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    return raw;
  }

  function escapeHtml(value) {
    return String(value || '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

  function handleBook(vehicle, numericPrice, tripType) {
    var pickupDateForApi = normalizeDateForApi(quoteData.date);

    console.log('=== Book Clicked ===');
    console.log('Vehicle:', vehicle);
    console.log('Price:', numericPrice);
    console.log('Trip type:', tripType);

    var payload = {
      pickup_address: quoteData.pickup,
      dropoff_address: quoteData.dropoff,
      pickup_date: pickupDateForApi,
      vehicle_type: vehicle,
      price: numericPrice,
      trip_type: tripType,
      source_url: quoteData.source_url || ''
    };

    fetch(API_URL.replace('/quote', '/quote/save'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json'
      },
      body: JSON.stringify(payload)
    })
    .then(function(r) { return r.json(); })
    .then(function(resp) {
      if (!resp.success) {
        alert('Could not save quote: ' + (resp.message || 'unknown error'));
        return;
      }

      console.log('Saved to DB. Quote ref:', resp.quote_ref, resp.return_ref ? '| Return ref: ' + resp.return_ref : '');

      localStorage.setItem('booking_data', JSON.stringify({
        quote_ref: resp.quote_ref,
        return_ref: resp.return_ref || null,
        pickup: quoteData.pickup,
        dropoff: quoteData.dropoff,
        pickup_date: pickupDateForApi,
        vehicle_type: vehicle,
        price: numericPrice,
        trip_type: tripType
      }));

      window.location.href = 'booking-confirmation';
    })
    .catch(function(err) {
      alert('Request failed: ' + err.message);
    });
  }
</script>

@include('partials.layouts.layoutsBottom')


