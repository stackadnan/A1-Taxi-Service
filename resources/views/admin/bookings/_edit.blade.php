<form id="booking-edit-form" method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="space-y-4">
  @csrf
  @method('PUT')
  @php
    $activeSection = (string) request()->query('section', '');
    $isReadOnly = request()->boolean('readonly');
    $isConfirmedTab = $activeSection === 'confirmed';
    $isCancelledTab = $activeSection === 'cancelled';
    $isCompletedTab = $activeSection === 'completed';
    $changeLogs = is_array($booking->meta['change_logs'] ?? null) ? $booking->meta['change_logs'] : [];
  @endphp
  <input type="hidden" name="readonly_assign_mode" value="{{ $isReadOnly ? 1 : 0 }}">

  <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="lg:col-span-3 space-y-4">
      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Booking ID</label>
          <input type="text" value="{{ $booking->booking_code }}" class="mt-1 block w-full border rounded p-2 bg-gray-50" readonly>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Payment Type</label>
          @php
            $currentPaymentType = old('payment_type', $booking->payment_type ?? ($booking->meta['payment_type'] ?? ''));
            $paymentTypeOptions = ['cash' => 'Cash', 'card' => 'Card'];
          @endphp
          <select name="payment_type" class="mt-1 block w-full border rounded p-2">
            <!-- <option value="">(Auto)</option> -->
            @foreach($paymentTypeOptions as $paymentValue => $paymentLabel)
              <option value="{{ $paymentValue }}" {{ (string)$currentPaymentType === (string)$paymentValue ? 'selected' : '' }}>{{ $paymentLabel }}</option>
            @endforeach
            @if($currentPaymentType && !array_key_exists((string)$currentPaymentType, $paymentTypeOptions))
              <option value="{{ $currentPaymentType }}" selected>{{ ucfirst($currentPaymentType) }}</option>
            @endif
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Payment Id</label>
          <input type="text" name="payment_id" value="{{ old('payment_id', $booking->payment_id) }}" class="mt-1 block w-full border rounded p-2" placeholder="Payment id">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Booking Price £</label>
          <div class="input-with-icon">
            <input id="booking-charges-input" type="number" step="0.01" name="booking_charges" value="{{ old('booking_charges', $booking->total_price) }}" class="mt-1 block w-full border rounded p-2 form-input form-input-with-icon" placeholder="0.00" />
          </div>
        </div>
        <!-- <div>
          <label class="block text-sm font-medium text-gray-700">Source</label>
          <div class="mt-1 flex gap-2">
            <input type="text" name="source" value="{{ old('source', $booking->source_url ?? ($booking->meta['source'] ?? '')) }}" class="block w-full border rounded p-2" placeholder="Source URL">
            @if(!$isReadOnly)
              <button type="submit" id="source-update-btn" class="px-3 py-2 text-white rounded text-sm whitespace-nowrap" style="background-color: #1E293B;">Update</button>
            @endif
          </div>
        </div> -->
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Name</label>
          <input type="text" name="passenger_name" value="{{ old('passenger_name',$booking->passenger_name) }}" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" value="{{ old('email',$booking->email) }}" class="mt-1 block w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Phone</label>
          <input type="text" name="phone" value="{{ old('phone',$booking->phone) }}" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Alt Phone</label>
          <input type="text" name="alternate_phone" value="{{ old('alternate_phone', $booking->alternate_phone) }}" class="mt-1 block w-full border rounded p-2">
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Pickup address</label>
        <input type="text" name="pickup_address" value="{{ old('pickup_address', $booking->pickup_address) }}" class="mt-1 block w-full border rounded p-2">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Dropoff address</label>
        <input type="text" name="dropoff_address" value="{{ old('dropoff_address', $booking->dropoff_address) }}" class="mt-1 block w-full border rounded p-2">
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Pickup date</label>
          <input type="date" name="pickup_date" value="{{ old('pickup_date', optional($booking->pickup_date)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Pickup time</label>
          <input type="time" name="pickup_time" value="{{ old('pickup_time', $booking->pickup_time) }}" class="mt-1 block w-full border rounded p-2" required>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">No of Passengers</label>
          <input type="number" min="1" name="passengers" value="{{ old('passengers', $booking->passengers_count) }}" class="mt-1 block w-full border rounded p-2" placeholder="Passengers">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Luggage</label>
          <input type="text" name="luggage" value="{{ old('luggage', $booking->luggage_count) }}" class="mt-1 block w-full border rounded p-2" placeholder="Luggage">
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Flight Number</label>
          <input type="text" name="flight_number" value="{{ old('flight_number', $booking->flight_number) }}" class="mt-1 block w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Flight Time</label>
          <input type="time" name="flight_time" value="{{ old('flight_time', $booking->meta['flight_time'] ?? $booking->flight_arrival_time) }}" class="mt-1 block w-full border rounded p-2">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Meet &amp; Greet</label>
          <select name="meet_and_greet" class="mt-1 block w-full border rounded p-2">
            <option value="">(Auto)</option>
            <option value="1" {{ (string)old('meet_and_greet', $booking->meet_and_greet ? '1' : '0') === '1' ? 'selected' : '' }}>Yes</option>
            <option value="0" {{ (string)old('meet_and_greet', $booking->meet_and_greet ? '1' : '0') === '0' ? 'selected' : '' }}>No</option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Baby seat</label>
          <input id="baby_seat_hidden" type="hidden" name="baby_seat" value="0">
          <select id="baby_seat_select" name="baby_seat_age" class="mt-1 block w-full border rounded p-2">
            <option value="">No seat</option>
            <option value="0-1" {{ (old('baby_seat_age', (old('baby_seat', $booking->baby_seat) ? $booking->baby_seat_age : '')) == '0-1') ? 'selected' : '' }}>0 to 1 Years</option>
            <option value="1-3" {{ (old('baby_seat_age', (old('baby_seat', $booking->baby_seat) ? $booking->baby_seat_age : '')) == '1-3') ? 'selected' : '' }}>1 to 3 Years</option>
            <option value="4-7" {{ (old('baby_seat_age', (old('baby_seat', $booking->baby_seat) ? $booking->baby_seat_age : '')) == '4-7') ? 'selected' : '' }}>4 to 7 Years</option>
            <option value="8-12" {{ (old('baby_seat_age', (old('baby_seat', $booking->baby_seat) ? $booking->baby_seat_age : '')) == '8-12') ? 'selected' : '' }}>8 to 12 Years</option>
            <option value="13+" {{ (old('baby_seat_age', (old('baby_seat', $booking->baby_seat) ? $booking->baby_seat_age : '')) == '13+') ? 'selected' : '' }}>13+ Years</option>
          </select>
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Note To Driver</label>
        <textarea name="message_to_driver" rows="3" class="mt-1 block w-full border rounded p-2">{{ old('message_to_driver',$booking->message_to_driver) }}</textarea>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Note to Admin</label>
        <textarea name="message_to_admin" rows="3" class="mt-1 block w-full border rounded p-2" placeholder="Private note to admin">{{ old('message_to_admin',$booking->message_to_admin) }}</textarea>
      </div>

      <input type="hidden" name="vehicle_type" value="{{ old('vehicle_type',$booking->vehicle_type) }}">

  <script>
    (function(){
      try {
        var seatSelect = document.getElementById('baby_seat_select');
        var seatHidden = document.getElementById('baby_seat_hidden');
        if (!seatSelect || !seatHidden) return;
        function syncBabySeat(){ seatHidden.value = seatSelect.value ? '1' : '0'; }
        seatSelect.addEventListener('change', syncBabySeat);
        syncBabySeat();
      } catch(e){ console.error('baby seat sync error', e); }
    })();
  </script>

    </div>

    <div class="lg:col-span-1">
      <div class="min-h-full rounded border border-dashed border-gray-300 bg-white p-4 space-y-3">
        @if($isConfirmedTab)
          @if($booking->driver_id)
            <div>
              <button type="button" id="remove-driver-btn" class="w-full px-3 py-2 text-white rounded text-sm" style="background-color: #DC2626;">Remove Driver</button>
            </div>
          @else
            <div>
              <button type="button" id="assign-driver-open" class="w-full px-3 py-2 text-white rounded text-sm" style="background-color: #4F46E5;">Assign Driver</button>
            </div>
          @endif
        @endif

        <div>
          <label class="block text-sm font-medium text-gray-700">Status</label>
          <select id="status-primary" name="status" class="mt-1 block w-full border rounded p-2">
            <option value="">(Keep current)</option>
            <option value="new" {{ (old('status', $booking->status->name ?? '') == 'new') ? 'selected' : '' }}>New</option>
            <option value="confirmed" {{ (old('status', $booking->status->name ?? '') == 'confirmed') ? 'selected' : '' }}>Confirmed</option>
            <option value="in_progress" {{ (old('status', $booking->status->name ?? '') == 'in_progress') ? 'selected' : '' }}>Pending</option>
            <option value="completed" {{ (old('status', $booking->status->name ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ (old('status', $booking->status->name ?? '') == 'cancelled') ? 'selected' : '' }}>Canceled</option>
            <option value="junk" {{ (old('status') == 'junk') ? 'selected' : '' }}>Junk</option>
          </select>
        </div>
        
        @if($isConfirmedTab)
          <div>
            <button type="button" id="send-confirmation-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #059669;">Send Confirmatiion (Email+Sms)</button>
          </div>
        @endif

        @if($isConfirmedTab)
          <div>
            <button type="button" id="contact-whatsapp-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #25D366;">Contact On whatsapp</button>
          </div>
        @endif

        @if($isConfirmedTab)
          <div>
            <button type="button" id="send-driver-info-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #0EA5E9;">Send Driver info</button>
          </div>
        @endif

        @if($isConfirmedTab)
          <div>
            <button type="button" id="copy-job-details-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #64748B;">Copy Job details</button>
          </div>
        @endif

        @if($isConfirmedTab)
          <div>
            <a type="button" href="https://www.google.com/search?q={{ $booking->flight_number }}" target="_blank"  class="w-full px-3 py-2 border rounded text-sm text-center text-white" style="background-color: #a812b3;">Track Flight</a>
          </div>
        @endif

@if($isConfirmedTab || $isCompletedTab)

  @php
    $assignedDriver = $booking->driver;
    $assignedDriverName = $booking->driver_name ?: ($assignedDriver->name ?? null);
    $assignedDriverPhone = $assignedDriver->phone ?? null;
    $assignedDriverCarType = $assignedDriver->car_type ?? ($booking->vehicle_type ?? null);
    $assignedDriverPlate = $assignedDriver->vehicle_plate ?? null;

    $isAssigned = $assignedDriverName || $booking->driver_id;
  @endphp

  <div class="rounded border border-gray-200 bg-gray-50 p-3 text-sm">
    <div class="font-semibold text-gray-800 mb-2">Assigned Driver Info</div>

    @if($isAssigned)
      <div class="text-gray-700"><span class="font-medium">Name:</span> {{ $assignedDriverName }}</div>
      <div class="text-gray-700"><span class="font-medium">Phone:</span> {{ $assignedDriverPhone ?? '-' }}</div>
      <div class="text-gray-700"><span class="font-medium">Car Type:</span> {{ $assignedDriverCarType ?? '-' }}</div>
      <div class="text-gray-700"><span class="font-medium">Number Plate:</span> {{ $assignedDriverPlate ?? '-' }}</div>
    @else
      <div class="text-gray-500 italic">Not assigned yet</div>
    @endif

  </div>

@endif

        @if($isCancelledTab)
          <div>
            <button type="button" id="send-cancellation-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #DC2626;">Send Cancellation</button>
          </div>
        @endif

        @if($isCompletedTab)
          <div>
            <button type="button" id="send-receipt-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #2563EB;">Send Receipt</button>
          </div>
          <div>
            <button type="button" id="send-review-approval-btn" class="w-full px-3 py-2 border rounded text-sm text-white" style="background-color: #7C3AED;">Send review approval</button>
          </div>
        @endif
      </div>

      @if($isConfirmedTab)
        <div id="assign-driver-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
          <div class="fixed inset-0 bg-black opacity-40"></div>
          <div class="bg-white rounded-lg shadow-lg z-60 w-full max-w-lg p-6 mx-4 space-y-4">
            <div class="flex items-start justify-between">
              <h3 class="text-lg font-semibold">Assign Driver</h3>
              <button type="button" class="text-gray-400 hover:text-gray-600" id="assign-driver-close" aria-label="Close">✕</button>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Driver Visible Price (GBP)</label>
              <div class="input-with-icon">
                <span class="input-icon" style="color: #6b7280;">£</span>
                <input id="driver-display-price-input" type="number" step="0.01" name="driver_display_price" value="{{ old('driver_display_price', $booking->meta['driver_display_price'] ?? ($booking->driver_price ?? $booking->total_price)) }}" class="mt-1 block w-full border rounded p-2 form-input form-input-with-icon" placeholder="0.00" />
              </div>
              <span class="text-xs text-gray-500">This is the fare shown to the driver. Original booking price remains admin-only.</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Assign driver</label>
              <select name="driver_id" class="mt-1 block w-full border rounded p-2">
                <option value="">(Keep current)</option>
                <option value="__remove__">Remove Driver ⚠️</option>
                @foreach($activeDrivers as $drv)
                  <option value="{{ $drv->id }}" {{ (string)old('driver_id', $booking->driver_id) === (string)$drv->id ? 'selected' : '' }}>{{ $drv->name }}</option>
                @endforeach
              </select>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Driver percentage (%)</label>
              <input id="driver-percentage-input" name="driver_percentage" type="number" min="0" max="100" step="0.01" value="{{ old('driver_percentage', $booking->meta['driver_percentage'] ?? 20) }}" class="mt-1 block w-full border rounded p-2" />
              <span class="text-xs text-gray-500">Driver payout is calculated as: (Driver Visible Price) × (1 - Percentage/100)</span>
            </div>

            <div>
              <label class="block text-sm font-medium text-gray-700">Driver Price (GBP)</label>
              <div class="input-with-icon">
                <span class="input-icon" style="color: #6b7280;">£</span>
                <input id="driver-price-input" type="number" step="0.01" name="driver_price" value="{{ old('driver_price', $booking->driver_price) }}" class="mt-1 block w-full border rounded p-2 form-input form-input-with-icon" placeholder="0.00" />
              </div>
            </div>

            <div class="flex justify-end gap-2">
              <button type="button" id="assign-driver-done" class="px-4 py-2 border rounded">Done</button>
            </div>
          </div>
        </div>

        <div id="remove-driver-confirmation-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
          <div class="fixed inset-0 bg-black opacity-40"></div>
          <div class="bg-white rounded-lg shadow-lg z-60 w-full max-w-md p-6 mx-4 space-y-4">
            <div class="flex items-start justify-between">
              <div>
                <h3 class="text-lg font-semibold">Confirm Removal</h3>
                <p class="text-sm text-gray-600 mt-1">Are you sure you want to remove the assigned driver from this booking?</p>
              </div>
              <button type="button" class="text-gray-400 hover:text-gray-600" id="remove-driver-cancel-btn" aria-label="Close">✕</button>
            </div>
            <div class="flex justify-end gap-3">
              <button type="button" id="remove-driver-cancel-action" class="px-4 py-2 border rounded text-sm">Cancel</button>
              <button type="button" id="remove-driver-confirm-btn" class="px-4 py-2 text-white rounded text-sm" style="background-color: #DC2626;">Remove Driver</button>
            </div>
          </div>
        </div>
      @endif

      <script>
        (function(){
          try {
            var openBtn = document.getElementById('assign-driver-open');
            var closeBtn = document.getElementById('assign-driver-close');
            var doneBtn = document.getElementById('assign-driver-done');
            var modal = document.getElementById('assign-driver-modal');
            function openModal(){ if (modal) modal.classList.remove('hidden'); }
            function closeModal(){ if (modal) modal.classList.add('hidden'); }
            if (openBtn) openBtn.addEventListener('click', openModal);
            if (closeBtn) closeBtn.addEventListener('click', closeModal);
            if (doneBtn) {
              doneBtn.addEventListener('click', function(){
                closeModal();
                if (@json($isReadOnly)) {
                  var readonlyForm = document.getElementById('booking-edit-form');
                  if (readonlyForm && typeof readonlyForm.requestSubmit === 'function') {
                    readonlyForm.requestSubmit();
                  }
                }
              });
            }

            var removeDriverBtn = document.getElementById('remove-driver-btn');
            var removeDriverModal = document.getElementById('remove-driver-confirmation-modal');
            var removeDriverCancelBtn = document.getElementById('remove-driver-cancel-action');
            var removeDriverCloseBtn = document.getElementById('remove-driver-cancel-btn');
            var removeDriverConfirmBtn = document.getElementById('remove-driver-confirm-btn');

            function openRemoveDriverModal() {
              if (removeDriverModal) {
                removeDriverModal.classList.remove('hidden');
              }
            }

            function closeRemoveDriverModal() {
              if (removeDriverModal) {
                removeDriverModal.classList.add('hidden');
              }
            }

            if (removeDriverBtn) {
              removeDriverBtn.addEventListener('click', function() {
                openRemoveDriverModal();
              });
            }

            if (removeDriverCancelBtn) {
              removeDriverCancelBtn.addEventListener('click', function() {
                closeRemoveDriverModal();
              });
            }

            if (removeDriverCloseBtn) {
              removeDriverCloseBtn.addEventListener('click', function() {
                closeRemoveDriverModal();
              });
            }

            if (removeDriverConfirmBtn) {
              removeDriverConfirmBtn.addEventListener('click', function() {
                if (removeDriverBtn) {
                  removeDriverBtn.disabled = true;
                  removeDriverBtn.style.opacity = '0.65';
                  removeDriverBtn.style.cursor = 'not-allowed';
                }
                closeRemoveDriverModal();
                var driverSelect = document.querySelector('select[name="driver_id"]');
                if (driverSelect) {
                  driverSelect.value = '__remove__';
                }
                var form = document.getElementById('booking-edit-form');
                if (form) {
                  if (typeof form.requestSubmit === 'function') {
                    form.requestSubmit();
                  } else {
                    form.submit();
                  }
                }
              });
            }

            var percentInput = document.getElementById('driver-percentage-input');
            var driverPriceInput = document.getElementById('driver-price-input');
            var driverDisplayPriceInput = document.getElementById('driver-display-price-input');
            function compute(){
              if (!percentInput || !driverPriceInput) return;
              var pct = parseFloat(percentInput.value || '0');
              if (isNaN(pct)) pct = 0;
              if (pct < 0) pct = 0;
              if (pct > 100) pct = 100;
              var base = parseFloat((driverDisplayPriceInput && driverDisplayPriceInput.value) ? driverDisplayPriceInput.value : '{{ $booking->driver_price ?? $booking->total_price ?? 0 }}');
              if (isNaN(base)) base = 0;
              driverPriceInput.value = (base * (1 - (pct / 100))).toFixed(2);
            }
            if (percentInput) { percentInput.addEventListener('input', compute); percentInput.addEventListener('change', compute); }
            if (driverDisplayPriceInput) driverDisplayPriceInput.addEventListener('input', compute);
            compute();

            var copyBtn = document.getElementById('copy-job-details-btn');
            if (copyBtn) {
              copyBtn.addEventListener('click', function(){
                var getVal = function(name){ var el = document.querySelector('[name="' + name + '"]'); return el ? (el.value || '').trim() : ''; };
                var details = [
                  'Booking: {{ $booking->booking_code }}',
                  'Passenger: ' + getVal('passenger_name'),
                  'Phone: ' + getVal('phone'),
                  'Pickup: ' + getVal('pickup_address'),
                  'Dropoff: ' + getVal('dropoff_address'),
                  'Pickup Date: ' + getVal('pickup_date'),
                  'Pickup Time: ' + getVal('pickup_time'),
                  'Driver Fare GBP: ' + (getVal('driver_display_price') || getVal('driver_price') || '{{ $booking->driver_price ?? $booking->total_price ?? '' }}'),
                  'Driver Payout GBP: ' + (getVal('driver_price') || '{{ $booking->driver_price ?? '' }}')
                ].join('\n');
                if (navigator.clipboard && navigator.clipboard.writeText) {
                  navigator.clipboard.writeText(details).then(function(){ if (typeof window.showToast === 'function') window.showToast('Job details copied'); });
                } else {
                  var ta = document.createElement('textarea');
                  ta.value = details;
                  document.body.appendChild(ta);
                  ta.select();
                  document.execCommand('copy');
                  ta.remove();
                  if (typeof window.showToast === 'function') window.showToast('Job details copied');
                }
              });
            }

            function configureSendButton(button, options) {
              if (!button) return;
              var originalText = button.textContent;
              var loadingText = options.loadingText || 'Sending';
              var successMessage = options.successMessage;
              var errorMessage = options.errorMessage;
              var url = options.url;
              var loadingTimer = null;

              var setLoading = function() {
                button.disabled = true;
                button.dataset.originalText = originalText;
                button.textContent = loadingText + '...';
                button.style.opacity = '0.65';
                button.style.cursor = 'not-allowed';
                var dots = 0;
                loadingTimer = setInterval(function() {
                  dots = (dots + 1) % 4;
                  button.textContent = loadingText + new Array(dots + 1).join('.');
                }, 400);
              };

              var resetButton = function() {
                if (loadingTimer) {
                  clearInterval(loadingTimer);
                  loadingTimer = null;
                }
                button.disabled = false;
                button.textContent = button.dataset.originalText || originalText;
                button.style.opacity = '';
                button.style.cursor = '';
              };

              button.addEventListener('click', function() {
                var tokenEl = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
                var token = tokenEl ? (tokenEl.getAttribute('content') || tokenEl.value) : '';
                setLoading();
                fetch(url, {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  }
                })
                .then(function(res){ return res.json().catch(function(){ return { success: false, message: 'Invalid response from server' }; }); })
                .then(function(json){
                  if (json && json.success) {
                    if (typeof window.showToast === 'function') window.showToast(json.message || successMessage);
                    else alert(json.message || successMessage);
                  } else {
                    if (typeof window.showAlert === 'function') window.showAlert('Error', (json && json.message) ? json.message : errorMessage);
                    else alert((json && json.message) ? json.message : errorMessage);
                  }
                })
                .catch(function(err){
                  console.error(errorMessage.toLowerCase(), err);
                  if (typeof window.showAlert === 'function') window.showAlert('Error', errorMessage);
                  else alert(errorMessage);
                })
                .finally(function(){ resetButton(); });
              });
            }

            configureSendButton(document.getElementById('send-confirmation-btn'), {
              url: '{{ route('admin.bookings.send_confirmation', $booking) }}',
              loadingText: 'Sending confirmation...',
              successMessage: 'Confirmation email sent',
              errorMessage: 'Failed to send confirmation email'
            });

            var sendDriverInfoBtn = document.getElementById('send-driver-info-btn');
            if (sendDriverInfoBtn) {
              sendDriverInfoBtn.addEventListener('click', function(){
                var tokenEl = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
                var token = tokenEl ? (tokenEl.getAttribute('content') || tokenEl.value) : '';
                sendDriverInfoBtn.disabled = true;
                fetch('{{ route('admin.bookings.send_driver_info', $booking) }}', {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  }
                })
                .then(function(res){ return res.json().catch(function(){ return { success: false, message: 'Invalid response from server' }; }); })
                .then(function(json){
                  if (json && json.success) {
                    if (typeof window.showToast === 'function') window.showToast(json.message || 'Driver info email sent');
                    else alert(json.message || 'Driver info email sent');
                  } else {
                    if (typeof window.showAlert === 'function') window.showAlert('Error', (json && json.message) ? json.message : 'Failed to send driver info email');
                    else alert((json && json.message) ? json.message : 'Failed to send driver info email');
                  }
                })
                .catch(function(err){
                  console.error('send driver info failed', err);
                  if (typeof window.showAlert === 'function') window.showAlert('Error', 'Failed to send driver info email');
                  else alert('Failed to send driver info email');
                })
                .finally(function(){ sendDriverInfoBtn.disabled = false; });
              });
            }

            configureSendButton(document.getElementById('send-cancellation-btn'), {
              url: '{{ route('admin.bookings.send_cancellation', $booking) }}',
              loadingText: 'Sending cancellation...',
              successMessage: 'Cancellation email sent',
              errorMessage: 'Failed to send cancellation email'
            });

            configureSendButton(document.getElementById('send-receipt-btn'), {
              url: '{{ route('admin.bookings.send_completion', $booking) }}',
              loadingText: 'Sending receipt...',
              successMessage: 'Completion receipt sent',
              errorMessage: 'Failed to send completion receipt'
            });

            var sendReviewApprovalBtn = document.getElementById('send-review-approval-btn');
            if (sendReviewApprovalBtn) {
              sendReviewApprovalBtn.addEventListener('click', function(){
                var tokenEl = document.querySelector('meta[name="csrf-token"]') || document.querySelector('input[name="_token"]');
                var token = tokenEl ? (tokenEl.getAttribute('content') || tokenEl.value) : '';
                sendReviewApprovalBtn.disabled = true;
                fetch('{{ route('admin.bookings.send_review_approval', $booking) }}', {
                  method: 'POST',
                  credentials: 'same-origin',
                  headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                  }
                })
                .then(function(res){ return res.json().catch(function(){ return { success: false, message: 'Invalid response from server' }; }); })
                .then(function(json){
                  if (json && json.success) {
                    if (typeof window.showToast === 'function') window.showToast(json.message || 'Review approval queued');
                    else alert(json.message || 'Review approval queued');
                  } else {
                    if (typeof window.showAlert === 'function') window.showAlert('Error', (json && json.message) ? json.message : 'Failed to queue review approval');
                    else alert((json && json.message) ? json.message : 'Failed to queue review approval');
                  }
                })
                .catch(function(err){
                  console.error('send review approval failed', err);
                  if (typeof window.showAlert === 'function') window.showAlert('Error', 'Failed to queue review approval');
                  else alert('Failed to queue review approval');
                })
                .finally(function(){ sendReviewApprovalBtn.disabled = false; });
              });
            }

            var whatsappBtn = document.getElementById('contact-whatsapp-btn');
            if (whatsappBtn) {
              whatsappBtn.addEventListener('click', function(){
                var phoneInput = document.querySelector('input[name="phone"]');
                var rawPhone = phoneInput ? (phoneInput.value || '') : '';
                var digits = (rawPhone || '').replace(/[^\d+]/g, '');
                var normalized = digits;
                if (normalized.indexOf('+') === 0) {
                  normalized = normalized.substring(1);
                }
                if (normalized.indexOf('0') === 0) {
                  normalized = '44' + normalized.substring(1);
                }
                if (!normalized) {
                  if (typeof window.showAlert === 'function') window.showAlert('Error', 'Customer phone is missing');
                  else alert('Customer phone is missing');
                  return;
                }

                var pickup = (document.querySelector('input[name="pickup_address"]') || {}).value || '';
                var dropoff = (document.querySelector('input[name="dropoff_address"]') || {}).value || '';
                var msg = 'Booking {{ $booking->booking_code }}\nPickup: ' + pickup + '\nDropoff: ' + dropoff;
                var waUrl = 'https://wa.me/' + encodeURIComponent(normalized) + '?text=' + encodeURIComponent(msg);
                window.open(waUrl, '_blank');
              });
            }
          } catch (e) {
            console.error('right panel init error', e);
          }
        })();
      </script>
    </div>

  </div>

  <div class="mt-6 rounded border border-dashed border-gray-300 bg-white p-4">
    <h3 class="text-sm font-semibold text-gray-800 mb-3">Booking Change Logs</h3>
    <div id="booking-change-log-list" class="space-y-2 max-h-[420px] overflow-y-auto pr-1">
      @if(empty($changeLogs))
        <div class="text-sm text-gray-500">No changes logged yet.</div>
      @else
        @foreach(array_reverse($changeLogs) as $entry)
          @php
            $entryAt = $entry['at'] ?? '-';
            $entryBy = $entry['by']['name'] ?? 'System';
            $entryChanges = is_array($entry['changes'] ?? null) ? $entry['changes'] : [];
            $parts = [];
            foreach ($entryChanges as $chg) {
              $field = $chg['field'] ?? 'Field';
              $oldVal = $chg['old'] ?? '-';
              $newVal = $chg['new'] ?? '-';
              $parts[] = $field . ': ' . $oldVal . ' -> ' . $newVal;
            }
            $line = $entryAt . ' | By: ' . $entryBy . (empty($parts) ? '' : (' | ' . implode(' | ', $parts)));
          @endphp
          <div class="rounded border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 whitespace-nowrap overflow-x-auto" title="{{ $line }}">
            {{ $line }}
          </div>
        @endforeach
      @endif
    </div>
  </div>
</form>

@if($isReadOnly)
<script>
  (function(){
    try {
      var form = document.getElementById('booking-edit-form');
      if (!form) return;

      form.querySelectorAll('input:not([type="hidden"]), textarea').forEach(function(el){
        var name = el.getAttribute('name') || '';
        if (name === 'driver_display_price' || name === 'driver_percentage' || name === 'driver_price') return;
        el.readOnly = true;
        el.classList.add('bg-gray-50');
      });

      form.querySelectorAll('select').forEach(function(sel){
        var name = sel.getAttribute('name') || '';
        if (name === 'driver_id' || name === 'status') return;
        var initial = sel.value;
        sel.classList.add('bg-gray-50', 'cursor-not-allowed');
        sel.addEventListener('mousedown', function(e){ e.preventDefault(); });
        sel.addEventListener('keydown', function(e){ e.preventDefault(); });
        sel.addEventListener('change', function(){ sel.value = initial; });
      });

      var statusSelect = document.getElementById('status-primary');
      if (statusSelect) {
        statusSelect.addEventListener('change', function(){
          if (form && typeof form.requestSubmit === 'function') {
            form.requestSubmit();
          }
        });
      }

      ['source-update-btn']
        .forEach(function(id){
          var btn = document.getElementById(id);
          if (!btn) return;
          btn.disabled = true;
          btn.classList.add('opacity-50', 'cursor-not-allowed');
        });
    } catch (e) {
      console.error('readonly mode init failed', e);
    }
  })();
</script>
@endif

@if(session('success'))
  <script>
    document.addEventListener('DOMContentLoaded', function(){
      var msg = @json(session('success'));
      if (typeof window.showToast === 'function') {
        window.showToast(msg);
      } else if (typeof window.showAlert === 'function') {
        window.showAlert('Updated', msg);
      } else {
        alert(msg);
      }
    });
  </script>
@endif

<script>
  (function(){
    try {
      var form = document.getElementById('booking-edit-form');
      if (!form) return;

      function escapeLogHtml(value) {
        return String(value === null || value === undefined ? '' : value)
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#39;');
      }

      function renderBookingChangeLogs(logs) {
        var container = document.getElementById('booking-change-log-list');
        if (!container) return;

        if (!Array.isArray(logs) || !logs.length) {
          container.innerHTML = '<div class="text-sm text-gray-500">No changes logged yet.</div>';
          return;
        }

        var ordered = logs.slice().reverse();
        container.innerHTML = ordered.map(function(entry){
          var at = entry && entry.at ? entry.at : '-';
          var by = (entry && entry.by && entry.by.name) ? entry.by.name : 'System';
          var changes = Array.isArray(entry && entry.changes ? entry.changes : null) ? entry.changes : [];

          var changeSummary = changes.map(function(chg){
            var field = chg && chg.field ? chg.field : 'Field';
            var oldVal = chg && chg.old ? chg.old : '-';
            var newVal = chg && chg.new ? chg.new : '-';
            return field + ': ' + oldVal + ' -> ' + newVal;
          }).join('');

          var line = at + ' | By: ' + by + (changeSummary ? (' | ' + changeSummary) : '');

          return '<div class="rounded border border-gray-200 bg-gray-50 px-3 py-2 text-xs text-gray-700 whitespace-nowrap overflow-x-auto" title="' + escapeLogHtml(line) + '">' +
            escapeLogHtml(line) +
          '</div>';
        }).join('');
      }

      form.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(form);
        var action = form.getAttribute('action');
        var submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) submitBtn.disabled = true;

        fetch(action, {
          method: 'POST',
          body: fd,
          credentials: 'same-origin',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        }).then(function(res){
          if (res.ok) {
            return res.json().then(function(json){
              if (json && json.success) {
                var message = json.message || (json.moved_to ? 'Booking updated' : 'Booking updated');
                if (typeof window.showToast === 'function') window.showToast(message);
                else if (typeof window.showAlert === 'function') window.showAlert('Updated', message);
                else alert(message);

                if (json.booking && json.booking.meta && json.booking.meta.change_logs) {
                  renderBookingChangeLogs(json.booking.meta.change_logs);
                }

                // If server included a warning (assignment allowed but driver remains inactive), show it
                if (json.warning) {
                  if (typeof window.showToast === 'function') window.showToast(json.warning);
                  else if (typeof window.showAlert === 'function') window.showAlert('Warning', json.warning);
                  else alert(json.warning);
                }

                // Broadcast update/move events so booking lists refresh immediately without full reload.
                var selectedStatusEl = form.querySelector('[name="status"]');
                var selectedStatus = selectedStatusEl ? selectedStatusEl.value : null;
                var moveTarget = json.moved_to || selectedStatus || null;

                try {
                  document.dispatchEvent(new CustomEvent('bookingUpdated', {
                    detail: {
                      id: json.booking && json.booking.id ? json.booking.id : null,
                      booking: json.booking || null,
                      to: moveTarget
                    }
                  }));
                } catch (evtErr) {
                  console.warn('Failed to dispatch bookingUpdated event', evtErr);
                }

                if (moveTarget) {
                  try {
                    document.dispatchEvent(new CustomEvent('bookingMoved', {
                      detail: {
                        id: json.booking && json.booking.id ? json.booking.id : null,
                        to: moveTarget
                      }
                    }));
                  } catch (evtErr) {
                    console.warn('Failed to dispatch bookingMoved event', evtErr);
                  }
                }

              } else if (json && json.conflict) {
                // Driver availability conflict - show modal and allow override
                try {
                  var confModal = document.getElementById('availability-conflict-modal');
                  var msgEl = document.getElementById('availability-conflict-message');
                  if (msgEl && json.message) {
                    msgEl.textContent = json.message;

                    // If the server sent expired documents information, show a simple popup and list them
                    if (json.documents && Array.isArray(json.documents) && json.documents.length) {
                      try {
                        var confModal = document.getElementById('availability-conflict-modal');
                        var msgEl = document.getElementById('availability-conflict-message');
                        if (msgEl) msgEl.textContent = 'Selected driver has expired documents. Update them before assigning.';

                        // remove previous details block if exists
                        var prev = document.getElementById('availability-conflict-details'); if (prev) prev.remove();

                        var details = document.createElement('div'); details.id = 'availability-conflict-details'; details.className = 'mt-3 text-sm text-gray-700';

                        json.documents.forEach(function(d){
                          try {
                            var row = document.createElement('div'); row.className = 'py-2 border-b last:border-b-0';
                            row.innerHTML = '<div class="font-medium text-gray-800">'+ (d.label || '') +'</div><div class="text-xs text-red-600 mt-1">'+ (d.expiry || '') + ' Expired</div>';
                            details.appendChild(row);
                          } catch(e){ console.warn('Failed to append doc row', e); }
                        });

                        msgEl.parentNode.insertBefore(details, msgEl.nextSibling);

                        if (confModal) { confModal.classList.remove('hidden'); var cbtn = confModal.querySelector('#availability-conflict-cancel'); if (cbtn && typeof cbtn.focus === 'function') cbtn.focus(); }
                        return;
                      } catch(e){ console.warn('Failed to show expired docs popup', e); }
                    }

                    // build a side-by-side comparison block for pickup vs unavailable window
                    try {
                      // remove previous details block if exists
                      var prev = document.getElementById('availability-conflict-details'); if (prev) prev.remove();

                      var details = document.createElement('div'); details.id = 'availability-conflict-details'; details.className = 'mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700';

                      var pickupCol = document.createElement('div'); var up = json.pickup_at ? json.pickup_at : 'N/A';
                      pickupCol.innerHTML = '<div class="font-medium text-gray-800">Pickup Time</div><div class="text-xs text-gray-600 mt-1">' + up + '</div>';

                      var availCol = document.createElement('div');
                      var from = json.unavailable_from ? json.unavailable_from : 'N/A';
                      var to = json.unavailable_to ? json.unavailable_to : 'N/A';
                      availCol.innerHTML = '<div class="font-medium text-gray-800">Driver Unavailable</div><div class="text-xs text-gray-600 mt-1">' + from + ' &ndash; ' + to + '</div>';

                      details.appendChild(pickupCol);
                      details.appendChild(availCol);

                      msgEl.parentNode.insertBefore(details, msgEl.nextSibling);
                    } catch(e){ console.warn('Failed to append unavailable details', e); }
                  }

                  if (confModal) {
                    confModal.classList.remove('hidden');
                    // focus confirm button for accessibility
                    var cbtn = confModal.querySelector('#availability-conflict-confirm'); if (cbtn && typeof cbtn.focus === 'function') cbtn.focus();
                  } else {
                    if (typeof window.showAlert === 'function') window.showAlert('Driver Unavailable', json.message);
                    else alert(json.message);
                  }
                } catch(e){ console.error('Failed to show availability conflict modal', e); if (typeof window.showAlert === 'function') window.showAlert('Update failed', json.message); else alert(json.message); }

              } else {
                var msg = (json && json.message) ? json.message : 'Failed to update booking';
                if (typeof window.showAlert === 'function') window.showAlert('Update failed', msg); else alert(msg);
              }
            }).catch(function(err){
              console.error('parse json error', err);
              if (typeof window.showAlert === 'function') window.showAlert('Error', 'Failed to update booking'); else alert('Failed to update booking');
            });
          } else if (res.status === 422) {
            return res.text().then(function(text){
              if (typeof window.showAlert === 'function') window.showAlert('Validation Error', text); else alert('Validation error');
            });
          } else {
            return res.text().then(function(text){
              if (typeof window.showAlert === 'function') window.showAlert('Error', text || 'Update failed'); else alert(text || 'Update failed');
            });
          }
        }).catch(function(err){
          console.error('Update error', err);
          if (typeof window.showAlert === 'function') window.showAlert('Error', 'Update failed'); else alert('Update failed');
        }).finally(function(){ if (submitBtn) submitBtn.disabled = false; });
      });

    } catch(e){ console.error('booking-edit-form init', e); }
  })();
</script>

<script>
  (function(){
    function updateRow(booking){
      try {
        var tr = document.querySelector('tr[data-booking-id="'+booking.id+'"]');
        if (!tr) return;
        var driverCell = tr.querySelector('[data-col="driver_name"]');
        if (driverCell) driverCell.innerText = booking.driver_name ? booking.driver_name : '-';
        var driverPriceCell = tr.querySelector('[data-col="driver_price"]');
        if (driverPriceCell) driverPriceCell.innerText = (booking.driver_price ? parseFloat(booking.driver_price).toFixed(2) : '-');
        var responseCell = tr.querySelector('[data-col="driver_response"]');
        if (responseCell) {
          // show '-' if there's no driver assigned
          if (!booking.driver_id) {
            responseCell.innerHTML = '<span class="text-sm text-gray-500">-</span>';
          } else {
            var meta = booking.meta && typeof booking.meta === 'object' ? booking.meta : {};
            var dr = meta.driver_response ? String(meta.driver_response).toLowerCase() : '';
            var statusName = (booking.status && booking.status.name) ? String(booking.status.name).toLowerCase() : '';
            var inRoute = (meta.in_route === true || meta.in_route === 1 || meta.in_route === '1' || meta.in_route === 'true');
            var arrivedAtPickup = (meta.arrived_at_pickup === true || meta.arrived_at_pickup === 1 || meta.arrived_at_pickup === '1' || meta.arrived_at_pickup === 'true');
            var pobMarked = !!meta.pob_marked_at;
            var completedAt = !!meta.completed_at;
            var statusClass = '';
            var statusText = '';

            if (statusName === 'completed' || completedAt) {
              statusClass = 'bg-emerald-100 text-emerald-800';
              statusText = 'Completed';
            } else if (statusName === 'pob' || pobMarked) {
              statusClass = 'bg-indigo-100 text-indigo-800';
              statusText = 'POB';
            } else if (arrivedAtPickup) {
              statusClass = 'bg-sky-100 text-sky-800';
              statusText = 'Arrived';
            } else if (inRoute) {
              statusClass = 'bg-purple-100 text-purple-800';
              statusText = 'In Route';
            } else if (dr === 'accepted') {
              statusClass = 'bg-green-100 text-green-800';
              statusText = 'Accepted';
            } else if (dr === 'declined') {
              statusClass = 'bg-red-100 text-red-800';
              statusText = 'Rejected';
            } else {
              statusClass = 'bg-yellow-100 text-yellow-800';
              statusText = 'Pending';
            }

            responseCell.innerHTML = '<span class="text-xs px-2 py-1 rounded-full font-medium '+statusClass+'">'+statusText+'</span>';
          }
        }
        var totalCell = tr.querySelector('[data-col="total_price"]');
        if (totalCell) totalCell.innerText = (booking.total_price ? parseFloat(booking.total_price).toFixed(2) : '-');
      } catch(e){ console.error('updateRow error', e); }
    }

    document.addEventListener('bookingUpdated', function(e){
      if (!e || !e.detail || !e.detail.booking) return;
      updateRow(e.detail.booking);
    });

    // Also provide a global function for immediate usage
    window.updateBookingRow = updateRow;
  })();
</script> 