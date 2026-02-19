<div class="grid grid-cols-12 gap-6" style="max-width:100%; overflow-x:hidden;">
  <!-- Driver List Section -->
  <div class="col-span-8">
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead>
          <tr class="text-sm text-gray-500">
            <th class="p-2">Driver</th>
            <th class="p-2">Booking</th>
            <th class="p-2">Status</th>
            <th class="p-2">Since</th>
            <th class="p-2">Track</th>
          </tr>
        </thead>
      <tbody>
        @forelse($drivers as $d)
        <tr class="border-t" data-driver-id="{{ $d->id }}">
          <td class="p-2">{{ $d->name }}</td>
          <td class="p-2">@if($d->current_booking)<a href="{{ route('admin.bookings.show', $d->current_booking) }}" class="text-indigo-600 text-sm">{{ $d->current_booking->booking_code }}</a>@else None @endif</td>
          <td class="p-2">
            @php
              $colorClass = 'bg-gray-100 text-gray-700';
              if ($d->status_color === 'green') $colorClass = 'bg-green-100 text-green-700';
              elseif ($d->status_color === 'yellow') $colorClass = 'bg-yellow-100 text-yellow-700';
              elseif ($d->status_color === 'orange') $colorClass = 'bg-orange-100 text-orange-700';
              elseif ($d->status_color === 'blue') $colorClass = 'bg-blue-100 text-blue-700';
              elseif ($d->status_color === 'purple') $colorClass = 'bg-purple-100 text-purple-700';
              elseif ($d->status_color === 'red') $colorClass = 'bg-red-100 text-red-700';
            @endphp
            <span class="text-sm px-2 py-1 rounded {{ $colorClass }}">{{ $d->status_label }}</span>
          </td>
          <td class="p-2">{{ $d->status_since }}</td>
          <td class="p-2">
            @if($d->current_booking && ($d->status_label === 'POB' || $d->status_label === 'In Route' || $d->status_label === 'Arrived'))
              <button 
                onclick="startInlineTracking({{ $d->id }}, {{ $d->current_booking->id }}, '{{ $d->name }}', '{{ $d->current_booking->booking_code }}', '{{ $d->status_label }}')" 
                class="{{ $d->status_label === 'In Route' ? 'bg-purple-600 hover:bg-purple-700' : 'bg-blue-600 hover:bg-blue-700' }} text-white px-3 py-1 rounded text-sm flex items-center gap-1"
                title="Track Driver Live Location"
              >
                <i class="fas fa-satellite-dish"></i>
                <span>Track</span>
              </button>
            @else
              <button 
                onclick="startInlineTracking({{ $d->id }}, 0, '{{ $d->name }}', '', '{{ $d->status_label }}')" 
                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm flex items-center gap-1"
                title="Check Distance to Pickup"
              >
                <i class="fas fa-map-marker-alt"></i>
                <span>Track</span>
              </button>
            @endif
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="p-4 text-center text-gray-600">No drivers found.</td>
        </tr>
        @endforelse
      </tbody>
      </table>
    </div>

    <div class="mt-4">
      {{ $drivers->links() }}
    </div>
  </div>
  
  <!-- Map Tracking Section -->
  <div class="col-span-4">
    <div class="bg-white p-4 rounded-xl shadow-sm border border-gray-200">
      <div class="flex items-center gap-2 mb-4">
        <i class="fas fa-map-marker-alt text-indigo-600"></i>
        <h3 class="text-lg font-semibold text-gray-900">Live Driver Tracking</h3>
      </div>
      
      <div id="driver-tracking-map" class="w-full h-80 rounded-lg overflow-hidden border border-gray-300 bg-gray-100">
        <div class="flex items-center justify-center h-full text-gray-500">
          <div class="text-center">
            <i class="fas fa-map-marker-alt text-4xl mb-2"></i>
            <p>Click "Track" on any driver to view location</p>
          </div>
        </div>
      </div>
      
      <div id="tracking-info" class="mt-4 text-sm" style="display:none;">
        <div class="border-t pt-3">
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-gray-700">Driver:</span>
            <span id="tracked-driver-name" class="text-gray-900">-</span>
          </div>
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-gray-700">Booking:</span>
            <span id="tracked-booking-code" class="text-indigo-600">-</span>
          </div>
          
          {{-- Pickup Location Input for non-POB drivers --}}
          <div id="pickup-input-section" class="mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200" style="display:none;">
            <label class="block text-sm font-medium text-gray-700 mb-1">
              <i class="fas fa-search-location mr-1"></i>Enter Pickup Location
            </label>
            <div class="flex gap-2">
              <input type="text" id="pickup-address-input" 
                     class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                     placeholder="Type address to check distance...">
              <button onclick="calculateDistanceToPickup()" 
                      id="check-distance-btn"
                      class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-all flex items-center gap-1">
                <i class="fas fa-route"></i>
                <span>Check</span>
              </button>
            </div>
            <p class="text-xs text-gray-500 mt-1">Start typing to see address suggestions</p>
          </div>
          
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-gray-700">Status:</span>
            <span id="tracked-driver-status" class="px-2 py-1 rounded text-xs bg-blue-100 text-blue-700">POB</span>
          </div>
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-gray-700">Distance:</span>
            <span id="tracked-distance" class="text-gray-900 font-semibold">Calculating...</span>
          </div>
          <div class="flex items-center justify-between mb-2">
            <span class="font-medium text-gray-700">ETA:</span>
            <span id="tracked-eta" class="text-green-600 font-semibold">Calculating...</span>
          </div>
          <div class="flex items-center justify-between">
            <span class="font-medium text-gray-700">Last Update:</span>
            <span id="tracked-last-update" class="text-gray-600">-</span>
          </div>
          <button onclick="stopTracking()" class="mt-3 w-full bg-gray-500 hover:bg-gray-600 text-white px-3 py-2 rounded text-sm transition-all">
            <i class="fas fa-stop mr-1"></i>Stop Tracking
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function(){
  var rows = document.querySelectorAll('[data-driver-id]');
  rows.forEach(function(r){
    if (r.dataset.rowBound) return;
    r.dataset.rowBound = '1';

    var anchors = r.querySelectorAll('a');
    anchors.forEach(function(a){ if (a.dataset.stopPropagationAttached) return; a.dataset.stopPropagationAttached='1'; a.addEventListener('click', function(e){ e.stopPropagation(); }); });

    // row click does nothing; keep consistent with other list behavior
    r.addEventListener('click', function(e){ if (e.key === 'Enter' || e.keyCode === 13) { var id = r.getAttribute('data-driver-id'); if (!id) return; window.location.href = '{{ route("admin.drivers.show", ":id") }}'.replace(':id', id); } });
  });

  // Google Maps tracking variables
  let googleMap = null;
  let driverMarker = null;
  let pickupMarker = null;
  let destinationMarker = null;
  let directionsRenderer = null;
  let directionsService = null;
  let currentTrackingDriver = null;
  let trackingInterval = null;
  let isFirstLoad = true;
  let lastDriverPosition = null;
  let googleMapsLoaded = false;
  
  // Google Maps API Key
  const GOOGLE_MAPS_API_KEY = '{{ config("services.google.maps_api_key") }}';
  
  // Calculate straight-line distance using Haversine formula (for movement detection)
  function calculateStraightDistance(lat1, lon1, lat2, lon2) {
    const R = 6371;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
  }
  
  // Initialize Google Map
  function initGoogleMap() {
    if (googleMap) return;
    
    const mapContainer = document.getElementById('driver-tracking-map');
    mapContainer.innerHTML = '';
    
    // Create Google Map
    googleMap = new google.maps.Map(mapContainer, {
      center: { lat: 51.5074, lng: -0.1278 },
      zoom: 13,
      mapTypeControl: false,
      streetViewControl: false,
      fullscreenControl: true,
      zoomControl: true
    });
    
    // Initialize directions service and renderer
    directionsService = new google.maps.DirectionsService();
    directionsRenderer = new google.maps.DirectionsRenderer({
      map: googleMap,
      suppressMarkers: true, // We'll add custom markers
      polylineOptions: {
        strokeColor: '#4285F4',
        strokeWeight: 5,
        strokeOpacity: 0.8
      }
    });
  }
  
  // Load Google Maps API
  function loadGoogleMapsAPI(callback) {
    if (window.google && window.google.maps) {
      googleMapsLoaded = true;
      if (callback) callback();
      return;
    }
    
    window.initGoogleMapsCallback = function() {
      googleMapsLoaded = true;
      console.log('Google Maps API loaded successfully');
      if (callback) callback();
    };
    
    const script = document.createElement('script');
    script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_API_KEY}&libraries=places&callback=initGoogleMapsCallback`;
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  }
  
  // Start inline tracking for a driver
  let autocomplete = null;
  let customPickupLocation = null;
  
  window.startInlineTracking = function(driverId, bookingId, driverName, bookingCode, status) {
    currentTrackingDriver = { id: driverId, bookingId: bookingId, name: driverName, bookingCode: bookingCode, status: status };
    customPickupLocation = null;
    
    // Update UI
    document.getElementById('tracked-driver-name').textContent = driverName;
    document.getElementById('tracked-booking-code').textContent = bookingCode || 'No Booking';
    document.getElementById('tracked-last-update').textContent = 'Loading map...';
    document.getElementById('tracking-info').style.display = 'block';
    
    // Update status badge with appropriate color
    const statusBadge = document.getElementById('tracked-driver-status');
    statusBadge.textContent = status;
    if (status === 'POB') {
      statusBadge.className = 'px-2 py-1 rounded text-xs bg-green-100 text-green-700';
    } else if (status === 'In Route') {
      statusBadge.className = 'px-2 py-1 rounded text-xs bg-purple-100 text-purple-700';
    } else if (status === 'Idle') {
      statusBadge.className = 'px-2 py-1 rounded text-xs bg-gray-100 text-gray-700';
    } else if (status === 'Accepted') {
      statusBadge.className = 'px-2 py-1 rounded text-xs bg-blue-100 text-blue-700';
    } else {
      statusBadge.className = 'px-2 py-1 rounded text-xs bg-yellow-100 text-yellow-700';
    }
    
    // Show/hide pickup input based on status
    // Only show for drivers WITHOUT a booking (Idle, Accepted without In Route/POB)
    const pickupInputSection = document.getElementById('pickup-input-section');
    if (status !== 'POB' && status !== 'In Route') {
      // Show for Idle, Accepted, etc - drivers without active route
      pickupInputSection.style.display = 'block';
      document.getElementById('pickup-address-input').value = '';
      document.getElementById('tracked-distance').textContent = 'Enter pickup location';
      document.getElementById('tracked-eta').textContent = '-';
    } else {
      // Hide for POB and In Route - they have bookings with known locations
      pickupInputSection.style.display = 'none';
      document.getElementById('tracked-distance').textContent = 'Calculating...';
      document.getElementById('tracked-eta').textContent = 'Calculating...';
    }
    
    // Reset state
    isFirstLoad = true;
    lastDriverPosition = null;
    
    // Load Google Maps and start tracking
    loadGoogleMapsAPI(function() {
      initGoogleMap();
      
      // Initialize Places Autocomplete if not already done
      if (!autocomplete && status !== 'POB') {
        const input = document.getElementById('pickup-address-input');
        autocomplete = new google.maps.places.Autocomplete(input, {
          types: ['address'],
          componentRestrictions: { country: 'gb' } // UK only
        });
        
        autocomplete.addListener('place_changed', function() {
          const place = autocomplete.getPlace();
          if (place.geometry && place.geometry.location) {
            customPickupLocation = {
              lat: place.geometry.location.lat(),
              lng: place.geometry.location.lng(),
              address: place.formatted_address
            };
            // Automatically calculate distance when place selected
            calculateDistanceToPickup();
          }
        });
      }
      
      // Clear existing markers
      if (driverMarker) { driverMarker.setMap(null); driverMarker = null; }
      if (pickupMarker) { pickupMarker.setMap(null); pickupMarker = null; }
      if (destinationMarker) { destinationMarker.setMap(null); destinationMarker = null; }
      if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
      
      // Start location updates
      updateDriverLocation();
      trackingInterval = setInterval(updateDriverLocation, 20000); // Every 20 seconds
    });
  };
  
  // Update driver location
  function updateDriverLocation() {
    if (!currentTrackingDriver || !googleMapsLoaded) return;
    
    // For POB or In Route drivers with booking, use full tracking endpoint
    const isPOB = currentTrackingDriver.status === 'POB' && currentTrackingDriver.bookingId > 0;
    const isInRoute = currentTrackingDriver.status === 'In Route' && currentTrackingDriver.bookingId > 0;
    
    let fetchUrl;
    if (isPOB || isInRoute) {
      fetchUrl = `{{ route('admin.drivers.location', [':driverId', ':bookingId']) }}`
        .replace(':driverId', currentTrackingDriver.id)
        .replace(':bookingId', currentTrackingDriver.bookingId);
    } else {
      // For non-POB drivers, we'll fetch just driver location (bookingId = 0)
      fetchUrl = `{{ route('admin.drivers.location', [':driverId', '0']) }}`
        .replace(':driverId', currentTrackingDriver.id);
    }
    
    fetch(fetchUrl, {
      method: 'GET',
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      }
    })
    .then(response => response.json())
    .then(data => {
      if (!data.success) {
        document.getElementById('tracked-last-update').textContent = data.message || 'Location unavailable';
        return;
      }
      
      const driverLat = parseFloat(data.driver.latitude);
      const driverLng = parseFloat(data.driver.longitude);
      
      if (isNaN(driverLat) || isNaN(driverLng)) {
        document.getElementById('tracked-last-update').textContent = 'Invalid location data';
        return;
      }
      
      const driverPos = { lat: driverLat, lng: driverLng };
      
      // Update or create driver marker (car icon)
      if (!driverMarker) {
        driverMarker = new google.maps.Marker({
          position: driverPos,
          map: googleMap,
          icon: {
            url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
              <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40">
                <circle cx="20" cy="20" r="18" fill="#4285F4" stroke="white" stroke-width="3"/>
                <text x="20" y="26" text-anchor="middle" font-size="18">🚗</text>
              </svg>
            `),
            scaledSize: new google.maps.Size(40, 40),
            anchor: new google.maps.Point(20, 20)
          },
          title: currentTrackingDriver.name,
          zIndex: 1000
        });
      } else {
        driverMarker.setPosition(driverPos);
      }
      
      // For POB or In Route drivers, show pickup and destination from booking
      if (isPOB || isInRoute) {
        const pickupLat = parseFloat(data.pickup.latitude);
        const pickupLng = parseFloat(data.pickup.longitude);
        const destLat = parseFloat(data.destination.latitude);
        const destLng = parseFloat(data.destination.longitude);
        const destPos = { lat: destLat, lng: destLng };
        const pickupPos = { lat: pickupLat, lng: pickupLng };
        
        // Add pickup marker (green)
        if (!pickupMarker && !isNaN(pickupLat) && !isNaN(pickupLng)) {
          pickupMarker = new google.maps.Marker({
            position: pickupPos,
            map: googleMap,
            icon: {
              url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                  <circle cx="15" cy="15" r="12" fill="#10b981" stroke="white" stroke-width="2"/>
                  <text x="15" y="20" text-anchor="middle" font-size="12" fill="white">P</text>
                </svg>
              `),
              scaledSize: new google.maps.Size(30, 30),
              anchor: new google.maps.Point(15, 15)
            },
            title: 'Pickup: ' + (data.pickup.address || '')
          });
        }
        
        // Add destination marker (red)
        if (!destinationMarker && !isNaN(destLat) && !isNaN(destLng)) {
          destinationMarker = new google.maps.Marker({
            position: destPos,
            map: googleMap,
            icon: {
              url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
                  <circle cx="15" cy="15" r="12" fill="#ef4444" stroke="white" stroke-width="2"/>
                  <text x="15" y="20" text-anchor="middle" font-size="12" fill="white">D</text>
                </svg>
              `),
              scaledSize: new google.maps.Size(30, 30),
              anchor: new google.maps.Point(15, 15)
            },
            title: 'Destination: ' + (data.destination.address || '')
          });
        }
        
        // Check if driver moved significantly (more than 100m)
        const shouldRecalculateRoute = !lastDriverPosition || 
          calculateStraightDistance(lastDriverPosition.lat, lastDriverPosition.lng, driverLat, driverLng) > 0.1;
        
        // Calculate and display route using Google Directions
        // If In Route: show route to PICKUP (purple)
        // If POB: show route to DESTINATION (blue)
        if (shouldRecalculateRoute) {
          lastDriverPosition = { lat: driverLat, lng: driverLng };
          
          // Determine destination based on status
          let routeDestination, routeColor;
          if (isInRoute) {
            // In Route: route to PICKUP location
            routeDestination = pickupPos;
            routeColor = '#9333EA'; // Purple
          } else {
            // POB: route to DESTINATION
            routeDestination = destPos;
            routeColor = '#4285F4'; // Blue
          }
          
          if (!isNaN(routeDestination.lat) && !isNaN(routeDestination.lng)) {
            // Update directions renderer color
            directionsRenderer.setOptions({
              polylineOptions: {
                strokeColor: routeColor,
                strokeWeight: 5,
                strokeOpacity: 0.8
              }
            });
            
            directionsService.route({
              origin: driverPos,
              destination: routeDestination,
              travelMode: google.maps.TravelMode.DRIVING
            }, function(result, status) {
              if (status === 'OK') {
                // Display the route on the map
                directionsRenderer.setDirections(result);
                
                // Get distance and duration
                const leg = result.routes[0].legs[0];
                document.getElementById('tracked-distance').textContent = leg.distance.text;
                document.getElementById('tracked-eta').textContent = leg.duration.text;
                
                console.log(`Route calculated (${isInRoute ? 'to pickup' : 'to destination'}):`, leg.distance.text, leg.duration.text);
              } else {
                console.error('Directions request failed:', status);
                document.getElementById('tracked-distance').textContent = 'Route unavailable';
                document.getElementById('tracked-eta').textContent = 'N/A';
              }
            });
          }
        }
        
        // Fit bounds only on first load
        if (isFirstLoad) {
          const bounds = new google.maps.LatLngBounds();
          bounds.extend(driverPos);
          if (!isNaN(destLat) && !isNaN(destLng)) bounds.extend(destPos);
          if (!isNaN(pickupLat) && !isNaN(pickupLng)) bounds.extend(pickupPos);
          googleMap.fitBounds(bounds, { padding: 50 });
          isFirstLoad = false;
        }
      } else {
        // For non-POB drivers, just center on driver location
        if (isFirstLoad) {
          googleMap.setCenter(driverPos);
          googleMap.setZoom(14);
          isFirstLoad = false;
        }
        
        // If there's a custom pickup location, calculate route to it
        if (customPickupLocation) {
          const pickupPos = { lat: customPickupLocation.lat, lng: customPickupLocation.lng };
          
          // Check if driver moved significantly (more than 100m)
          const shouldRecalculateRoute = !lastDriverPosition || 
            calculateStraightDistance(lastDriverPosition.lat, lastDriverPosition.lng, driverLat, driverLng) > 0.1;
          
          if (shouldRecalculateRoute) {
            lastDriverPosition = { lat: driverLat, lng: driverLng };
            
            directionsService.route({
              origin: driverPos,
              destination: pickupPos,
              travelMode: google.maps.TravelMode.DRIVING
            }, function(result, status) {
              if (status === 'OK') {
                directionsRenderer.setDirections(result);
                const leg = result.routes[0].legs[0];
                document.getElementById('tracked-distance').textContent = leg.distance.text;
                document.getElementById('tracked-eta').textContent = leg.duration.text;
              }
            });
          }
        }
      }
      
      // Update last update time
      document.getElementById('tracked-last-update').textContent = new Date().toLocaleTimeString();
    })
    .catch(error => {
      console.error('Error fetching driver location:', error);
      document.getElementById('tracked-last-update').textContent = 'Connection error';
    });
  }
  
  // Calculate distance to manually entered pickup location
  window.calculateDistanceToPickup = function() {
    if (!currentTrackingDriver || !googleMapsLoaded) return;
    
    const input = document.getElementById('pickup-address-input');
    const address = input.value.trim();
    
    if (!address && !customPickupLocation) {
      alert('Please enter a pickup address');
      return;
    }
    
    document.getElementById('tracked-distance').textContent = 'Calculating...';
    document.getElementById('tracked-eta').textContent = 'Calculating...';
    
    // If we already have coordinates from autocomplete, use them
    if (customPickupLocation) {
      calculateRouteToCustomPickup();
      return;
    }
    
    // Otherwise, geocode the address
    const geocoder = new google.maps.Geocoder();
    geocoder.geocode({ address: address, region: 'uk' }, function(results, status) {
      if (status === 'OK' && results[0]) {
        customPickupLocation = {
          lat: results[0].geometry.location.lat(),
          lng: results[0].geometry.location.lng(),
          address: results[0].formatted_address
        };
        calculateRouteToCustomPickup();
      } else {
        document.getElementById('tracked-distance').textContent = 'Address not found';
        document.getElementById('tracked-eta').textContent = '-';
      }
    });
  };
  
  // Calculate route from driver to custom pickup location
  function calculateRouteToCustomPickup() {
    if (!customPickupLocation || !driverMarker) return;
    
    const driverPos = driverMarker.getPosition();
    const pickupPos = { lat: customPickupLocation.lat, lng: customPickupLocation.lng };
    
    // Clear old pickup marker and add new one
    if (pickupMarker) { pickupMarker.setMap(null); pickupMarker = null; }
    
    pickupMarker = new google.maps.Marker({
      position: pickupPos,
      map: googleMap,
      icon: {
        url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
          <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 30 30">
            <circle cx="15" cy="15" r="12" fill="#10b981" stroke="white" stroke-width="2"/>
            <text x="15" y="20" text-anchor="middle" font-size="12" fill="white">P</text>
          </svg>
        `),
        scaledSize: new google.maps.Size(30, 30),
        anchor: new google.maps.Point(15, 15)
      },
      title: 'Pickup: ' + customPickupLocation.address
    });
    
    // Calculate route
    directionsService.route({
      origin: { lat: driverPos.lat(), lng: driverPos.lng() },
      destination: pickupPos,
      travelMode: google.maps.TravelMode.DRIVING
    }, function(result, status) {
      if (status === 'OK') {
        directionsRenderer.setDirections(result);
        
        const leg = result.routes[0].legs[0];
        document.getElementById('tracked-distance').textContent = leg.distance.text;
        document.getElementById('tracked-eta').textContent = leg.duration.text;
        
        // Fit bounds to show both driver and pickup
        const bounds = new google.maps.LatLngBounds();
        bounds.extend(driverPos);
        bounds.extend(pickupPos);
        googleMap.fitBounds(bounds, { padding: 50 });
        
        console.log('Route to pickup calculated:', leg.distance.text, leg.duration.text);
      } else {
        console.error('Directions request failed:', status);
        document.getElementById('tracked-distance').textContent = 'Route unavailable';
        document.getElementById('tracked-eta').textContent = 'N/A';
      }
    });
  }
  
  // Stop tracking
  window.stopTracking = function() {
    if (trackingInterval) {
      clearInterval(trackingInterval);
      trackingInterval = null;
    }
    
    currentTrackingDriver = null;
    customPickupLocation = null;
    document.getElementById('tracking-info').style.display = 'none';
    document.getElementById('pickup-input-section').style.display = 'none';
    
    // Clear markers but keep the map
    if (driverMarker) { driverMarker.setMap(null); driverMarker = null; }
    if (pickupMarker) { pickupMarker.setMap(null); pickupMarker = null; }
    if (destinationMarker) { destinationMarker.setMap(null); destinationMarker = null; }
    if (directionsRenderer) directionsRenderer.setDirections({ routes: [] });
    
    // Reset map view
    if (googleMap) {
      googleMap.setCenter({ lat: 51.5074, lng: -0.1278 });
      googleMap.setZoom(6);
    }
  };
  
  // Pre-load Google Maps API for faster tracking start
  loadGoogleMapsAPI();
})();
</script>