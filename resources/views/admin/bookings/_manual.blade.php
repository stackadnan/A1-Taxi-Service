<div class="grid grid-cols-12 gap-6" style="max-width:100%; overflow-x:hidden;">
  <style>
    /* Modern Clean Form Styles */
    .section-header {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      font-size: 0.875rem;
      font-weight: 600;
      color: #6366f1;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #e0e7ff;
    }
    
    .form-group {
      margin-bottom: 1rem;
    }
    
    .form-label {
      display: block;
      font-size: 0.875rem;
      font-weight: 500;
      color: #374151;
      margin-bottom: 0.375rem;
    }
    
    .input-with-icon {
      position: relative;
    }
    
    .input-icon {
      position: absolute;
      left: 0.75rem;
      top: 50%;
      transform: translateY(-50%);
      color: #9ca3af;
      pointer-events: none;
    }

    /* Reserve space for the baby seat dropdown so showing/hiding it won't shift nearby controls */
    #baby_seat_age_wrapper {
      min-width: 220px; /* adjust if needed */
      transition: opacity .12s ease, visibility .12s ease;
      /* keep element in layout even when "hidden" so checkbox doesn't shift */
      display: flex;
      align-items: center;
    }

    /* When hidden via JS we will set visibility:hidden and opacity:0 */
    #baby_seat_age_wrapper.hidden {
      visibility: hidden !important;
      opacity: 0 !important;
      pointer-events: none !important;
    }

    /* Ensure labels with inline-flex alignment remain stable in height */
    form#manual-booking-form label.inline-flex,
    .manual-card label.inline-flex {
      align-items: center !important;
      line-height: 18px !important;
      min-height: 18px !important;
      display: inline-flex !important;
    }

    /* Ensure the select inside has fixed height to avoid reflow */
    #baby_seat_age_wrapper .form-input {
      min-width: 220px;
      height: 38px;
      line-height: 1.15;
    }
    
    .form-input {
      width: 100%;
      padding: 0.625rem 0.75rem;
      border: 1px solid #d1d5db;
      border-radius: 0.375rem;
      font-size: 0.875rem;
      transition: all 0.15s ease;
    }
    
    .form-input-with-icon {
      padding-left: 2.5rem;
    }
    
    .form-input:focus {
      outline: none;
      border-color: #6366f1;
      box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .form-input::placeholder {
      color: #9ca3af;
    }
    
    .form-input:disabled,
    .form-input:read-only {
      background-color: #f9fafb;
      color: #6b7280;
      cursor: not-allowed;
    }

    /* Toggle switch styles for baby seat */
    .switch {
      display: inline-flex;
      align-items: center;
      cursor: pointer;
    }
    .switch input[type="checkbox"].sr-only {
      position: absolute !important;
      width: 1px !important;
      height: 1px !important;
      padding: 0 !important;
      margin: -1px !important;
      overflow: hidden !important;
      clip: rect(0,0,0,0) !important;
      white-space: nowrap !important;
      border: 0 !important;
    }
    .switch .toggle-track {
      width: 44px;
      height: 24px;
      background: #e5e7eb;
      border-radius: 9999px;
      padding: 3px;
      display: inline-flex;
      align-items: center;
      transition: background .15s ease;
    }
    .switch .toggle-thumb {
      width: 18px;
      height: 18px;
      background: #fff;
      border-radius: 9999px;
      box-shadow: 0 1px 2px rgba(0,0,0,0.2);
      transition: transform .15s ease;
      transform: translateX(0);
    }
    .switch input[type="checkbox"].sr-only:checked + .toggle-track {
      background: #6366f1;
    }
    .switch input[type="checkbox"].sr-only:checked + .toggle-track .toggle-thumb {
      transform: translateX(20px);
    }
    .switch:focus-visible .toggle-track {
      box-shadow: 0 0 0 3px rgba(99,102,241,0.2);
    }

    /* Keep legacy checkbox rules for other checkboxes */
    form#manual-booking-form input.form-input[type="checkbox"]:not([disabled]),
    .manual-card input.form-input[type="checkbox"]:not([disabled]) {
      cursor: pointer !important;
      -webkit-appearance: checkbox;
      appearance: checkbox;
      width: 18px !important;
      height: 18px !important;
      padding: 0 !important;
      margin: 0 !important;
      display: inline-block !important;
      vertical-align: middle !important;
      transform: none !important;
      transition: none !important;
      box-shadow: none !important;
    }

    /* Force pointer cursor and native dropdown appearance for SELECT controls so they don't show a disabled cursor on hover */
    form#manual-booking-form select.form-input:not([disabled]),
    form#manual-booking-form select.form-input-with-icon:not([disabled]),
    .manual-card select.form-input:not([disabled]) {
      cursor: pointer !important;
      -webkit-appearance: menulist-button;
      appearance: menulist-button;
    }
    
    .btn-primary {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      padding: 0.625rem 1.25rem;
      background-color: #6366f1;
      color: white;
      font-weight: 500;
      font-size: 0.875rem;
      border-radius: 0.375rem;
      border: none;
      cursor: pointer;
      transition: all 0.15s ease;
    }
    
    .btn-primary:hover {
      background-color: #4f46e5;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }
    
    .btn-primary:active {
      transform: scale(0.98);
    }
    
    .manual-card {
      background: white;
      border-radius: 0.75rem;
      box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
      border: 1px solid #e5e7eb;
    }
    
    /* Pricing list items */
    #vehicle-pricing-list .pricing-item {
      border-radius: 0.5rem;
      border: 1px solid #e5e7eb;
      padding: 0.75rem;
      margin-bottom: 0.5rem;
      transition: all 0.15s;
      background: #fff;
      cursor: pointer;
    }
    
    #vehicle-pricing-list .pricing-item:hover {
      border-color: #6366f1;
      background-color: #f5f3ff;
    }
    
    #vehicle-pricing-list .pricing-item.selected {
      border-color: #6366f1;
      background-color: #ede9fe;
    }
  </style>

  <div class="col-span-8">
    <form id="manual-booking-form" method="POST" action="{{ route('admin.bookings.manual.store') }}" class="manual-card p-6">
      @csrf
      
      <!-- Review Route Section -->
      <div class="section-header">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 7m0 13V7m0 0L9 4"></path>
        </svg>
        <span>Review Route</span>
      </div>
      
      <div class="grid grid-cols-12 gap-4 mb-6">
        <div class="col-span-5">
          <label class="form-label">Pickup Location</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <input type="text" name="pickup_address" class="form-input form-input-with-icon" placeholder="Search pickup location..." />
          </div>
        </div>
        <div class="col-span-5">
          <label class="form-label">Dropoff Location</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            <input type="text" name="dropoff_address" class="form-input form-input-with-icon" placeholder="Search dropoff location..." />
          </div>
        </div>
        <div class="col-span-2">
          <label class="form-label">&nbsp;</label>
          <button type="button" id="get-quote-btn" class="btn-primary w-full">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            Get Quote
          </button>
          <select name="vehicle_type" class="hidden">
            <option value="">select vehicle</option>
            @foreach($vehicleTypes as $v)
              <option value="{{ $v }}">{{ $v }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <!-- Booking Details Section -->
      <div class="section-header mt-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
        </svg>
        <span>Booking Details</span>
      </div>
      
      <div class="grid grid-cols-12 gap-4 mb-4">
        <div class="col-span-3">
          <label class="form-label">Booking ID</label>
          <div class="input-with-icon">
            <svg class="input-icon w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
            <input readonly value="{{ mt_rand(100000,999999) }}" class="form-input form-input-with-icon" />
          </div>
        </div>
        <div class="col-span-9">
          <label class="form-label">Search Previous Booking</label>
          <div class="input-with-icon" style="position:relative;">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="search" name="search_prev" id="booking-search" class="form-input form-input-with-icon" placeholder="Search by name, ID or phone..." autocomplete="off" />
            <div id="booking-search-results" class="bg-white border rounded shadow mt-1 max-h-72 overflow-auto" style="position:absolute;left:0;right:0;z-index:50;display:none;"></div>
          </div>
        </div>
      </div>
      
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div>
          <label class="form-label">Pickup Address Line</label>
          <input type="text" name="pickup_address_line" class="form-input" placeholder="Detailed Pickup Address" />
        </div>
        <div>
          <label class="form-label">Dropoff Address Line</label>
          <input type="text" name="dropoff_address_line" class="form-input" placeholder="Detailed Dropoff Address" />
        </div>
        <div>
          <label class="form-label">Vehicle Type</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
            </svg>
            <input type="text" name="vehicle_type_text" class="form-input form-input-with-icon" placeholder="e.g. Saloon" />
          </div>
        </div>
        <div>
          <label class="form-label">Price (EUR)</label>
          <div class="input-with-icon">
            <span class="input-icon" style="color: #6b7280;">€</span>
            <input id="booking-charges-input" type="number" step="0.01" name="booking_charges" value="{{ old('booking_charges') }}" class="form-input form-input-with-icon" placeholder="0.00" />
          </div>
        </div>
      </div>

      <!-- Passenger Details Section -->
      <div class="section-header mt-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
        </svg>
        <span>Passenger Details</span>
      </div>
      
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div>
          <label class="form-label">Name</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <input type="text" name="passenger_name" class="form-input form-input-with-icon" placeholder="Full Name" />
          </div>
        </div>
        <div>
          <label class="form-label">Phone</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
            </svg>
            <input type="text" name="phone" class="form-input form-input-with-icon" placeholder="Phone Number" />
          </div>
        </div>
        <div>
          <label class="form-label">Email</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <input type="email" name="email" class="form-input form-input-with-icon" placeholder="Email Address" />
          </div>
        </div>
        <div>
          <label class="form-label">Alt Phone</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <input type="text" name="alt_phone" class="form-input form-input-with-icon" placeholder="Alternate Phone" />
          </div>
        </div>
      </div>

      <!-- Trip Information Section -->
      <div class="section-header mt-6">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <span>Trip Information</span>
      </div>
      
      <div class="grid grid-cols-4 gap-4 mb-4">
        <div>
          <label class="form-label">Passengers</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <input type="number" name="passengers" class="form-input form-input-with-icon" placeholder="Full Capacity" />
          </div>
        </div>
        <div>
          <label class="form-label">Luggage</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>
            <input type="text" name="luggage" class="form-input form-input-with-icon" placeholder="Bags count" />
          </div>
        </div>
        <div>
          <label class="form-label">Flight Number</label>
          <div class="input-with-icon">
            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
            </svg>
            <input type="text" name="flight_number" class="form-input form-input-with-icon" placeholder="Flight #" />
          </div>
        </div>
        <div>
          <label class="form-label">Pickup Date mm/dd/yyyy</label>
          <input type="date" name="pickup_date" class="form-input" min="{{ \Carbon\Carbon::today()->format('Y-m-d') }}" value="{{ old('pickup_date', \Carbon\Carbon::today()->format('Y-m-d')) }}" />
        </div>
      </div>
      
      <div class="grid grid-cols-4 gap-4 mb-6">
        <div>
          <label class="form-label">Pickup Time --:-- --</label>
          <input type="time" name="pickup_time" class="form-input" />
        </div>
        <div>
          <label class="form-label">Flight Time --:-- --</label>
          <input type="time" name="flight_time" class="form-input" />
        </div>
        <div>
          <label class="form-label">Meet & Greet Select option</label>
          <select name="meet_and_greet" class="form-input">
            <option value="">Select option</option>
            <option value="1">Yes</option>
            <option value="0">No</option>
          </select>
        </div>
        <div>
          <label class="form-label">Baby Seat</label>
          <div class="flex items-center gap-3">
            <label class="switch" for="baby_seat_toggle" aria-label="Enable baby seat">
              <!-- ensure a boolean-friendly value: hidden 0 fallback + checkbox value=1 overrides when checked -->
              <input type="hidden" name="baby_seat" value="0" />
              <input type="checkbox" name="baby_seat" value="1" id="baby_seat_toggle" class="sr-only" {{ old('baby_seat') ? 'checked' : '' }} />
              <span class="toggle-track" aria-hidden="true">
                <span class="toggle-thumb"></span>
              </span>
            </label>
            <div id="baby_seat_age_wrapper" class="{{ old('baby_seat') ? '' : 'hidden' }}" style="flex:1;">
              <div class="input-with-icon">
                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3z"></path>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 20c0-3.866 3.582-7 8-7s8 3.134 8 7"></path>
                </svg>
                <select name="baby_seat_age" class="form-input form-input-with-icon">
                  <option value="">Select child seat</option>
                  <option value="0-1" {{ old('baby_seat_age') == '0-1' ? 'selected' : '' }}>0 to 1 Years</option>
                  <option value="1-3" {{ old('baby_seat_age') == '1-3' ? 'selected' : '' }}>1 to 3 Years</option>
                  <option value="3-5" {{ old('baby_seat_age') == '3-5' ? 'selected' : '' }}>3 to 5 Years</option>
                  <option value="5-12" {{ old('baby_seat_age') == '5-12' ? 'selected' : '' }}>5 to 12 Years</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>
      
      <!-- Message Section -->
      <div class="mb-6">
        <label class="form-label">Message to Driver</label>
        <textarea name="message_to_driver" class="form-input" rows="3" placeholder="Enter instructions for the driver..."></textarea>
      </div>

      <!-- Bottom Actions -->
      <div class="grid grid-cols-12 gap-4 items-end pt-4 border-t border-gray-200">
        <div class="col-span-8">
          <label class="form-label">Internal Admin Note</label>
          <input type="text" name="message_to_admin" class="form-input" placeholder="Private note..." />
        </div>
        <div class="col-span-2">
          <label class="form-label">Source</label>
          <select name="source" class="form-input">
            <option value="">Select</option>
            <option value="phone">Phone</option>
            <option value="web">Web</option>
            <option value="app">App</option>
          </select>
        </div>
        <div class="col-span-2">
          <label class="form-label">&nbsp;</label>
          <button type="submit" class="btn-primary w-full">Make Changes</button>
        </div>
      </div>

    </form>
  </div>

  <div class="col-span-4">
    <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-200 manual-card card-inner">
      <!-- Legacy Leaflet map (fallback) -->
      <div id="manual-map" class="w-full h-80 rounded-lg overflow-hidden"></div>
      <!-- Google map (preferred) -->
      <div id="manual-google-map" class="w-full h-80 rounded-lg overflow-hidden" style="display:none;"></div>

      <div class="mt-4 text-sm text-gray-700">
        <div>Time required: <span id="manual-time">-</span></div>
        <div>Distance: <span id="manual-distance">0</span> miles</div>
      </div>

      <!-- Pricing list (hidden until quote) -->
      <div id="vehicle-pricing-list" class="mt-4" style="display:none;">
        <!-- Dynamically populated with available vehicle prices and source -->
      </div>

      <div id="manual-charges-container" style="display:none;">
        <div class="mt-2 text-sm text-gray-700" id="manual-zone-pricing">Zone price: <span id="manual-zone-price">-</span></div>
        <!-- Visible editable booking charges field -->
        <div class="mt-2 text-sm text-gray-700">Booking Charges £</div>
        <input type="number" step="0.01" name="booking_charges" id="booking-charges-input" class="border rounded p-2 w-full mt-1 h-10" placeholder="0.00" />
        <div class="text-xs text-gray-500 mt-1">You can edit this value after getting a quote.</div>
      </div>
    </div>
  </div>
</div>

<!-- Leaflet assets (local) -->
<link rel="stylesheet" href="{{ asset('vendor/leaflet/leaflet.css') }}" />
<script src="{{ asset('vendor/leaflet/leaflet.js') }}"></script>

<!-- Google Maps JS (Places) will be loaded when this partial is injected -->
<script>
  // Load Google Maps asynchronously with proper loading parameter
  (function(){
    var script = document.createElement('script');
    script.src = 'https://maps.googleapis.com/maps/api/js?key={{ config("services.google.maps_api_key") }}&libraries=places&loading=async&callback=initMaps';
    script.async = true;
    script.defer = true;
    document.head.appendChild(script);
  })();
</script>

<style>
  .autocomplete-list { position: absolute; z-index: 50; background: #fff; border: 1px solid #e5e7eb; border-radius: 4px; max-height: 240px; overflow:auto; width:100%; box-shadow: 0 4px 8px rgba(0,0,0,0.06); }
  .autocomplete-item { padding: 8px 10px; font-size: 13px; color: #111827; }
  .autocomplete-item:hover, .autocomplete-item:focus { background:#f3f4f6; cursor:pointer; }
</style>

<script>
(function(){
  // Suppress Google Maps deprecation warnings and other console noise
  (function(){
    var originalWarn = console.warn;
    var originalError = console.error;
    
    console.warn = function() {
      var msg = Array.prototype.slice.call(arguments).join(' ');
      // Filter out Google Maps deprecation warnings
      if (msg.indexOf('google.maps.places.Autocomplete') !== -1 ||
          msg.indexOf('google.maps.Marker') !== -1 ||
          msg.indexOf('PlaceAutocompleteElement') !== -1 ||
          msg.indexOf('AdvancedMarkerElement') !== -1 ||
          msg.indexOf('loading=async') !== -1) {
        return; // Suppress these specific warnings
      }
      originalWarn.apply(console, arguments);
    };
    
    // Suppress interceptor.js errors (third-party widget errors)
    window.addEventListener('error', function(e) {
      if (e && e.filename && (e.filename.indexOf('interceptor.js') !== -1 || e.filename.indexOf('widgetId') !== -1)) {
        e.preventDefault();
        return true; // Suppress this error
      }
    }, true);
  })();

  // simple toast helper
  function showToast(msg){
    var container = document.getElementById('toast-container');
    if (!container) { container = document.createElement('div'); container.id='toast-container'; container.style.position='fixed'; container.style.right='20px'; container.style.bottom='20px'; container.style.zIndex=10000; document.body.appendChild(container); }
    var t = document.createElement('div'); t.className='bg-black text-white px-4 py-2 rounded shadow'; t.style.opacity='0'; t.textContent = msg; container.appendChild(t); requestAnimationFrame(function(){ t.style.opacity='1'; t.style.transition='opacity 200ms'; }); setTimeout(function(){ t.style.opacity='0'; setTimeout(function(){ t.remove(); }, 300); }, 2500);
  }

  // init map
  try {
    var map = L.map('manual-map').setView([51.5074, -0.1278], 9);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      maxZoom: 19,
      attribution: '© OpenStreetMap'
    }).addTo(map);

    // Fix Leaflet default icon paths (use CDN) to avoid missing images in development
    try {
      if (L && L.Icon && L.Icon.Default && L.Icon.Default.mergeOptions) {
        L.Icon.Default.mergeOptions({
          iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon-2x.png',
          iconUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon.png',
          shadowUrl: 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-shadow.png'
        });
        console.debug('Leaflet default icons set to CDN URLs');
      }
    } catch (e) { console.warn('Could not set Leaflet default icon urls', e); }

  } catch(e){ console.error('Map init failed', e); }

  var pickupMarker = null;
  var dropoffMarker = null;

  function haversineDistance(lat1, lon1, lat2, lon2){
    function toRad(x){ return x*Math.PI/180; }
    var R = 6371; // km
    var dLat = toRad(lat2-lat1);
    var dLon = toRad(lon2-lon1);
    var a = Math.sin(dLat/2)*Math.sin(dLat/2) + Math.cos(toRad(lat1))*Math.cos(toRad(lat2))*Math.sin(dLon/2)*Math.sin(dLon/2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    var d = R * c;
    return d; // km
  }

  function updateDistanceAndTime(){
    var plat = parseFloat(document.querySelector('input[name="pickup_lat"]').value || '');
    var plon = parseFloat(document.querySelector('input[name="pickup_lon"]').value || '');
    var dlat = parseFloat(document.querySelector('input[name="dropoff_lat"]').value || '');
    var dlon = parseFloat(document.querySelector('input[name="dropoff_lon"]').value || '');
    if (!isNaN(plat) && !isNaN(plon) && !isNaN(dlat) && !isNaN(dlon)){
      var km = haversineDistance(plat, plon, dlat, dlon);
      var miles = (km * 0.621371).toFixed(1);
      document.getElementById('manual-distance').textContent = miles;
      // rough time estimate assuming average 40 mph
      var hours = Math.max(0.1, (km * 0.621371) / 40);
      var mins = Math.round(hours * 60);
      document.getElementById('manual-time').textContent = mins + ' min';
    }
  }

  // Initialize Google Places Autocomplete and Directions proxy integration
  function decodePolyline(encoded) {
    if (!encoded) return [];
    var points = [];
    var index = 0, len = encoded.length;
    var lat = 0, lng = 0;
    while (index < len) {
      var b, shift = 0, result = 0;
      do { b = encoded.charCodeAt(index++) - 63; result |= (b & 0x1f) << shift; shift += 5; } while (b >= 0x20);
      var dlat = ((result & 1) ? ~(result >> 1) : (result >> 1));
      lat += dlat;
      shift = 0; result = 0;
      do { b = encoded.charCodeAt(index++) - 63; result |= (b & 0x1f) << shift; shift += 5; } while (b >= 0x20);
      var dlng = ((result & 1) ? ~(result >> 1) : (result >> 1));
      lng += dlng;
      points.push([lat / 1e5, lng / 1e5]);
    }
    return points;
  }

  function removeRoute(){ if (typeof routeLine !== 'undefined' && routeLine) { try { map.removeLayer(routeLine); } catch(e){} routeLine = null; } }

  function getDefaultIcon(){
    // prefer retina icon if device supports it
    var iconUrl = 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon.png';
    var iconRetinaUrl = 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-icon-2x.png';
    var shadowUrl = 'https://unpkg.com/leaflet@1.9.3/dist/images/marker-shadow.png';
    var useRetina = (window.devicePixelRatio && window.devicePixelRatio > 1);
    return L.icon({
      iconUrl: useRetina ? iconRetinaUrl : iconUrl,
      shadowUrl: shadowUrl,
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      tooltipAnchor: [16, -28],
      shadowSize: [41, 41]
    });
  }

  function setMarker(kind, lat, lon){
    try {
      // If Google map is active, set Google markers and compute route via client-side DirectionsService
      if (window.google && window.google.maps && document.getElementById('manual-google-map') && document.getElementById('manual-google-map').style.display !== 'none'){
        if (window.setGoogleMarker) window.setGoogleMarker(kind, lat, lon);
        // when both lat/lon present on inputs, use client route computation
        var plat = document.querySelector('input[name="pickup_lat"]').value;
        var plon = document.querySelector('input[name="pickup_lon"]').value;
        var dlat = document.querySelector('input[name="dropoff_lat"]').value;
        var dlon = document.querySelector('input[name="dropoff_lon"]').value;
        if (plat && plon && dlat && dlon && window.computeRouteClient) {
          window.computeRouteClient();
        }
        return;
      }

      var icon = getDefaultIcon();
      console.debug('Creating marker', kind, {lat:lat, lon:lon, icon: icon.options && icon.options.iconUrl});

      if (kind === 'pickup'){
        if (pickupMarker) pickupMarker.remove();
        pickupMarker = L.marker([lat, lon], { title: 'Pickup', icon: icon }).addTo(map);
      } else {
        if (dropoffMarker) dropoffMarker.remove();
        dropoffMarker = L.marker([lat, lon], { title: 'Dropoff', icon: icon }).addTo(map);
      }

      // verify image loads; if fails, replace with a simple DivIcon fallback
      var img = new Image(); img.onload = function(){ console.debug('Marker image loaded:', icon.options && icon.options.iconUrl); };
      img.onerror = function(){ console.warn('Marker image failed to load, using DivIcon fallback');
        var fallback = L.divIcon({ className: 'booking-marker-fallback', html: '<div style="width:14px;height:14px;border-radius:7px;background:#2563eb;border:2px solid #fff;"></div>' });
        if (kind === 'pickup'){ if (pickupMarker) { pickupMarker.setIcon(fallback); } }
        else { if (dropoffMarker) { dropoffMarker.setIcon(fallback); } }
      };
      img.src = icon.options && icon.options.iconUrl;

    } catch(e){ console.error('setMarker error', e); }

    var pts = [];
    if (pickupMarker) pts.push(pickupMarker.getLatLng());
    if (dropoffMarker) pts.push(dropoffMarker.getLatLng());
    if (pts.length === 1) map.setView(pts[0], 12);
    if (pts.length === 2) {
      var bounds = L.latLngBounds(pts); map.fitBounds(bounds.pad(0.25));
      // fetch server-side directions (cached) and draw
      fetchRoute();
    }
  }

  // call server proxy to get directions (Google Directions) and draw route on Leaflet or Google map (fallback)
  function fetchRoute(){
    var plat = document.querySelector('input[name="pickup_lat"]').value;
    var plon = document.querySelector('input[name="pickup_lon"]').value;
    var dlat = document.querySelector('input[name="dropoff_lat"]').value;
    var dlon = document.querySelector('input[name="dropoff_lon"]').value;
    if (!plat || !plon || !dlat || !dlon) return;

    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch('{{ route('admin.bookings.directions') }}', { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: JSON.stringify({ pickup_lat: plat, pickup_lon: plon, dropoff_lat: dlat, dropoff_lon: dlon }) })
    .then(function(res){ return res.json(); })
    .then(function(json){
      if (!json.success) { console.warn('Directions failed', json); removeRoute(); return; }
      removeRoute();

      // If Google map is active, draw polyline on Google map
      if (window.google && window.google.maps && document.getElementById('manual-google-map') && document.getElementById('manual-google-map').style.display !== 'none'){
        var path = decodePolyline(json.polyline).map(function(p){ return { lat: p[0], lng: p[1] }; });
        if (window.googleRoute) { window.googleRoute.setMap(null); window.googleRoute = null; }
        window.googleRoute = new google.maps.Polyline({ path: path, strokeColor: '#2563eb', strokeOpacity: 0.9, strokeWeight: 5 });
        window.googleRoute.setMap(window.googleMap);
        // fit bounds
        var bounds = new google.maps.LatLngBounds(); path.forEach(function(pt){ bounds.extend(pt); });
        window.googleMap.fitBounds(bounds);
      } else {
        var pts = decodePolyline(json.polyline);
        if (pts && pts.length) {
          var latlngs = pts.map(function(p){ return [p[0], p[1]]; });
          routeLine = L.polyline(latlngs, { color: '#2563eb', weight: 4, opacity: 0.9 }).addTo(map);
          map.fitBounds(routeLine.getBounds().pad(0.25));
        }
      }

      // show distance/time
      if (json.distance !== undefined) document.getElementById('manual-distance').textContent = ( (json.distance/1000)*0.621371 ).toFixed(1);
      if (json.duration !== undefined) document.getElementById('manual-time').textContent = Math.round(json.duration/60) + ' min';
    }).catch(function(err){ console.error('Directions error', err); removeRoute(); });
  }

  function initPlaces(){
    try {
      // make sure autocomplete dropdown is visible above other UI
      var css = document.createElement('style'); css.innerHTML = '.pac-container{ z-index: 10050 !important; }'; document.head.appendChild(css);

      var pickupEls = [document.querySelector('input[name="pickup_address"]'), document.querySelector('input[name="pickup_address_line"]')];
      var dropEls = [document.querySelector('input[name="dropoff_address"]'), document.querySelector('input[name="dropoff_address_line"]')];

      function attachAutocompleteTo(el, kind){
        if (!el) return;
        
        // Use the standard Autocomplete (suppress console warnings by handling them gracefully)
        var ac = new google.maps.places.Autocomplete(el, { 
          componentRestrictions: { country: 'gb' },
          fields: ['formatted_address', 'address_components', 'geometry', 'name']
        });
        
        ac.addListener('place_changed', function(){ 
          var place = ac.getPlace(); 
          console.debug('place_changed', {kind: kind, element: el.name, place: place});
          if (place && place.geometry && place.geometry.location){ 
            var lat = place.geometry.location.lat(); 
            var lng = place.geometry.location.lng(); 
            
            // Prefer the place name for display (e.g., "Heathrow Airport"). Fall back to formatted_address and finally the element value.
            // var displayName = place.name || (place.formatted_address ? (place.formatted_address.split(',')[0] || place.formatted_address) : null);
            // var addressText = displayName || place.formatted_address || el.value;
            
            // var addressText = place.name && place.formatted_address ? (place.name + ', ' + place.formatted_address) : (place.formatted_address || el.value);
        //   var addressText = place.formatted_address;
            // if (!addressText) return;



            var selectedText = (el && el.value && el.value.trim()) ? el.value.trim() : '';
            var addressText = selectedText || place.name || place.formatted_address || '';

            // set both corresponding inputs (top and line) to keep UI in sync
            try {
              var topInput = document.querySelector('input[name="'+kind+'_address"]');
              var lineInput = document.querySelector('input[name="'+kind+'_address_line"]');
              if (topInput) topInput.value = addressText;
              if (lineInput) lineInput.value = addressText;
              // also set the input that triggered this event
              el.value = addressText;

              // Reapply after a short delay to avoid other listeners overwriting our chosen value
              setTimeout(function(){
                try {
                  if (topInput && topInput.value !== addressText) topInput.value = addressText;
                  if (lineInput && lineInput.value !== addressText) lineInput.value = addressText;
                  if (el && el.value !== addressText) el.value = addressText;
                } catch(e){ console.warn('reapply addressText failed', e); }
              }, 120);
            } catch(e){ console.warn('Failed to sync address inputs', e); }

            document.querySelector('input[name="'+kind+'_lat"]').value = lat; 
            document.querySelector('input[name="'+kind+'_lon"]').value = lng; 
            
            // extract postcode from address components if present
            var postcode = '';
            try {
              if (place.address_components && place.address_components.length) {
                for (var i=0;i<place.address_components.length;i++){
                  var comp = place.address_components[i];
                  if (comp.types && comp.types.indexOf('postal_code') !== -1) { postcode = comp.long_name; break; }
                }
              }
            } catch(e){}
            
            try { if (!postcode && addressText) { var m = addressText.match(/[A-Z]{1,2}\d[\dA-Z]?\s*\d[A-Z]{2}/i); if (m) postcode = m[0]; } } catch(e){}
            if (postcode) document.querySelector('input[name="'+kind+'_postcode"]').value = postcode.trim().toUpperCase();
            setMarker(kind, lat, lng); 
          } 
        });
      }

      pickupEls.forEach(function(el){ attachAutocompleteTo(el, 'pickup'); });
      dropEls.forEach(function(el){ attachAutocompleteTo(el, 'dropoff'); });

    } catch(e){ console.error('initPlaces error', e); }
  }

  // Geocode an address to lat/lng using Google Geocoder (returns a Promise)
  function geocodeAddress(address){
    return new Promise(function(resolve, reject){
      try {
        if (!address) return reject('empty_address');
        if (window.google && window.google.maps && window.google.maps.Geocoder){
          var geocoder = new google.maps.Geocoder();
          geocoder.geocode({ address: address, componentRestrictions: { country: 'GB' } }, function(results, status){
            if (status === 'OK' && results && results.length){
              var place = results[0];
              var lat = place.geometry.location.lat();
              var lng = place.geometry.location.lng();
              var postcode = '';
              try {
                if (place.address_components && place.address_components.length) {
                  for (var i=0;i<place.address_components.length;i++){
                    var comp = place.address_components[i];
                    if (comp.types && comp.types.indexOf('postal_code') !== -1) { postcode = comp.long_name; break; }
                  }
                }
              } catch(e){}
              resolve({ lat: lat, lng: lng, formatted_address: place.formatted_address, postcode: postcode });
              return;
            }
            reject(status || 'no_results');
          });
        } else {
          // Google Geocoder not available
          reject('geocoder_unavailable');
        }
      } catch(e){ reject(e); }
    });
  }

  // Zone lookup & quoting helpers
  var zoneLookupUrl = "{{ route('admin.pricing.zones.lookup') }}";
  var zoneQuoteUrl = "{{ route('admin.pricing.zones.quote') }}";

  // Vehicle quote modal markup (created dynamically)
  function showVehicleQuoteModal(rows){
    // remove existing if present
    var existing = document.getElementById('vehicle-quote-modal');
    if (existing) existing.remove();

    var modal = document.createElement('div'); modal.id = 'vehicle-quote-modal';
    modal.style.position = 'fixed'; modal.style.left = '0'; modal.style.top = '0'; modal.style.right = '0'; modal.style.bottom = '0'; modal.style.background = 'rgba(0,0,0,0.5)'; modal.style.zIndex = 20000; modal.style.display = 'flex'; modal.style.alignItems = 'center'; modal.style.justifyContent = 'center';

    var box = document.createElement('div'); box.style.background = '#fff'; box.style.borderRadius = '8px'; box.style.width = '420px'; box.style.maxWidth = '90%'; box.style.padding = '16px'; box.style.boxShadow = '0 10px 30px rgba(0,0,0,0.2)';

    var title = document.createElement('h3'); title.textContent = 'Select vehicle'; title.style.margin = '0 0 8px 0'; box.appendChild(title);

    var list = document.createElement('div'); list.style.maxHeight = '320px'; list.style.overflow = 'auto'; list.style.marginBottom = '12px';

    Object.keys(rows).forEach(function(k){
      var price = rows[k];
      var label = k; // display label
      var displayName = k.replace('_price','');
      var human = (displayName === 'saloon') ? 'Saloon' : (displayName === 'business' ? 'Business' : (displayName === 'mpv6' ? 'MPV6' : (displayName === 'mpv8' ? 'MPV8' : displayName))); 
      var item = document.createElement('label'); item.style.display = 'flex'; item.style.alignItems = 'center'; item.style.justifyContent = 'space-between'; item.style.padding = '8px'; item.style.borderBottom = '1px solid #eee';
      var left = document.createElement('div');
      var radio = document.createElement('input'); radio.type = 'radio'; radio.name = 'vehicle_quote_radio'; radio.value = human; radio.dataset.price = (price !== null && price !== undefined) ? price : '';
      radio.style.marginRight = '8px';
      left.appendChild(radio);
      var span = document.createElement('span'); span.textContent = human; left.appendChild(span);
      var right = document.createElement('div'); right.textContent = (price !== null && price !== undefined) ? Number(price).toFixed(2) + ' ' : 'N/A';
      item.appendChild(left); item.appendChild(right); list.appendChild(item);

      radio.addEventListener('change', function(){
        // on select, set vehicle and pricing
        var sel = document.querySelector('select[name="vehicle_type"]'); if (sel) sel.value = this.value;
        var display = document.getElementById('selected-vehicle-name'); if (display) display.textContent = this.value;
        var val = this.dataset.price ? Number(this.dataset.price).toFixed(2) : '';
        var bookingInput = document.getElementById('booking-charges-input'); if (bookingInput) bookingInput.value = val;
        var zonePriceEl = document.getElementById('manual-zone-price'); if (zonePriceEl) zonePriceEl.textContent = val || '-';
        // also update the visible vehicle_type_text input if present
        var vt = document.querySelector('input[name="vehicle_type_text"]'); if (vt) vt.value = this.value;
        // Auto-fill the price/charge field
        var priceInput = document.querySelector('input[name="booking_charges"]'); 
        if (priceInput) priceInput.value = val;
        // close modal
        modal.remove();
      });
    });

    box.appendChild(list);
    var footer = document.createElement('div'); footer.style.textAlign = 'right';
    var cancel = document.createElement('button'); cancel.type='button'; cancel.textContent='Cancel'; cancel.className='px-3 py-1 border rounded mr-2'; cancel.addEventListener('click', function(){ modal.remove(); });
    footer.appendChild(cancel);
    box.appendChild(footer);

    modal.appendChild(box); document.body.appendChild(modal);
  }

  // Render vehicle pricing list under the map (replaces modal popup)
  function renderVehiclePricingList(json) {
    try {
      var container = document.getElementById('vehicle-pricing-list');
      if (!container) {
        container = document.createElement('div'); container.id = 'vehicle-pricing-list'; container.className = 'mt-4';
        var ref = document.getElementById('manual-distance');
        if (ref && ref.parentNode && ref.parentNode.parentNode) { ref.parentNode.parentNode.insertBefore(container, ref.parentNode.nextSibling); }
        else { document.getElementById('manual-map').parentNode.appendChild(container); }
      }

      container.style.display = 'block';
      container.innerHTML = '';

      var title = document.createElement('div'); title.className = 'text-sm font-semibold mb-2'; title.textContent = 'Pricing';
      container.appendChild(title);

      var list = document.createElement('div'); list.className = 'space-y-2';

      var priceList = [
        { key: 'saloon_price', label: 'Saloon' },
        { key: 'business_price', label: 'Business Class' },
        { key: 'mpv6_price', label: 'MPV6' },
        { key: 'mpv8_price', label: 'MPV8' }
      ];

      priceList.forEach(function(it){
        var priceRaw = (json.pricing && json.pricing[it.key] !== undefined && json.pricing[it.key] !== null) ? json.pricing[it.key] : null;
        var priceText = priceRaw !== null ? Number(priceRaw).toFixed(2) : 'N/A';
        var row = document.createElement('div'); row.className = 'p-2 border rounded flex justify-between items-center cursor-pointer hover:bg-gray-50';
        row.dataset.key = it.key; row.dataset.price = priceRaw !== null ? priceRaw : '';
        row.innerHTML = '<div>' + it.label + '</div><div class="font-medium">' + priceText + '</div>';
        row.addEventListener('click', function(){
          // mark selected
          var prev = container.querySelectorAll('.selected-price-row'); prev.forEach(function(el){ el.classList.remove('selected-price-row'); el.classList.remove('bg-indigo-50'); el.classList.remove('border-indigo-500'); });
          row.classList.add('selected-price-row'); row.classList.add('bg-indigo-50'); row.classList.add('border-indigo-500');

          var human = it.label;
          var sel = document.querySelector('select[name="vehicle_type"]'); var matched = false;
          if (sel) {
            for (var i=0;i<sel.options.length;i++){ var opt = sel.options[i]; if (opt.text === human || opt.value.toLowerCase().indexOf(human.replace(/\s+/g,'').toLowerCase()) !== -1) { sel.value = opt.value; matched = true; break; } }
          }
          var vt = document.querySelector('input[name="vehicle_type_text"]'); if (!matched && vt) vt.value = human; else if (matched && vt) vt.value = sel.options[sel.selectedIndex] ? sel.options[sel.selectedIndex].text : human;

          // fill booking charge and zone price
          var bookingInput = document.getElementById('booking-charges-input'); if (bookingInput) bookingInput.value = row.dataset.price ? Number(row.dataset.price).toFixed(2) : '';
          var zonePriceEl = document.getElementById('manual-zone-price'); if (zonePriceEl) zonePriceEl.textContent = bookingInput && bookingInput.value ? bookingInput.value : '-';
          
          // Auto-fill the price/charge field
          var priceInput = document.querySelector('input[name="booking_charges"]'); 
          if (priceInput) priceInput.value = row.dataset.price ? Number(row.dataset.price).toFixed(2) : '';

          // auto-fill highlighted address and vehicle type fields
          try {
            // copy address lines to top inputs (if present)
            var pickupLine = document.querySelector('input[name="pickup_address_line"]');
            var dropLine = document.querySelector('input[name="dropoff_address_line"]');
            var pickupTop = document.querySelector('input[name="pickup_address"]');
            var dropTop = document.querySelector('input[name="dropoff_address"]');
            if (pickupLine && pickupTop && pickupLine.value) pickupTop.value = pickupLine.value;
            if (dropLine && dropTop && dropLine.value) dropTop.value = dropLine.value;

            // also copy top to line if line is empty
            if (pickupTop && pickupLine && !pickupLine.value && pickupTop.value) pickupLine.value = pickupTop.value;
            if (dropTop && dropLine && !dropLine.value && dropTop.value) dropLine.value = dropTop.value;

            // update zone display if pricing provided by server
            if (json && json.pickup_zone) updateZoneDisplay('pickup', json.pickup_zone);
            if (json && json.dropoff_zone) updateZoneDisplay('dropoff', json.dropoff_zone);
          } catch(e){ console.error('auto-fill on vehicle select failed', e); }
        });

        list.appendChild(row);
      });

      container.appendChild(list);

      var src = document.createElement('div'); src.className = 'mt-2 text-xs text-gray-500';
      var srcText = 'Source: ';
      if (json.pricing_type === 'zone') srcText += 'Zones';
      else if (json.pricing_type === 'postcode') srcText += 'Postcode';
      else if (json.pricing_type === 'mileage') srcText += 'Mileage';
      else srcText += (json.pricing_type || '-');

      if (json.pricing_type === 'zone' && json.pickup_zone && json.dropoff_zone) srcText += ' (' + (json.pickup_zone.zone_name || '') + ' → ' + (json.dropoff_zone.zone_name || '') + ')';
      else if (json.pricing_type === 'postcode' && json.pickup_postcode && json.dropoff_postcode) srcText += ' (' + (json.pickup_postcode || '') + ' → ' + (json.dropoff_postcode || '') + ')';
      else if (json.pricing_type === 'mileage' && json.mileage) {
        var em = (json.mileage.end_mile === null || json.mileage.end_mile === undefined) ? '∞' : json.mileage.end_mile;
        srcText += ' (' + (json.mileage.start_mile || 0) + ' - ' + em + ' miles)';
      }

      src.textContent = srcText;
      container.appendChild(src);

      // Show airport charges if applied
      if (json.pricing && json.pricing.airport_charges && json.pricing.airport_charges > 0) {
        var chargesDiv = document.createElement('div'); 
        chargesDiv.className = 'mt-2 p-2 bg-blue-50 border border-blue-200 rounded text-xs';
        var chargesText = '<strong>Airport Charges Applied:</strong> £' + Number(json.pricing.airport_charges).toFixed(2);
        if (json.pricing.applied_charges && json.pricing.applied_charges.length > 0) {
          chargesText += '<br>';
          json.pricing.applied_charges.forEach(function(charge) {
            chargesText += '• ' + charge.type.charAt(0).toUpperCase() + charge.type.slice(1) + ' (' + charge.zone + '): £' + Number(charge.amount).toFixed(2) + '<br>';
          });
        }
        chargesDiv.innerHTML = chargesText;
        container.appendChild(chargesDiv);
      }

      // select the server-provided selected_price when present
      if (json.pricing && json.pricing.selected_price !== undefined && json.pricing.selected_price !== null) {
        var rows = container.querySelectorAll('[data-price]'); for (var r=0;r<rows.length;r++){ if (rows[r].dataset.price && Number(rows[r].dataset.price) == Number(json.pricing.selected_price)) { rows[r].click(); break; } }
      }

    } catch(e){ console.error('renderVehiclePricingList error', e); }
  }

  function getAllVehiclePrices(){
    // reuse quote endpoint without vehicle_type to get pricing object with all prices
    var plat = document.querySelector('input[name="pickup_lat"]').value;
    var plon = document.querySelector('input[name="pickup_lon"]').value;
    var dlat = document.querySelector('input[name="dropoff_lat"]').value;
    var dlon = document.querySelector('input[name="dropoff_lon"]').value;
    var pcode = document.querySelector('input[name="pickup_postcode"]') ? document.querySelector('input[name="pickup_postcode"]').value : null;
    var dcode = document.querySelector('input[name="dropoff_postcode"]') ? document.querySelector('input[name="dropoff_postcode"]').value : null;
    if (!plat || !plon || !dlat || !dlon) {
      // If user typed an address but didn't select from autocomplete, try geocoding the typed addresses
      var pAddr = document.querySelector('input[name="pickup_address_line"]') ? document.querySelector('input[name="pickup_address_line"]').value : document.querySelector('input[name="pickup_address"]').value;
      var dAddr = document.querySelector('input[name="dropoff_address_line"]') ? document.querySelector('input[name="dropoff_address_line"]').value : document.querySelector('input[name="dropoff_address"]').value;

      var needsPickupGeocode = (!plat || !plon) && pAddr;
      var needsDropGeocode = (!dlat || !dlon) && dAddr;

      if (!needsPickupGeocode && !needsDropGeocode) {
        alert('Please select both pickup and dropoff locations first');
        return;
      }

      // provide UI feedback
      var btnEl = document.getElementById('get-quote-btn');
      if (btnEl) { btnEl.disabled = true; btnEl.dataset.origText = btnEl.textContent; btnEl.textContent = 'Looking up...'; }
      console.log('Geocoding missing coordinates...', { pickup: needsPickupGeocode ? pAddr : null, dropoff: needsDropGeocode ? dAddr : null });

      var tasks = [];
      if (needsPickupGeocode) tasks.push(geocodeAddress(pAddr).then(function(res){ return { kind: 'pickup', res: res }; }).catch(function(err){ return { kind: 'pickup', err: err }; }));
      else tasks.push(Promise.resolve({ kind: 'pickup', res: { lat: plat, lng: plon } }));
      if (needsDropGeocode) tasks.push(geocodeAddress(dAddr).then(function(res){ return { kind: 'dropoff', res: res }; }).catch(function(err){ return { kind: 'dropoff', err: err }; }));
      else tasks.push(Promise.resolve({ kind: 'dropoff', res: { lat: dlat, lng: dlon } }));

      Promise.all(tasks).then(function(results){
        var failed = false;
        results.forEach(function(r){
          if (r.err) {
            console.warn('Geocode failed for', r.kind, r.err);
            failed = true;
            return;
          }
          if (r.kind === 'pickup' && r.res && r.res.lat && r.res.lng){
            document.querySelector('input[name="pickup_lat"]').value = r.res.lat;
            document.querySelector('input[name="pickup_lon"]').value = r.res.lng;
            if (r.res.postcode) document.querySelector('input[name="pickup_postcode"]').value = r.res.postcode.trim().toUpperCase();
            setMarker('pickup', r.res.lat, r.res.lng);
          }
          if (r.kind === 'dropoff' && r.res && r.res.lat && r.res.lng){
            document.querySelector('input[name="dropoff_lat"]').value = r.res.lat;
            document.querySelector('input[name="dropoff_lon"]').value = r.res.lng;
            if (r.res.postcode) document.querySelector('input[name="dropoff_postcode"]').value = r.res.postcode.trim().toUpperCase();
            setMarker('dropoff', r.res.lat, r.res.lng);
          }
        });

        if (btnEl) { btnEl.disabled = false; btnEl.textContent = btnEl.dataset.origText || 'Get quote'; }

        if (failed) {
          alert('Failed to resolve one or more addresses. Please select the correct address from the autocomplete suggestions.');
          return;
        }

        // Re-run quoting now that coordinates are filled
        getAllVehiclePrices();
      }).catch(function(err){
        if (btnEl) { btnEl.disabled = false; btnEl.textContent = btnEl.dataset.origText || 'Get quote'; }
        console.error('Geocoding error', err); alert('Failed to lookup addresses for quoting. Please try selecting addresses from suggestions.');
      });

      return;
    }
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // include client-side driving distance (if available) so server can use the same distance when matching mileage ranges
    var distEl = document.getElementById('manual-distance');
    var distVal = null;
    try { distVal = distEl && distEl.textContent ? parseFloat(distEl.textContent) : null; if (isNaN(distVal)) distVal = null; } catch(e){ distVal = null; }
    fetch(zoneQuoteUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: JSON.stringify({ pickup_lat: plat, pickup_lon: plon, dropoff_lat: dlat, dropoff_lon: dlon, pickup_postcode: pcode, dropoff_postcode: dcode, distance_miles: distVal }) })
    .then(function(res){ return res.json(); })
    .then(function(json){
      // Log pricing calculation details to console
      if (json && json.success) {
        console.log('%c=== PRICE CALCULATION RESULT ===', 'color: #2563eb; font-weight: bold; font-size: 14px;');
        console.log('📍 Pickup Location:', { lat: plat, lon: plon, postcode: pcode || 'N/A' });
        console.log('📍 Dropoff Location:', { lat: dlat, lon: dlon, postcode: dcode || 'N/A' });
        
        if (json.pickup_zone) {
          console.log('🏢 Pickup Zone Detected:', json.pickup_zone.zone_name);
        } else {
          console.log('❌ No pickup zone detected');
        }
        
        if (json.dropoff_zone) {
          console.log('🏢 Dropoff Zone Detected:', json.dropoff_zone.zone_name);
        } else {
          console.log('❌ No dropoff zone detected');
        }
        
        console.log('💰 Pricing Type:', json.pricing_type);
        
        if (json.pricing) {
          console.log('💵 Base Prices:', {
            Saloon: '£' + (json.pricing.base_saloon_price || json.pricing.saloon_price),
            Business: '£' + (json.pricing.base_business_price || json.pricing.business_price),
            MPV6: '£' + (json.pricing.base_mpv6_price || json.pricing.mpv6_price),
            MPV8: '£' + (json.pricing.base_mpv8_price || json.pricing.mpv8_price)
          });
          
          if (json.pricing.airport_charges && json.pricing.airport_charges > 0) {
            console.log('%c✈️ Airport Charges Applied: £' + json.pricing.airport_charges, 'color: #059669; font-weight: bold;');
            if (json.pricing.applied_charges && json.pricing.applied_charges.length > 0) {
              json.pricing.applied_charges.forEach(function(charge) {
                console.log('  • ' + charge.type + ' (' + charge.zone + '): £' + charge.amount);
              });
            }
          }
          
          console.log('%c🎯 Final Prices (with airport charges):', 'color: #7c3aed; font-weight: bold;');
          console.log({
            Saloon: '£' + json.pricing.saloon_price,
            Business: '£' + json.pricing.business_price,
            MPV6: '£' + json.pricing.mpv6_price,
            MPV8: '£' + json.pricing.mpv8_price
          });
        }
        
        console.log('%c=================================', 'color: #2563eb; font-weight: bold;');
      }
      
      if (json && json.success && json.pricing) {
        renderVehiclePricingList(json);
      } else {
        // if server says matching mileage exists but inactive, show informative message in pricing panel
        if (json && json.matching_mileage) {
          var container = document.getElementById('vehicle-pricing-list');
          if (!container) {
            container = document.createElement('div'); container.id = 'vehicle-pricing-list'; container.className = 'mt-4';
            var ref = document.getElementById('manual-distance'); if (ref && ref.parentNode && ref.parentNode.parentNode) ref.parentNode.parentNode.insertBefore(container, ref.parentNode.nextSibling);
            else document.getElementById('manual-map').parentNode.appendChild(container);
          }
          container.style.display = 'block'; container.innerHTML = '';
          var msg = document.createElement('div'); msg.className = 'p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-900 text-sm rounded';
          var rangeText = (json.matching_mileage.end_mile === null || json.matching_mileage.end_mile === undefined) ? json.matching_mileage.start_mile + ' - ∞' : json.matching_mileage.start_mile + ' - ' + json.matching_mileage.end_mile;
          msg.innerHTML = '<strong>Pricing unavailable:</strong> a matching mileage rule exists (' + rangeText + ' miles) but it is currently inactive. Please enable it in Admin → Pricing → Mileage or create an active range.';
          container.appendChild(msg);
        } else {
          alert(json.message || 'No pricing available');
        }
      }
    }).catch(function(err){ console.error('getAllVehiclePrices error', err); alert('Failed to get quotes'); });
  }

  function attachGetQuoteHandler(){
    try {
      var btn = document.getElementById('get-quote-btn');
      if (!btn) return;
      if (btn.dataset.quoteBound === '1') return; // already bound
      btn.addEventListener('click', function(e){
        try { console.log('Get quote button clicked'); } catch(e){}
        // brief UI feedback
        btn.disabled = true; var orig = btn.textContent; btn.textContent = 'Getting...';
        Promise.resolve().then(function(){
          try { getAllVehiclePrices(); } catch(err){ console.error('getAllVehiclePrices error', err); }
        }).finally(function(){ setTimeout(function(){ try { btn.disabled = false; btn.textContent = orig; } catch(e){} }, 500); });
      });
      btn.dataset.quoteBound = '1';
      console.log('Get quote handler attached');
    } catch(e){ console.error('attachGetQuoteHandler failed', e); }
  }

  // ensure handler is attached on DOM ready
  document.addEventListener('DOMContentLoaded', function(){ attachGetQuoteHandler(); });

  function updateZoneDisplay(kind, zone){
    try {
      var el = document.getElementById(kind + '-zone');
      if (!el) return;
      el.innerHTML = 'Zone: <strong>' + (zone && zone.zone_name ? zone.zone_name : '-') + '</strong>';
    } catch(e){ console.error('updateZoneDisplay error', e); }
  }

  function lookupZone(kind, lat, lon){
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    fetch(zoneLookupUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: JSON.stringify({ lat: lat, lon: lon }) })
    .then(function(res){ return res.json(); })
    .then(function(json){ if (json && json.success && json.zone) { updateZoneDisplay(kind, json.zone); } else { updateZoneDisplay(kind, null); } 
      // try to quote when both zones available
      try { var pzoneText = document.getElementById('pickup-zone') && document.getElementById('pickup-zone').textContent.indexOf('-') === -1; var dzoneText = document.getElementById('dropoff-zone') && document.getElementById('dropoff-zone').textContent.indexOf('-') === -1; if (pzoneText && dzoneText) { quoteRoute(); } } catch(e){}
    }).catch(function(err){ console.error('lookupZone error', err); updateZoneDisplay(kind, null); });
  }

  function quoteRoute(){
    var plat = document.querySelector('input[name="pickup_lat"]').value;
    var plon = document.querySelector('input[name="pickup_lon"]').value;
    var dlat = document.querySelector('input[name="dropoff_lat"]').value;
    var dlon = document.querySelector('input[name="dropoff_lon"]').value;
    // prefer the select vehicle type if present, otherwise fall back to free-text vehicle input
    var vsel = document.querySelector('select[name="vehicle_type"]');
    var vtxt = document.querySelector('input[name="vehicle_type_text"]');
    var v = null;
    if (vsel && vsel.value) v = vsel.value;
    else if (vtxt && vtxt.value) v = vtxt.value;

    if (!plat || !plon || !dlat || !dlon) return;
    var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    var pcode = document.querySelector('input[name="pickup_postcode"]') ? document.querySelector('input[name="pickup_postcode"]').value : null;
    var dcode = document.querySelector('input[name="dropoff_postcode"]') ? document.querySelector('input[name="dropoff_postcode"]').value : null;
    // include client-side driving distance (if available) so server can use the same distance when matching mileage ranges
    var distEl2 = document.getElementById('manual-distance');
    var distVal2 = null;
    try { distVal2 = distEl2 && distEl2.textContent ? parseFloat(distEl2.textContent) : null; if (isNaN(distVal2)) distVal2 = null; } catch(e){ distVal2 = null; }
    fetch(zoneQuoteUrl, { method: 'POST', credentials: 'same-origin', headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: JSON.stringify({ pickup_lat: plat, pickup_lon: plon, dropoff_lat: dlat, dropoff_lon: dlon, vehicle_type: v, pickup_postcode: pcode, dropoff_postcode: dcode, distance_miles: distVal2 }) })
    .then(function(res){ return res.json(); })
    .then(function(json){ if (json && json.success && json.pricing) { var sel = json.pricing.selected_price !== null ? json.pricing.selected_price : null; var zonePriceEl = document.getElementById('manual-zone-price'); if (zonePriceEl) zonePriceEl.textContent = sel !== null ? sel : '-'; var bookingInput = document.getElementById('booking-charges-input'); if (bookingInput) { if (sel !== null) bookingInput.value = Number(sel).toFixed(2); else bookingInput.value = ''; } renderVehiclePricingList(json); } else { var zonePriceEl2 = document.getElementById('manual-zone-price'); if (zonePriceEl2) zonePriceEl2.textContent = '-'; var bookingInput2 = document.getElementById('booking-charges-input'); if (bookingInput2) bookingInput2.value = ''; var list = document.getElementById('vehicle-pricing-list'); if (list) { list.style.display = 'none'; list.innerHTML = ''; } } })
    .catch(function(err){ console.error('quoteRoute error', err); var zonePriceEl3 = document.getElementById('manual-zone-price'); if (zonePriceEl3) zonePriceEl3.textContent = '-'; var bookingInput3 = document.getElementById('booking-charges-input'); if (bookingInput3) bookingInput3.value = ''; var list2 = document.getElementById('vehicle-pricing-list'); if (list2) { list2.style.display = 'none'; list2.innerHTML = ''; } });
  }

  // allow vehicle type changes to trigger quoting (debounced for text input)
  function debounce(fn, delay){ var t; return function(){ var args = arguments; clearTimeout(t); t = setTimeout(function(){ fn.apply(null, args); }, delay); }; }

  var vehicleSelect = document.querySelector('select[name="vehicle_type"]');
  if (vehicleSelect) {
    vehicleSelect.addEventListener('change', function(){ try { quoteRoute(); } catch(e){ console.error('vehicle change quote error', e); } });
  }
  var vehicleText = document.querySelector('input[name="vehicle_type_text"]');
  if (vehicleText) {
    vehicleText.addEventListener('input', debounce(function(){ try { quoteRoute(); } catch(e){ console.error('vehicle text quote error', e); } }, 300));
  }

  // If coordinates present on load and a vehicle is selected, auto-run quote once
  try {
    var _plat = document.querySelector('input[name="pickup_lat"]').value;
    var _plon = document.querySelector('input[name="pickup_lon"]').value;
    var _dlat = document.querySelector('input[name="dropoff_lat"]').value;
    var _dlon = document.querySelector('input[name="dropoff_lon"]').value;
    if (_plat && _plon && _dlat && _dlon) {
      // small timeout to allow page resources to settle
      setTimeout(function(){ try { quoteRoute(); } catch(e){} }, 250);
    }
  } catch(e){}

  // patch: ensure autocomplete attach calls perform lookup after place selected by adding observer to hidden inputs
  // the attachAutocompleteTo callbacks already set the hidden lat/lon and call setMarker.
  // We intercept setMarker by wrapping the existing local function (or install a handler) to also perform lookup and attach the get-quote handler.
  (function(){
    var _origLocal = (typeof setMarker === 'function') ? setMarker : null;
    if (typeof setMarker === 'function') {
      setMarker = function(kind, lat, lon){
        try { _origLocal && _origLocal(kind, lat, lon); } catch(e){ console.error('wrapped setMarker orig failed', e); }
        try { lookupZone(kind, lat, lon); } catch(e){ console.error('wrapped lookupZone failed', e); }
        try { attachGetQuoteHandler(); } catch(e){}
      };
    } else {
      // fallback: expose a global stub that at least performs lookup
      window.setMarker = function(kind, lat, lon){ try { lookupZone(kind, lat, lon); } catch(e){} try { attachGetQuoteHandler(); } catch(e){} };
    }
  })();

  // If Google is ready, init; otherwise the API script triggers `initMaps` callback which calls initPlaces below
  if (window.google && google.maps && google.maps.places) { console.log('Google Maps API available — initializing google map and places'); initGoogleMap(); initPlaces(); }
  window.initMaps = function(){ console.log('Google Maps API loaded'); initGoogleMap(); initPlaces(); attachGetQuoteHandler(); };

  // Initialize Google map and Directions rendering
  function initGoogleMap(){
    try {
      var gmDiv = document.getElementById('manual-google-map');
      var lfDiv = document.getElementById('manual-map');
      if (!gmDiv) return;
      // show google map and hide leaflet
      gmDiv.style.display = 'block';
      if (lfDiv) lfDiv.style.display = 'none';

      // create map
      window.googleMap = new google.maps.Map(gmDiv, { center: {lat:51.5074, lng:-0.1278}, zoom: 12, mapTypeControl: true });
      window.directionsService = new google.maps.DirectionsService();
      window.directionsRenderer = new google.maps.DirectionsRenderer({ map: window.googleMap, suppressMarkers: true, preserveViewport: false });
      window.googleMarkers = { pickup: null, dropoff: null };
      window.googleRoute = null;
      console.log('Google map initialized');

      // helper to set Google markers (labels A/B)
      window.setGoogleMarker = function(kind, lat, lon){
        if (!window.google || !window.google.maps) return;
        var pos = {lat: parseFloat(lat), lng: parseFloat(lon)};
        var label = (kind === 'pickup') ? 'A' : 'B';
        if (window.googleMarkers[kind]) { window.googleMarkers[kind].setMap(null); window.googleMarkers[kind] = null; }
        window.googleMarkers[kind] = new google.maps.Marker({ position: pos, map: window.googleMap, label: { text: label, color: 'white', fontWeight: 'bold' }, title: kind === 'pickup' ? 'Pickup' : 'Dropoff' });
      };

      window.computeRouteClient = function(){
        var plat = parseFloat(document.querySelector('input[name="pickup_lat"]').value || '');
        var plon = parseFloat(document.querySelector('input[name="pickup_lon"]').value || '');
        var dlat = parseFloat(document.querySelector('input[name="dropoff_lat"]').value || '');
        var dlon = parseFloat(document.querySelector('input[name="dropoff_lon"]').value || '');
        if (isNaN(plat) || isNaN(plon) || isNaN(dlat) || isNaN(dlon)) return;

        var req = {
          origin: { lat: plat, lng: plon },
          destination: { lat: dlat, lng: dlon },
          travelMode: google.maps.TravelMode.DRIVING
        };
        window.directionsService.route(req, function(result, status){
          if (status === 'OK'){
            window.directionsRenderer.setDirections(result);
            try {
              var leg = result.routes[0].legs[0];
              document.getElementById('manual-distance').textContent = (leg.distance && leg.distance.value) ? ((leg.distance.value/1000)*0.621371).toFixed(2) : '-';
              document.getElementById('manual-time').textContent = (leg.duration && leg.duration.value) ? Math.round(leg.duration.value/60) + ' min' : '-';
            } catch(e){ console.warn('Failed to set distance/time from directions', e); }

            // place labeled markers
            var start = result.routes[0].legs[0].start_location;
            var end = result.routes[0].legs[0].end_location;
            window.setGoogleMarker('pickup', start.lat(), start.lng());
            window.setGoogleMarker('dropoff', end.lat(), end.lng());
          } else {
            console.warn('DirectionsService failed, falling back to server proxy', status);
            fetchRoute();
          }
        });
      };

    } catch(e){ console.error('initGoogleMap error', e); }
  };

  // Debug helper: log script src and errors to help diagnose failures
  (function(){
    try {
      var scriptEl = document.querySelector('script[src*="maps.googleapis.com"]');
      console.log('Detected Google Maps script tag:', scriptEl ? scriptEl.src : 'none');
      console.log('Google object present at load check:', !!window.google, 'google.maps present:', !!(window.google && window.google.maps));

      if (scriptEl) {
        scriptEl.addEventListener('load', function(){ console.log('maps.googleapis.com script loaded event'); });
        scriptEl.addEventListener('error', function(){ console.error('maps.googleapis.com script failed to load (network or blocked)'); });
      }

      window.addEventListener('error', function(e){
        try {
          if (e && e.filename && e.filename.indexOf('maps.googleapis.com') !== -1) {
            console.error('Global error from maps.googleapis.com script:', e.message, e);
          }
        } catch (ex) { /* ignore */ }
      });
    } catch(e){ console.error('Debug init failed', e); }

    // Diagnostic helper: if Google Maps/Places fail to load, show a visible banner with guidance
    function showGoogleError(msg){
      console.error(msg);
      var form = document.getElementById('manual-booking-form');
      if (form) {
        var existing = document.getElementById('google-maps-error');
        if (existing) return;
        var el = document.createElement('div');
        el.id = 'google-maps-error';
        el.className = 'mt-4 p-3 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-900 text-sm rounded';
        el.innerHTML = '<strong>Google Maps failed to load.</strong> Check the API key, enabled APIs (Maps JavaScript, Places, Directions), and referer restrictions (include http://localhost if developing locally). Open the browser console for specific error messages.';
        form.parentNode.insertBefore(el, form.nextSibling);
      }
    }

    setTimeout(function(){
      if (!window.google || !google.maps || !google.maps.places) {
        showGoogleError('Google Maps JS or Places library is not available on this page after load timeout.');
      }
    }, 2500);
  })();

  // add hidden fields for lat/lon and postcodes (if not present)
  var form = document.getElementById('manual-booking-form');
  if (form && !document.querySelector('input[name="pickup_lat"]')){
    var extra = document.createElement('div'); extra.style.display='none'; extra.innerHTML = '<input type="hidden" name="pickup_lat" /><input type="hidden" name="pickup_lon" /><input type="hidden" name="dropoff_lat" /><input type="hidden" name="dropoff_lon" /><input type="hidden" name="pickup_postcode" /><input type="hidden" name="dropoff_postcode" />'; form.appendChild(extra);
  }

  // wire up legacy createAutocomplete calls to stay compatible (no-op now)
  function createAutocomplete(a,b,c) { return; }

  // Booking search + autofill
  (function(){
    var input = document.getElementById('booking-search');
    var resultsBox = document.getElementById('booking-search-results');
    var timer = null;
    function renderResults(items){
      if (!items || !items.length) { resultsBox.style.display='none'; resultsBox.innerHTML=''; return; }
      resultsBox.innerHTML = items.map(function(it){
        var label = (it.booking_code ? ('['+it.booking_code+'] ') : '') + (it.passenger_name || '') + (it.phone ? ' — '+it.phone : '');
        return '<div class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-id="'+it.id+'" data-json="'+encodeURIComponent(JSON.stringify(it))+'">'+label+'</div>';
      }).join('');
      resultsBox.style.display = 'block';
    }

    function doSearch(q){
      if (!q || q.length < 2) { renderResults([]); return; }
      fetch('{{ route('admin.bookings.search') }}?q=' + encodeURIComponent(q), { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
        .then(function(r){ return r.json(); })
        .then(function(json){ if (json && json.results) renderResults(json.results); else renderResults([]); })
        .catch(function(e){ console.error('Booking search failed', e); renderResults([]); });
    }

    if (input && resultsBox) {
      input.addEventListener('input', function(){ clearTimeout(timer); timer = setTimeout(function(){ doSearch(input.value.trim()); }, 250); });
      input.addEventListener('keydown', function(e){ if (e.key === 'Escape') { resultsBox.style.display='none'; } });

      resultsBox.addEventListener('click', function(e){ var el = e.target.closest('[data-json]'); if (!el) return; var data = JSON.parse(decodeURIComponent(el.getAttribute('data-json'))); // autofill fields
        try {
          var set = function(sel, val){ var el = document.querySelector('[name="'+sel+'"]'); if (el) el.value = (val === null || val === undefined) ? '' : val; };
          set('pickup_address_line', (data.meta && data.meta.pickup_address) ? data.meta.pickup_address : (data.pickup_address_line || ''));
          set('dropoff_address_line', (data.meta && data.meta.dropoff_address) ? data.meta.dropoff_address : (data.dropoff_address_line || ''));
          set('passenger_name', data.passenger_name || '');
          set('phone', data.phone || '');
          set('email', data.email || '');
          set('vehicle_type', data.vehicle_type || '');
          set('flight_number', data.flight_number || '');
          set('pickup_date', data.pickup_date ? (data.pickup_date.split(' ')[0]) : '');
          set('pickup_time', data.pickup_time || '');
          // baby seat
          var babyCheckbox = document.querySelector('input[type="checkbox"][name="baby_seat"]');
          if (babyCheckbox && (data.baby_seat || data.baby_seat == 1 || data.baby_seat === true)) { babyCheckbox.checked = true; } else if (babyCheckbox) { babyCheckbox.checked = false; }
          // set age select
          var ageSel = document.querySelector('select[name="baby_seat_age"]'); if (ageSel) { ageSel.value = (data.baby_seat_age || (data.meta && data.meta.baby_seat_age) || ''); }

          // hide results and show a toast
          resultsBox.style.display='none'; input.value = '';
          if (typeof window.showToast === 'function') window.showToast('Form filled from booking: ' + (data.booking_code || data.passenger_name || ''));
        } catch(e) { console.error('Autofill failed', e); }
      });

      // click outside to dismiss
      document.addEventListener('click', function(e){ if (!e.target.closest('#booking-search-results') && e.target !== input) resultsBox.style.display='none'; });
    }
  })();

  // form submit via ajax (preserve existing behavior)
  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      // Ensure booking_charges has a value before submit (fallback to manual zone price if user didn't explicitly choose)
      try {
        var bookingInput = form.querySelector('input[name="booking_charges"]');
        var zonePriceEl = document.getElementById('manual-zone-price');
        if (bookingInput && (!bookingInput.value || bookingInput.value === '')) {
          var zp = zonePriceEl ? zonePriceEl.textContent.trim() : '';
          if (zp && !isNaN(parseFloat(zp))) {
            bookingInput.value = parseFloat(zp).toFixed(2);
          }
        }
        console.debug('Submitting manual booking with booking_charges=', bookingInput ? bookingInput.value : '(none)');
      } catch(e){ console.error('Pre-submit booking_charges check failed', e); }

      var fd = new FormData(form);
      var token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      fetch(form.getAttribute('action'), { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': token, 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }, credentials: 'same-origin' })
      .then(function(res){
        return res.text().then(function(txt){
          var parsed = null; try { parsed = txt && txt.length ? JSON.parse(txt) : null; } catch(e) { parsed = null; }
          if (res.status === 201) {
            return parsed || { success: true, message: 'Created' };
          }
          // For non-201 responses, throw a structured error object
          if (parsed) throw { status: res.status, body: parsed };
          throw { status: res.status, body: { message: txt } };
        });
      })
      .then(function(json){ showToast('Booking created');
        try {
          var id = json && json.booking && json.booking.id ? json.booking.id : null;
          // explicitly activate the 'new' tab
          var newTab = document.querySelector('ul[role="tablist"] [data-tab="new"]');
          if (newTab) newTab.click();

          // Refresh the 'new' section via AJAX and insert updated list without reloading
          var listUrl = '{{ route('admin.bookings.index') }}?partial=1&section=new';
          fetch(listUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' }).then(function(r){ return r.text(); }).then(function(html){
            var container = document.getElementById('booking-new-container');
            if (container) {
              container.innerHTML = html;
              // run any injected scripts and attach handlers
              if (typeof window.runInjectedScripts === 'function') window.runInjectedScripts(container);
              if (window.attachPagination) window.attachPagination(container);
              if (window.attachBookingViewButtons) window.attachBookingViewButtons(container);

              // highlight newly created row if present
              if (id) {
                var row = container.querySelector('[data-booking-id="' + id + '"]');
                if (row) {
                  row.classList.add('bg-yellow-50');
                  setTimeout(function(){ row.classList.remove('bg-yellow-50'); }, 2200);
                }

                // Update 'new' tab badge(s)
                try {
                  document.querySelectorAll('[data-count-for="new"]').forEach(function(b){
                    var n = parseInt(b.textContent.trim()) || 0; b.textContent = n + 1;
                    // remove inactive styles to highlight if needed
                    b.classList.remove('bg-gray-100','text-gray-700');
                  });
                } catch(e){ console.error('Failed to update new badge', e); }

              }
            } else {
              // fallback: reload page if container not present
              setTimeout(function(){ location.reload(); }, 400);
            }
          }).catch(function(err){ console.error('Failed to refresh bookings list', err); setTimeout(function(){ location.reload(); }, 600); });
        } catch(e) { console.error('Post-create handler failed', e); setTimeout(function(){ location.reload(); }, 600); }
      })
      .catch(function(err){
        console.error('Booking create failed', err);
        // Handle validation errors
        if (err && err.body && err.body.errors) {
          var errors = err.body.errors;
          var msgs = Object.keys(errors).map(function(k){ return errors[k][0]; }).join('\n');
          alert(msgs);
          return;
        }
        // Show a server-provided message if available
        if (err && err.body && err.body.message) {
          alert(err.body.message + (err.body.error ? '\n' + err.body.error : ''));
          return;
        }
        // Handle common status codes with friendly messages
        if (err && err.status) {
          if (err.status === 401) { alert('Authentication required. Please login.'); return; }
          if (err.status === 403) { alert('Permission denied. You do not have permission to create bookings.'); return; }
        }
        alert('Failed to create booking');
      });
    });
  }
})();
</script>

<script>
(function(){
  function initBabySeatToggle(root){
    root = root || document;
    // target the checkbox specifically (there's also a hidden input of same name)
    var toggle = root.querySelector('input[type="checkbox"][name="baby_seat"]');
    var wrapper = root.getElementById ? root.getElementById('baby_seat_age_wrapper') : (root.querySelector && root.querySelector('#baby_seat_age_wrapper'));
    if (!toggle || !wrapper) return false;

    // Ensure wrapper exists in layout (override any inline display:none so we can hide via visibility without reflow)
    try { wrapper.style.display = 'flex'; wrapper.style.minWidth = wrapper.style.minWidth || '220px'; } catch(e){}

    function update(){
      try {
        var checked = !!toggle.checked;
        if (checked){
          wrapper.classList.remove('hidden');
          wrapper.style.visibility = 'visible';
          wrapper.style.opacity = '1';
          wrapper.style.pointerEvents = 'auto';
          wrapper.style.display = 'flex';
        } else {
          // keep in flow but hide visually to avoid reflow shifting the checkbox
          wrapper.classList.add('hidden');
          wrapper.style.visibility = 'hidden';
          wrapper.style.opacity = '0';
          wrapper.style.pointerEvents = 'none';
          // keep display:flex so space is reserved
          wrapper.style.display = 'flex';
          var sel = wrapper.querySelector('select[name="baby_seat_age"]'); if (sel) sel.value = '';
        }
      } catch(e){ console.warn('baby seat update failed', e); }
    }

    // run once to set initial state
    update();

    // attach handlers (avoid duplicates)
    toggle.removeEventListener('change', update);
    toggle.addEventListener('change', update);

    toggle.removeEventListener('click', update);
    toggle.addEventListener('click', function(){ setTimeout(update, 0); });

    toggle.removeEventListener('keyup', update);
    toggle.addEventListener('keyup', function(e){ if (e.key === ' ' || e.key === 'Spacebar' || e.key === 'Enter') setTimeout(update, 0); });

    // label click fallback
    try { var lab = toggle.closest('label'); if (lab) { lab.removeEventListener('click', update); lab.addEventListener('click', function(){ setTimeout(update, 0); }); } } catch(e){}

    return true;
  }

  // run immediately in case partials are already present
  initBabySeatToggle(document);

  // also run on DOMContentLoaded for regular full page loads
  document.addEventListener('DOMContentLoaded', function(){ initBabySeatToggle(document); });

  // observe DOM for dynamically injected content (AJAX partials) and initialize when found
  try {
    var mo = new MutationObserver(function(muts){
      muts.forEach(function(m){
        if (!m.addedNodes) return;
        m.addedNodes.forEach(function(node){
          if (node.nodeType !== 1) return;
          // if node contains the checkbox toggle or wrapper, init within that node
          if (node.querySelector && (node.querySelector('input[type="checkbox"][name="baby_seat"]') || node.querySelector('#baby_seat_age_wrapper'))) {
            initBabySeatToggle(node);
          }
        });
      });
    });
    mo.observe(document.body || document.documentElement, { childList: true, subtree: true });
  } catch(e){ /* MutationObserver not supported, no-op */ }
})();
</script>