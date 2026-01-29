<form id="booking-edit-form" method="POST" action="{{ route('admin.bookings.update', $booking) }}" class="space-y-4">
  @csrf
  @method('PUT')

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Passenger name</label>
      <input type="text" name="passenger_name" value="{{ old('passenger_name',$booking->passenger_name) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Phone</label>
      <input type="text" name="phone" value="{{ old('phone',$booking->phone) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Email</label>
      <input type="email" name="email" value="{{ old('email',$booking->email) }}" class="mt-1 block w-full border rounded p-2">
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Vehicle type</label>
      <select name="vehicle_type" class="mt-1 block w-full border rounded p-2">
        <option value="">(Auto)</option>
        @foreach($vehicleTypes as $vt)
          <option value="{{ $vt }}" {{ (old('vehicle_type',$booking->vehicle_type) == $vt) ? 'selected' : '' }}>{{ $vt }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="block text-sm font-medium text-gray-700">Pickup date</label>
      <input type="date" name="pickup_date" value="{{ old('pickup_date', optional($booking->pickup_date)->format('Y-m-d')) }}" class="mt-1 block w-full border rounded p-2" required>
    </div>
    <div>
      <label class="block text-sm font-medium text-gray-700">Pickup time</label>
      <input type="time" name="pickup_time" value="{{ old('pickup_time', $booking->pickup_time) }}" class="mt-1 block w-full border rounded p-2" required>
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

  <div class="grid grid-cols-1 sm:grid-cols-3 md:grid-cols-4 gap-3 items-end">
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
      <div class="flex items-center gap-2">
        <input type="hidden" name="baby_seat" value="0">
        <input id="baby_seat_checkbox" type="checkbox" name="baby_seat" value="1" {{ (old('baby_seat', $booking->baby_seat) ? 'checked' : '') }} class="h-4 w-4">
        <span class="text-sm">Enable</span>
      </div>
    </div>

    <div id="baby-seat-age-wrapper" class="sm:col-span-2 md:col-span-2">
      <label class="block text-sm font-medium text-gray-700">Baby seat age</label>
      <select id="baby_seat_age" name="baby_seat_age" class="mt-1 block w-full border rounded p-2">
        <option value="">Select child seat</option>
        <option value="0-1" {{ (old('baby_seat_age', $booking->baby_seat_age) == '0-1') ? 'selected' : '' }}>0 to 1 Years</option>
        <option value="1-3" {{ (old('baby_seat_age', $booking->baby_seat_age) == '1-3') ? 'selected' : '' }}>1 to 3 Years</option>
        <option value="4-7" {{ (old('baby_seat_age', $booking->baby_seat_age) == '4-7') ? 'selected' : '' }}>4 to 7 Years</option>
        <option value="8-12" {{ (old('baby_seat_age', $booking->baby_seat_age) == '8-12') ? 'selected' : '' }}>8 to 12 Years</option>
        <option value="13+" {{ (old('baby_seat_age', $booking->baby_seat_age) == '13+') ? 'selected' : '' }}>13+ Years</option>
      </select>
    </div>
  </div>

  <script>
    (function(){
      try {
        var cb = document.getElementById('baby_seat_checkbox');
        var wrapper = document.getElementById('baby-seat-age-wrapper');
        if (!cb || !wrapper) return;
        function toggle(){ if (cb.checked) wrapper.classList.remove('hidden'); else wrapper.classList.add('hidden'); }
        cb.addEventListener('change', toggle);
        // initialize
        toggle();
      } catch(e){ console.error('baby seat toggle error', e); }
    })();
  </script>

  <div>
    <label class="block text-sm font-medium text-gray-700">Message to driver</label>
    <textarea name="message_to_driver" class="mt-1 block w-full border rounded p-2">{{ old('message_to_driver',$booking->message_to_driver) }}</textarea>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Message to admin</label>
    <textarea name="message_to_admin" class="mt-1 block w-full border rounded p-2" placeholder="Private note to admin">{{ old('message_to_admin',$booking->message_to_admin) }}</textarea>
  </div>

  <div>
    <label class="block text-sm font-medium text-gray-700">Price (EUR)</label>
    <div class="input-with-icon">
      <span class="input-icon" style="color: #6b7280;">€</span>
      <input type="number" step="0.01" name="booking_charges" value="{{ old('booking_charges', $booking->total_price) }}" class="mt-1 block w-full border rounded p-2 form-input form-input-with-icon" placeholder="0.00" />
    </div>
  </div>

  @if(optional($booking->status)->name === 'confirmed')
    <div>
      <label class="block text-sm font-medium text-gray-700">Assign driver</label>
      <select name="driver_id" class="mt-1 block w-full border rounded p-2">
        <option value="__remove__">Remove Driver ⚠️</option>
        @foreach($activeDrivers as $drv)
          @php
            // Only show name; if driver is inactive show a minimal '(Inactive)' marker (no datetime range)
            $label = $drv->name;
            if (($drv->status ?? '') !== 'active') {
              //$label .= ' (Inactive)';
              $label .= ' ';
            }
          @endphp
          <option value="{{ $drv->id }}" {{ (string)old('driver_id', $booking->driver_id) === (string)$drv->id ? 'selected' : '' }}>{{ $label }}</option>
        @endforeach
      </select>
    </div>

    <script>
      (function(){
        var select = document.querySelector('select[name="driver_id"]');
        var modal = document.getElementById('remove-driver-modal');
        var btnCancel = modal ? modal.querySelector('#remove-driver-cancel') : null;
        var btnConfirm = modal ? modal.querySelector('#remove-driver-confirm') : null;
        if (!select) return;
        var prev = select.value;

        function showModal(){ if (modal) modal.classList.remove('hidden'); }
        function hideModal(){ if (modal) modal.classList.add('hidden'); }

        select.addEventListener('change', function(e){
          // when changing selection, clear any override flag from prior confirmation
          var ov = document.querySelector('input[name="override_availability"]'); if (ov) ov.parentNode.removeChild(ov);

          if (this.value === '__remove__') {
            // open custom styled modal instead of native confirm
            showModal();

            // on cancel, restore previous value
            if (btnCancel) {
              btnCancel.onclick = function(){ select.value = prev; hideModal(); };
            }

            // on confirm, submit the form and close modal
            if (btnConfirm) {
              btnConfirm.onclick = function(){
                hideModal();
                prev = '';
                var form = document.getElementById('booking-edit-form');
                if (form) form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
              };
            }

          } else {
            prev = this.value;

            // On driver selection, proactively check availability and possibly auto-reactivate if window expired
            try {
              var driverId = this.value;
              if (!driverId) return;
              var url = '{{ url('admin/drivers') }}/' + driverId + '/check-availability';
              fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' }, credentials: 'same-origin' }).then(function(r){ return r.json(); }).then(function(json){
                if (!json || !json.success) return;
                // If server reactivated the driver because unavailable_to passed
                if (json.reactivated) {
                  // Update option label to remove unavailable note (simple approach: reload options from server would be more thorough)
                  var opt = select.querySelector('option[value="'+driverId+'"]');
                  if (opt) {
                    opt.textContent = json.driver.name + ' (Reactivated)';
                  }
                  if (typeof window.showToast === 'function') window.showToast('Driver reactivated (unavailability expired)');
                  return;
                }

                // If still unavailable currently, show conflict modal and prevent selection
                var now = json.now || null;
                var from = json.unavailable_from || null; var to = json.unavailable_to || null;
                if ((from && to) && now && now >= from && now <= to) {
                  // show same modal as on save
                  var confModal = document.getElementById('availability-conflict-modal');
                  var msgEl = document.getElementById('availability-conflict-message');
                  if (msgEl) msgEl.textContent = 'Selected driver is currently unavailable.';

                  // inject comparison details
                  var prevDetails = document.getElementById('availability-conflict-details'); if (prevDetails) prevDetails.remove();
                  try {
                    var details = document.createElement('div'); details.id = 'availability-conflict-details'; details.className = 'mt-3 grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700';
                    var pickupCol = document.createElement('div'); pickupCol.innerHTML = '<div class="font-medium text-gray-800">Now</div><div class="text-xs text-gray-600 mt-1">'+ (now || '') + '</div>';
                    var availCol = document.createElement('div'); availCol.innerHTML = '<div class="font-medium text-gray-800">Unavailable</div><div class="text-xs text-gray-600 mt-1">' + (from || '') + ' &ndash; ' + (to || '') + '</div>';
                    details.appendChild(pickupCol); details.appendChild(availCol);
                    msgEl.parentNode.insertBefore(details, msgEl.nextSibling);
                  } catch(e){ console.warn('Details insert failed', e); }

                  if (confModal) { confModal.classList.remove('hidden'); var cbtn = confModal.querySelector('#availability-conflict-confirm'); if (cbtn && typeof cbtn.focus === 'function') cbtn.focus(); }
                }
              }).catch(function(err){ console.error('driver availability check failed', err); });
            } catch(e){ console.error('driver select handler failed', e); }
          }
        });

        // allow closing modal with Escape key
        document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hideModal(); });
      })();
    </script>

    <!-- Remove driver confirmation modal (hidden by default) -->
    <div id="remove-driver-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black opacity-40"></div>
      <div class="bg-white rounded-lg shadow-lg z-60 w-full max-w-md p-6 mx-4">
        <div class="flex items-start justify-between">
          <h3 class="text-lg font-semibold">Remove assigned driver</h3>
          <button type="button" class="text-gray-400 hover:text-gray-600" id="remove-driver-close" aria-label="Close">✕</button>
        </div>
        <p class="text-sm text-gray-700 mt-3">Remove the assigned driver from this booking? This will unassign the job from the previous driver.</p>
        <div class="mt-4 flex justify-end gap-2">
          <button type="button" id="remove-driver-cancel" class="px-4 py-2 bg-white border rounded">Cancel</button>
          <button type="button" id="remove-driver-confirm" class="px-4 py-2 bg-red-600 text-white rounded">Remove</button>
        </div>
      </div>
    </div>

    <!-- Availability conflict modal (hidden by default) -->
    <div id="availability-conflict-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
      <div class="fixed inset-0 bg-black opacity-40"></div>
      <div class="bg-white rounded-lg shadow-lg z-60 w-full max-w-md p-6 mx-4">
        <div class="flex items-start justify-between">
          <h3 class="text-lg font-semibold">Driver Unavailable</h3>
          <button type="button" class="text-gray-400 hover:text-gray-600" id="availability-conflict-close" aria-label="Close">✕</button>
        </div>
        <div class="mt-3 text-sm text-gray-700" id="availability-conflict-message">Selected driver is unavailable for the requested pickup time.</div>
        <div class="mt-4 flex justify-end gap-2">
          <button type="button" id="availability-conflict-cancel" class="px-4 py-2 bg-white border rounded">Cancel</button>
          <button type="button" id="availability-conflict-confirm" class="px-4 py-2 bg-red-600 text-white rounded">Assign Anyway</button>
        </div>
      </div>
    </div>

    <script>
      (function(){
        var close = document.getElementById('remove-driver-close');
        var modal = document.getElementById('remove-driver-modal');
        if (close && modal) close.addEventListener('click', function(){ modal.classList.add('hidden'); });

        // availability conflict modal handlers
        var confModal = document.getElementById('availability-conflict-modal');
        var confCancel = confModal ? confModal.querySelector('#availability-conflict-cancel') : null;
        var confConfirm = confModal ? confModal.querySelector('#availability-conflict-confirm') : null;
        var confClose = confModal ? confModal.querySelector('#availability-conflict-close') : null;
        if (confCancel && confModal) confCancel.addEventListener('click', function(){ confModal.classList.add('hidden'); });
        if (confClose && confModal) confClose.addEventListener('click', function(){ confModal.classList.add('hidden'); });
        if (confConfirm && confModal) confConfirm.addEventListener('click', function(){
          // set override hidden input and submit via the real submit button (more reliable)
          var form = document.getElementById('booking-edit-form');
          var inp = document.querySelector('input[name="override_availability"]');
          if (!inp) { inp = document.createElement('input'); inp.type='hidden'; inp.name='override_availability'; form.appendChild(inp); }
          inp.value = '1';
          confModal.classList.add('hidden');

          // prevent duplicate clicks
          try { confConfirm.disabled = true; } catch(e){}

          // prefer clicking the submit button so the normal submit handler runs with the fresh DOM
          var submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn) {
            submitBtn.click();
          } else {
            form.dispatchEvent(new Event('submit', { bubbles: true, cancelable: true }));
          }
        });
      })();
    </script>

    <div>
      <label class="block text-sm font-medium text-gray-700">Use percentage</label>
      <div class="flex items-center gap-3">
        <input type="hidden" name="use_percentage" value="0">
        <input id="driver-use-percent" type="checkbox" name="use_percentage" value="1" {{ (old('use_percentage', isset($booking->meta['driver_percentage']) ? '1' : '0') == '1') ? 'checked' : '' }} class="h-4 w-4">
        <span class="text-sm">Calculate driver price by percentage of fare</span>
      </div>
    </div>

    <div id="driver-percent-wrapper" class="{{ (old('use_percentage', isset($booking->meta['driver_percentage']) ? '1' : '0') == '1') ? '' : 'hidden' }}">
      <label class="block text-sm font-medium text-gray-700">Driver percentage (%)</label>
      <input id="driver-percentage-input" name="driver_percentage" type="number" min="0" max="100" step="0.01" value="{{ old('driver_percentage', $booking->meta['driver_percentage'] ?? '') }}" class="mt-1 block w-full border rounded p-2" />
    </div>

    <div>
      <label class="block text-sm font-medium text-gray-700">Driver Price (EUR)</label>
      <div class="input-with-icon">
        <span class="input-icon" style="color: #6b7280;">€</span>
        <input id="driver-price-input" type="number" step="0.01" name="driver_price" value="{{ old('driver_price', $booking->driver_price) }}" class="mt-1 block w-full border rounded p-2 form-input form-input-with-icon" placeholder="0.00" />
      </div>
    </div>

    <script>
      (function(){
        try {
          var useCheckbox = document.getElementById('driver-use-percent');
          var percentWrapper = document.getElementById('driver-percent-wrapper');
          var percentInput = document.getElementById('driver-percentage-input');
          var driverPriceInput = document.getElementById('driver-price-input');
          var bookingPriceInput = document.getElementById('booking-charges-input') || document.getElementById('booking-charges-input-edit');

          function updateVisibility(){ if (!useCheckbox.checked) percentWrapper.classList.add('hidden'); else percentWrapper.classList.remove('hidden'); }

          function compute(){
            try {
              if (!useCheckbox.checked) return;
              var pct = parseFloat(percentInput.value || '0');
              if (isNaN(pct)) pct = 0;
              if (pct < 0) pct = 0; if (pct > 100) pct = 100;
              var base = parseFloat((bookingPriceInput && bookingPriceInput.value) ? bookingPriceInput.value : '{{ $booking->total_price ?? 0 }}');
              if (isNaN(base)) base = 0;
              var computed = (base * (1 - (pct/100))).toFixed(2);
              driverPriceInput.value = computed;
            } catch(e){ console.error('compute driver price error', e); }
          }

          if (useCheckbox){ useCheckbox.addEventListener('change', function(){ updateVisibility(); compute(); }); }
          if (percentInput){ percentInput.addEventListener('input', compute); percentInput.addEventListener('change', compute); }
          if (bookingPriceInput){ bookingPriceInput.addEventListener('input', compute); }

          // initialize
          updateVisibility(); compute();
        } catch(e){ console.error('driver percentage init', e); }
      })();
    </script>
  @endif

  <div>
    <label class="block text-sm font-medium text-gray-700">Status</label>
    <select name="status" class="mt-1 block w-full border rounded p-2">
      <option value="">(Keep current)</option>
      <option value="new" {{ (old('status', $booking->status->name ?? '') == 'new') ? 'selected' : '' }}>New</option>
      <option value="confirmed" {{ (old('status', $booking->status->name ?? '') == 'confirmed') ? 'selected' : '' }}>Confirmed</option>
      <option value="in_progress" {{ (old('status', $booking->status->name ?? '') == 'in_progress') ? 'selected' : '' }}>Pending</option>
      <option value="completed" {{ (old('status', $booking->status->name ?? '') == 'completed') ? 'selected' : '' }}>Completed</option>
      <option value="cancelled" {{ (old('status', $booking->status->name ?? '') == 'cancelled') ? 'selected' : '' }}>Canceled</option>
      <option value="junk" {{ (old('status') == 'junk') ? 'selected' : '' }}>Junk</option>
    </select>
  </div>

  <div class="pt-4">
    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">Update booking</button>
  </div>
</form>

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

                // dispatch bookingUpdated so other parts (list rows) refresh in-place
                if (json.booking) {
                  var updateEvent = new CustomEvent('bookingUpdated', { detail: { booking: json.booking } });
                  document.dispatchEvent(updateEvent);
                }

                // optionally mark moved_to for client-side handling
                if (json.moved_to) {
                  // dispatch a custom event so other scripts can refresh tabs if desired
                  var ev = new CustomEvent('bookingMoved', { detail: { id: json.booking.id, to: json.moved_to } });
                  document.dispatchEvent(ev);
                }

              } else if (json && json.conflict) {
                // Driver availability conflict - show modal and allow override
                try {
                  var confModal = document.getElementById('availability-conflict-modal');
                  var msgEl = document.getElementById('availability-conflict-message');
                  if (msgEl && json.message) {
                    msgEl.textContent = json.message;
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
            var dr = booking.meta && booking.meta.driver_response ? booking.meta.driver_response : null;
            var statusClass = '';
            var statusText = '';
            if (dr === 'accepted') { statusClass = 'bg-green-100 text-green-800'; statusText='Accepted'; }
            else if (dr === 'declined') { statusClass = 'bg-red-100 text-red-800'; statusText='Rejected'; }
            else { statusClass = 'bg-yellow-100 text-yellow-800'; statusText='Pending'; }
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