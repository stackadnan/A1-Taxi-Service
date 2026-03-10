<?php
// API endpoint — change to production URL when deploying
$api_url = 'http://localhost/AirportServices/public/api/quote';
function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Quote Results</title>
</head>
<body>

<h2>Quote Results</h2>
<div id="results">Fetching prices...</div>
<br>
<a href="page1.php">Back</a>

<script>
  var API_URL = '<?= e($api_url) ?>';

  var data = {};
  try { data = JSON.parse(localStorage.getItem('quote_data') || '{}'); } catch(e){}

  if (!data.pickup_lat || !data.dropoff_lat) {
    document.getElementById('results').textContent = 'No quote data found. Please go back and complete the form.';
  } else {
    fetch(API_URL, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify({
        pickup_lat:       data.pickup_lat,
        pickup_lon:       data.pickup_lon,
        dropoff_lat:      data.dropoff_lat,
        dropoff_lon:      data.dropoff_lon,
        pickup_postcode:  data.pickup_postcode  || '',
        dropoff_postcode: data.dropoff_postcode || '',
        pickup_address:   data.pickup,
        dropoff_address:  data.dropoff,
        date:             data.date,
        distance_miles:   data.distance_miles   || null,
        source_url:       data.source_url
      })
    })
    .then(function(r){ return r.json(); })
    .then(function(resp) {
      if (!resp.success) {
        document.getElementById('results').textContent = 'Error: ' + (resp.message || 'Could not calculate quote.');
        return;
      }

      var html = '<p><strong>From:</strong> ' + data.pickup + '</p>';
      html += '<p><strong>To:</strong> ' + data.dropoff + '</p>';
      html += '<p><strong>Date:</strong> ' + data.date + '</p>';
      html += '<table border="1" cellpadding="8" cellspacing="0">';
      html += '<tr><th>Vehicle</th><th>Price</th><th>One-Way</th><th>Return</th></tr>';

      var vehicles = [
        { key: 'saloon',   label: 'Saloon' },
        { key: 'business', label: 'Business Class' },
        { key: 'mpv6',     label: 'MPV6' },
        { key: 'mpv8',     label: 'MPV8' }
      ];

      vehicles.forEach(function(v) {
        var price = resp.pricing ? resp.pricing[v.key + '_price'] : resp[v.key + '_price'];
        var priceText = (price !== null && price !== undefined) ? '£' + Number(price).toFixed(2) : 'N/A';
        var returnPrice = (price !== null && price !== undefined) ? '£' + (Number(price) * 2).toFixed(2) : 'N/A';
        html += '<tr>';
        html += '<td>' + v.label + '</td>';
        html += '<td>' + priceText + '</td>';
        html += '<td><button onclick="handleBook(\'' + v.label + '\', \'' + priceText + '\', \'one-way\')">One-Way</button></td>';
        html += '<td><button onclick="handleBook(\'' + v.label + '\', \'' + returnPrice + '\', \'return\')">Return</button></td>';
        html += '</tr>';
      });

      html += '</table>';
      html += '<p><small>Pricing type: ' + (resp.pricing_type || '-') + '</small></p>';
      document.getElementById('results').innerHTML = html;
    })
    .catch(function(err) {
      document.getElementById('results').textContent = 'Request failed: ' + err.message;
    });
  }

  function handleBook(vehicle, price, tripType) {
    var numericPrice = parseFloat(price.replace('£', '')) || 0;

    console.log('=== Book Clicked ===');
    console.log('Vehicle:', vehicle);
    console.log('Price:', price);
    console.log('Trip type:', tripType);

    var payload = {
      pickup_address:  data.pickup,
      dropoff_address: data.dropoff,
      pickup_date:     data.date,
      vehicle_type:    vehicle,
      price:           numericPrice,
      trip_type:       tripType,
      source_url:      data.source_url || ''
    };

    fetch(API_URL.replace('/quote', '/quote/save'), {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
      body: JSON.stringify(payload)
    })
    .then(function(r){ return r.json(); })
    .then(function(resp) {
      if (!resp.success) {
        alert('Could not save quote: ' + (resp.message || 'unknown error'));
        return;
      }
      console.log('Saved to DB. Quote ref:', resp.quote_ref, resp.return_ref ? '| Return ref: ' + resp.return_ref : '');

      // Store booking summary for page3
      localStorage.setItem('booking_data', JSON.stringify({
        quote_ref:       resp.quote_ref,
        return_ref:      resp.return_ref || null,
        pickup:          data.pickup,
        dropoff:         data.dropoff,
        pickup_date:     data.date,
        vehicle_type:    vehicle,
        price:           numericPrice,
        trip_type:       tripType
      }));

      window.location.href = 'page3.php';
    })
    .catch(function(err) {
      alert('Request failed: ' + err.message);
    });
  }
</script>

</body>
</html>
