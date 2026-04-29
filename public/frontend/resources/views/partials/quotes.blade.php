<section class="hero-section hero-3 fix">
        
        <div class="swiper hero-slider-3">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="hero-image bg-cover" style="background-image: url('{{ \App\Support\GalleryPath::path('i/148') }}');">
                        <div class="line-shape" data-animation="slideInLeft" data-duration="3s" data-delay="2.1s">
                            <img src="{{ \App\Support\GalleryPath::path('i/145') }}" alt="shape-img">
                        </div>
                        <div class="line-shape-2" data-animation="slideInLeft" data-duration="3s" data-delay="2.3s">
                            <img src="{{ \App\Support\GalleryPath::path('i/146') }}" alt="shape-img">
                        </div>
                    </div>
                    <div class="container">
                        <div class="row justify-content-center">
                            <div class="col-xl-12">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 ">
                        
                                        <div class="product-search-area">
                                            <h2 class="search-text">Get Instant Quotes</h2>
                                            <div class="line-icon">
                                                <img src="{{ \App\Support\GalleryPath::path('i/147') }}" alt="img">
                                            </div>
                                            <form action="#" id="contact-form" method="POST">
                                                <div class="row g-4">
                                                    
                                                    <div class="col-md-12">
                                                        <div class="pickup-items">
                                                            <label class="field-label">FROM</label>
                                                            <div id="pickup" class="input-group" >
                                                                <input class="form-control" type="text" name="pickup-location" id="pickup-location" placeholder="Enter Pickup location">
                                                                <span class="input-group-addon"> <i class="fa-solid fa-location-pin"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="pickup-items">
                                                            <label class="field-label">TO</label>
                                                            <div id="destination" class="input-group" >
                                                                <input class="form-control" type="text" name="dropoff-location" id="dropoff-location" placeholder="Enter drop-off location">
                                                                <span class="input-group-addon"> <i class="fa-solid fa-location-pin"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="pickup-items">
                                                            <label class="field-label">Pickup Date</label>
                                                            <div id="datepicker" class="input-group date"
                                                                data-date-format="dd-mm-yyyy">
                                                                <input class="form-control" type="text"   readonly>
                                                                <span class="input-group-addon"> <i
                                                                        class="fa-solid fa-calendar-days"></i></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                     <div class="col-md-6">
                                                        <div class="pickup-items">
                                                            <p>Free Cancellation <br>up to 12 hours before pickup.</p>
                                                         </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="pickup-items">
                                                            <button type="submit" class="theme-btn">
                                                                GET INSTANT QUOTES
                                                            </button>
                                                            
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="pickup-items">
                                                            
                                                             
                                                            <span><a href="tel:+441582 801611">Call us for booking (+44) - 1582 801 - 611</a></span>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                   <div class="col-xl-6 col-lg-6">
                                        <div class="hero-content">

                                            <h4 class="text-white mb-2" data-animation="fadeInUp">
                                                    {{ $heroTitle ?? 'Reliable London Airport Taxi Service' }}
                                                </h4>

                                                <h1 class="text-white mb-3" data-animation="fadeInUp">
                                                    {{ $heroSubtitle ?? 'Airport Transfers Across the UK' }}
                                                </h1>

                                                @if(!empty($heroDescriptionHtml))
                                                    {!! $heroDescriptionHtml !!}
                                                @else
                                                    <p class="text-white mb-3" data-animation="fadeInUp">
                                                        {{ $heroDescription ?? 'Book professional London airport taxi transfers to and from all major UK airports. Whether you are travelling alone, with family, or in a group, we provide comfortable, punctual and affordable transport with fixed prices and no hidden charges.' }}
                                                    </p>

                                                    <p class="text-white mb-4" data-animation="fadeInUp">
                                                        {{ $heroAdditional ?? 'Reserve your taxi in advance through our quick online booking system and enjoy a smooth, stress-free journey to or from the airport.' }}
                                                    </p>

                                                    <p class="text-white mb-4" data-animation="fadeInUp">
                                                        {{ $contactSentence ?? 'Need assistance? Our customer support team is available' }} 
                                                        <strong>24 hours a day, 7 days a week</strong> on 
                                                        <strong>{{ $phoneNumber ?? '(+44) 1582 801 611' }}</strong>.
                                                    </p>

                                                    <ul class="text-white list-unstyled hero-features" data-animation="fadeInUp">
                                                        <li>✔ Free cancellation up to 12 hours before pickup</li>
                                                        <li>✔ Real-time flight tracking for timely pickups</li>
                                                        <li>✔ Fully licensed and professional drivers</li>
                                                        <li>✔ Comfortable vehicles for individuals and groups</li>
                                                        <li>✔ 24/7 customer support and assistance</li>
                                                    </ul>
                                                @endif

                                        </div>
                                    </div>
                                     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
             
            </div>
        </div>
        <!-- Quote Functionality Script Injection -->
        
</section>
        <?php
        $maps_api_key = env('GOOGLE_MAPS_API_KEY');
        ?>
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

                attachAutocomplete(document.getElementById('pickup-location'), 'pickup');
                attachAutocomplete(document.getElementById('dropoff-location'), 'dropoff');
            }

            function attachAutocomplete(el, kind) {
                if (!el) return;
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

                    // Create or update hidden fields for lat/lon/postcode
                    setHidden(kind + '_lat', lat);
                    setHidden(kind + '_lon', lng);

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
                    if (postcode) setHidden(kind + '_postcode', postcode.trim().toUpperCase());
                });
            }

            function setHidden(id, value) {
                var el = document.getElementById(id);
                if (!el) {
                    el = document.createElement('input');
                    el.type = 'hidden';
                    el.id = id;
                    el.name = id;
                    var form = document.getElementById('contact-form');
                    if (form) form.appendChild(el);
                }
                el.value = value;
            }

            document.addEventListener('DOMContentLoaded', function() {
                var form = document.getElementById('contact-form');
                if (!form) return;
                form.addEventListener('submit', function(e) {
                    e.preventDefault();

                    var pickup          = document.getElementById('pickup-location').value;
                    var dropoff         = document.getElementById('dropoff-location').value;
                    var date            = document.querySelector('#datepicker input') ? document.querySelector('#datepicker input').value : '';
                    var pickup_lat      = document.getElementById('pickup_lat') ? document.getElementById('pickup_lat').value : '';
                    var pickup_lon      = document.getElementById('pickup_lon') ? document.getElementById('pickup_lon').value : '';
                    var dropoff_lat     = document.getElementById('dropoff_lat') ? document.getElementById('dropoff_lat').value : '';
                    var dropoff_lon     = document.getElementById('dropoff_lon') ? document.getElementById('dropoff_lon').value : '';
                    var pickup_postcode = document.getElementById('pickup_postcode') ? document.getElementById('pickup_postcode').value : '';
                    var dropoff_postcode= document.getElementById('dropoff_postcode') ? document.getElementById('dropoff_postcode').value : '';

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

                            // Redirect immediately after saving quote data.
                            logAndRedirect('not-requested', distanceMiles);
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
                        window.location.href = 'quote-results';
                    }
                });
            });

            // Use Google DistanceMatrixService to get actual driving distance in miles
            // (same method the admin panel uses â€” avoids haversine straight-line mismatch)
            function getDrivingDistanceMiles(origin, destination, callback) {
                try {
                    var service = new google.maps.DistanceMatrixService();
                    var isDone = false;
                    var timeoutId = setTimeout(function() {
                        if (isDone) return;
                        isDone = true;
                        console.warn('DistanceMatrix timed out, falling back to backend distance handling');
                        callback(null);
                    }, 2500);

                    service.getDistanceMatrix({
                        origins: [origin],
                        destinations: [destination],
                        travelMode: google.maps.TravelMode.DRIVING,
                        unitSystem: google.maps.UnitSystem.IMPERIAL
                    }, function(response, status) {
                        if (isDone) {
                            return;
                        }

                        isDone = true;
                        clearTimeout(timeoutId);

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