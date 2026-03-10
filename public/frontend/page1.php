<?php
$maps_api_key = 'YOUR_GOOGLE_MAPS_API_KEY';
function e($v){ return htmlspecialchars($v, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Get a Quote</title>
</head>
<body>

<h2>Get a Quote</h2>

<form id="quote-form">
  <p>
    <label>Pickup Address</label><br>
    <input type="text" id="pickup" placeholder="Enter pickup address" autocomplete="off" style="width:300px">
    <input type="hidden" id="pickup_lat">
    <input type="hidden" id="pickup_lon">
    <input type="hidden" id="pickup_postcode">
  </p>

  <p>
    <label>Dropoff Address</label><br>
    <input type="text" id="dropoff" placeholder="Enter dropoff address" autocomplete="off" style="width:300px">
    <input type="hidden" id="dropoff_lat">
    <input type="hidden" id="dropoff_lon">
    <input type="hidden" id="dropoff_postcode">
  </p>

  <p>
    <label>Pickup Date</label><br>
    <input type="date" id="date">
  </p>

  <button type="submit">Get Quotes</button>
</form>

<script>
  // Store the page URL when user first lands here
  localStorage.setItem('capture_url', window.location.href);

  // Load Google Maps Places
  (function(){
    var s = document.createElement('script');
    s.src = 'https://maps.googleapis.com/maps/api/js?key=<?= e($maps_api_key) ?>&libraries=places&loading=async&callback=initMaps';
    s.async = true;
    s.defer = true;
    document.head.appendChild(s);
  })();

  function initMaps() {
    // Fix pac-container z-index so dropdown is always visible
    var style = document.createElement('style');
    style.innerHTML = '.pac-container { z-index: 9999 !important; }';
    document.head.appendChild(style);

    attachAutocomplete(document.getElementById('pickup'), 'pickup');
    attachAutocomplete(document.getElementById('dropoff'), 'dropoff');
  }

  function attachAutocomplete(el, kind) {
    var ac = new google.maps.places.Autocomplete(el, {
      componentRestrictions: { country: 'gb' },
      fields: ['formatted_address', 'address_components', 'geometry', 'name']
    });

    ac.addListener('place_changed', function() {
      var place = ac.getPlace();
      if (!place || !place.geometry || !place.geometry.location) return;

      var lat = place.geometry.location.lat();
      var lng = place.geometry.location.lng();

      // Use the typed/selected text exactly as shown in the input (same logic as admin panel)
      var selectedText = (el && el.value && el.value.trim()) ? el.value.trim() : '';
      var addressText = selectedText || place.name || place.formatted_address || '';
      el.value = addressText;

      document.getElementById(kind + '_lat').value = lat;
      document.getElementById(kind + '_lon').value = lng;

      // Extract postcode from address components
      var postcode = '';
      if (place.address_components) {
        for (var i = 0; i < place.address_components.length; i++) {
          var comp = place.address_components[i];
          if (comp.types && comp.types.indexOf('postal_code') !== -1) {
            postcode = comp.long_name;
            break;
          }
        }
      }
      if (!postcode && addressText) {
        var m = addressText.match(/[A-Z]{1,2}\d[\dA-Z]?\s*\d[A-Z]{2}/i);
        if (m) postcode = m[0];
      }
      if (postcode) document.getElementById(kind + '_postcode').value = postcode.trim().toUpperCase();
    });
  }

  document.getElementById('quote-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var pickup          = document.getElementById('pickup').value;
    var dropoff         = document.getElementById('dropoff').value;
    var date            = document.getElementById('date').value;
    var pickup_lat      = document.getElementById('pickup_lat').value;
    var pickup_lon      = document.getElementById('pickup_lon').value;
    var dropoff_lat     = document.getElementById('dropoff_lat').value;
    var dropoff_lon     = document.getElementById('dropoff_lon').value;
    var pickup_postcode = document.getElementById('pickup_postcode').value;
    var dropoff_postcode= document.getElementById('dropoff_postcode').value;

    if (!pickup_lat || !dropoff_lat) {
      alert('Please select pickup and dropoff addresses from the suggestions.');
      return;
    }

    // Get driving distance from Google (same as admin panel) then redirect
    getDrivingDistanceMiles(
      { lat: parseFloat(pickup_lat), lng: parseFloat(pickup_lon) },
      { lat: parseFloat(dropoff_lat), lng: parseFloat(dropoff_lon) },
      function(distanceMiles) {
        // Save all data for page2
        localStorage.setItem('quote_data', JSON.stringify({
          pickup: pickup,
          dropoff: dropoff,
          date: date,
          pickup_lat: pickup_lat,
          pickup_lon: pickup_lon,
          dropoff_lat: dropoff_lat,
          dropoff_lon: dropoff_lon,
          pickup_postcode: pickup_postcode,
          dropoff_postcode: dropoff_postcode,
          distance_miles: distanceMiles,
          source_url: window.location.href
        }));

        // Log to console then redirect
        fetch('https://api.ipify.org?format=json')
          .then(function(r){ return r.json(); })
          .then(function(d){ logAndRedirect(d.ip, distanceMiles); })
          .catch(function(){ logAndRedirect('unavailable', distanceMiles); });
      }
    );

    function logAndRedirect(ip, distanceMiles) {
      var captureUrl = localStorage.getItem('capture_url') || window.location.href;
      console.log('=== Get Quotes Clicked ===');
      console.log('Capture URL (localStorage):', captureUrl);
      console.log('Pickup:', pickup);
      console.log('Pickup  lat:', pickup_lat, '| lon:', pickup_lon);
      console.log('Dropoff:', dropoff);
      console.log('Dropoff lat:', dropoff_lat, '| lon:', dropoff_lon);
      console.log('Date:', date);
      console.log('Driving distance (miles):', distanceMiles);
      console.log('IP:', ip);
      window.location.href = 'page2.php';
    }
  });

  // Use Google DistanceMatrixService to get actual driving distance in miles
  // (same method the admin panel uses — avoids haversine straight-line mismatch)
  function getDrivingDistanceMiles(origin, destination, callback) {
    try {
      var service = new google.maps.DistanceMatrixService();
      service.getDistanceMatrix({
        origins: [origin],
        destinations: [destination],
        travelMode: google.maps.TravelMode.DRIVING,
        unitSystem: google.maps.UnitSystem.IMPERIAL
      }, function(response, status) {
        if (status === 'OK' &&
            response.rows[0] &&
            response.rows[0].elements[0] &&
            response.rows[0].elements[0].status === 'OK') {
          // distance.value is in metres; convert to miles
          var metres = response.rows[0].elements[0].distance.value;
          var miles  = metres / 1609.344;
          console.log('Driving distance from Google:', miles.toFixed(2), 'miles');
          callback(parseFloat(miles.toFixed(4)));
        } else {
          console.warn('DistanceMatrix failed (' + status + '), falling back to haversine');
          callback(null); // backend will haversine-fallback
        }
      });
    } catch(err) {
      console.warn('DistanceMatrix error, falling back:', err);
      callback(null);
    }
  }
</script>

</body>
</html>
