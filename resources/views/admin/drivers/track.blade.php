<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Tracking - {{ $driver->name }} | Airport Services</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Maps API (Comment out for demo) -->
    <!-- <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.maps_api_key', '') }}&libraries=geometry,places"></script> -->
    
    <!-- Leaflet Maps (Free Alternative) -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <style>
        #map {
            height: calc(100vh - 120px);
            width: 100%;
        }
        .info-panel {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .status-indicator {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: .7; }
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Top Info Panel -->
    <div class="info-panel text-white p-4 shadow-lg">
        <div class="flex justify-between items-center max-w-7xl mx-auto">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-user-circle text-2xl"></i>
                    <div>
                        <h1 class="text-xl font-bold">{{ $driver->name }}</h1>
                        <p class="text-sm opacity-90">{{ $driver->vehicle_plate ?? 'N/A' }} • {{ $driver->phone }}</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <i class="fas fa-clipboard-list text-lg"></i>
                    <div>
                        <p class="font-semibold">{{ $booking->booking_code }}</p>
                        <p class="text-sm opacity-90">{{ $booking->status->name ?? 'Unknown' }} Status</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-2">
                    <div class="status-indicator w-3 h-3 bg-green-400 rounded-full"></div>
                    <div>
                        <p class="font-semibold">Live Tracking</p>
                        <p class="text-sm opacity-90" id="last-update">Connecting...</p>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center space-x-4">
                <button onclick="refreshLocation()" class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg transition-all">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="window.close()" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded-lg transition-all">
                    <i class="fas fa-times mr-2"></i>Close
                </button>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div id="map"></div>

    <!-- Bottom Info Panel -->
    <div class="bg-white border-t p-4 shadow-lg">
        <div class="max-w-7xl mx-auto flex justify-between items-center text-sm">
            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-500 rounded-full"></div>
                    <span>Pickup: {{ $booking->from_address }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                    <span>Destination: {{ $booking->to_address }}</span>
                </div>
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-blue-500 rounded-full"></div>
                    <span id="distance-info">Calculating...</span>
                </div>
            </div>
            <div id="connection-status" class="text-gray-600">
                <i class="fas fa-circle text-green-500"></i> Connected
            </div>
        </div>
    </div>

    <script>
    let map, driverMarker, pickupMarker, destinationMarker, routeControl;
    let driverId = {{ $driver->id }};
    let bookingId = {{ $booking->id }};
    let updateInterval;

    // Initialize map with Leaflet
    function initMap() {
        // Default center (London)
        const defaultCenter = [51.5074, -0.1278]; 
        
        map = L.map('map').setView(defaultCenter, 13);
        
        // Add tile layer (free OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Load initial data
        loadTrackingData();
        
        // Start real-time updates
        updateInterval = setInterval(loadTrackingData, 5000); // Update every 5 seconds
    }

    // Load tracking data from server
    function loadTrackingData() {
        fetch(`{{ route('admin.drivers.location', ['driver' => $driver->id, 'booking' => $booking->id]) }}`, {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateMap(data);
                updateLastUpdateTime(data.last_update);
                document.getElementById('connection-status').innerHTML = 
                    '<i class="fas fa-circle text-green-500"></i> Connected';
            } else {
                console.error('Failed to load tracking data:', data.error);
                document.getElementById('connection-status').innerHTML = 
                    '<i class="fas fa-circle text-red-500"></i> Connection Error';
            }
        })
        .catch(error => {
            console.error('Error loading tracking data:', error);
            document.getElementById('connection-status').innerHTML = 
                '<i class="fas fa-circle text-red-500"></i> Connection Error';
        });
    }

    // Update map with new data
    function updateMap(data) {
        const pickup = [parseFloat(data.pickup.lat), parseFloat(data.pickup.lng)];
        const destination = [parseFloat(data.destination.lat), parseFloat(data.destination.lng)];
        
        // Update pickup marker (GREEN)
        if (!pickupMarker) {
            pickupMarker = L.marker(pickup, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-green.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);
            
            // Add popup for pickup
            pickupMarker.bindPopup(`<b>Pickup Location</b><br>${data.pickup.address}`).openPopup();
        }

        // Update destination marker (RED)
        if (!destinationMarker) {
            destinationMarker = L.marker(destination, {
                icon: L.icon({
                    iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-red.png',
                    shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowSize: [41, 41]
                })
            }).addTo(map);
            
            // Add popup for destination
            destinationMarker.bindPopup(`<b>Drop-off Location</b><br>${data.destination.address}`);
        }

        // Update driver marker if location available (BLUE)
        if (data.location && data.location.lat && data.location.lng) {
            const driverPos = [parseFloat(data.location.lat), parseFloat(data.location.lng)];
            
            if (!driverMarker) {
                driverMarker = L.marker(driverPos, {
                    icon: L.icon({
                        iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-blue.png',
                        shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                        iconSize: [32, 51],
                        iconAnchor: [16, 51],
                        popupAnchor: [1, -34],
                        shadowSize: [51, 51]
                    })
                }).addTo(map);
            } else {
                // Update marker position smoothly
                driverMarker.setLatLng(driverPos);
            }
            
            // Add popup for driver
            driverMarker.bindPopup(`<b>${data.driver.name}</b><br>Vehicle: ${data.driver.vehicle_plate}<br>Phone: ${data.driver.phone}`);

            // Remove existing route line if any
            if (routeControl) {
                map.removeControl(routeControl);
            }
            
            // Add route line from driver to destination
            routeControl = L.polyline([driverPos, destination], {
                color: '#4F46E5',
                weight: 4,
                opacity: 0.7,
                dashArray: '10, 5'
            }).addTo(map);

            // Calculate distance to destination
            const distance = map.distance(driverPos, destination);
            const distanceKm = (distance / 1000).toFixed(1);
            document.getElementById('distance-info').textContent = `${distanceKm} km to destination`;
            
            // Set map view to show all markers
            const allPoints = [pickup, destination, driverPos];
            const bounds = L.latLngBounds(allPoints);
            map.fitBounds(bounds, { padding: [20, 20] });
        } else {
            // No driver location, show route from pickup to destination
            document.getElementById('distance-info').textContent = 'Waiting for driver location...';
            
            // Set map view to show pickup and destination
            const bounds = L.latLngBounds([pickup, destination]);
            map.fitBounds(bounds, { padding: [20, 20] });
        }
        
        // Add legend explanation
        updateLegend();
    }
    
    // Add legend to explain markers
    function updateLegend() {
        if (!document.getElementById('map-legend')) {
            const legend = L.control({ position: 'bottomright' });
            legend.onAdd = function(map) {
                const div = L.DomUtil.create('div', 'map-legend');
                div.id = 'map-legend';
                div.style.backgroundColor = 'white';
                div.style.padding = '10px';
                div.style.border = '2px solid #ccc';
                div.style.borderRadius = '5px';
                div.style.fontSize = '12px';
                div.innerHTML = `
                    <b>Live Tracking Legend:</b><br>
                    <span style="color: green;">●</span> Pickup Location<br>
                    <span style="color: red;">●</span> Drop-off Destination<br>
                    <span style="color: blue;">●</span> Driver Live Location<br>
                    <span style="color: #4F46E5;">━━</span> Route to Destination
                `;
                return div;
            };
            legend.addTo(map);
        }
    }

    // Update last update time
    function updateLastUpdateTime(lastUpdate) {
        const element = document.getElementById('last-update');
        if (lastUpdate) {
            const updateTime = new Date(lastUpdate);
            const now = new Date();
            const diffMinutes = Math.round((now - updateTime) / 60000);
            
            if (diffMinutes < 1) {
                element.textContent = 'Just now';
            } else if (diffMinutes < 60) {
                element.textContent = `${diffMinutes} min ago`;
            } else {
                element.textContent = updateTime.toLocaleTimeString();
            }
        } else {
            element.textContent = 'Waiting for location...';
        }
    }

    // Refresh location manually
    function refreshLocation() {
        loadTrackingData();
    }

    // Cleanup on page unload
    window.addEventListener('beforeunload', () => {
        if (updateInterval) {
            clearInterval(updateInterval);
        }
    });

    // Initialize map when page loads
    window.addEventListener('load', initMap);
    </script>
</body>
</html>