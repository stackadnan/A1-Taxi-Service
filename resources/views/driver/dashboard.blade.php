@extends('driver.layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-2">
            Welcome, {{ $driver->name }}!
        </h1>
        <p class="text-gray-600">Here's your job overview for today</p>
    </div>

    <!-- Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- New Jobs Card -->
        <a href="{{ route('driver.jobs.new') }}" class="block">
            <div class="stat-card new-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-bell text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">New Jobs</h3>
                    <p id="new-jobs-count" class="text-3xl font-bold">{{ $newJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Accepted Jobs Card -->
        <a href="{{ route('driver.jobs.accepted') }}" class="block">
            <div class="stat-card accepted-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-check-circle text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Accepted Jobs</h3>
                    <p id="accepted-jobs-count" class="text-3xl font-bold">{{ $acceptedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Completed Jobs Card -->
        <a href="{{ route('driver.jobs.completed') }}" class="block">
            <div class="stat-card completed-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-trophy text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Completed Jobs</h3>
                    <p id="completed-jobs-count" class="text-3xl font-bold">{{ $completedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Declined Jobs Card -->
        <a href="{{ route('driver.jobs.declined') }}" class="block">
            <div class="stat-card declined-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-times-circle text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Jobs Declined</h3>
                    <p id="declined-jobs-count" class="text-3xl font-bold">{{ $declinedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Expired / Expiring Documents Card -->
        <a href="{{ route('driver.documents.expired') }}" class="block">
            <div class="stat-card expired-docs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-file-alt text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Expired / Expiring Documents</h3>
                    <p id="expired-docs-count" class="text-3xl font-bold">{{ $expiredDocsCount ?? 0 }}</p>
                    @if(isset($expiringDocsCount) && $expiringDocsCount > 0)
                        <p class="mt-1 text-xs text-yellow-600">{{ $expiringDocsCount }} expiring soon</p>
                    @endif
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Actions -->
    @if($newJobsCount > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-bolt text-blue-500 mr-2"></i>
            Quick Actions
        </h2>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('driver.jobs.new') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200">
                <i class="fas fa-eye mr-2"></i>
                View New Jobs (<span id="new-jobs-quick-count">{{ $newJobsCount }}</span>)
            </a>
        </div>
    </div>
    @endif

    <!-- Driver Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold text-gray-900 mb-4">
            <i class="fas fa-user text-green-500 mr-2"></i>
            Driver Information
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Email:</span>
                    <span class="text-sm text-gray-900">{{ $driver->email }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Phone:</span>
                    <span class="text-sm text-gray-900">{{ $driver->phone }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Status:</span>
                    <span data-test="driver-status-badge" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ ucfirst($driver->status) }}
                    </span>
                </div>
            </div>
            <div class="space-y-3">
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Vehicle:</span>
                    <span class="text-sm text-gray-900">{{ $driver->vehicle_make }} {{ $driver->vehicle_model }}</span>
                </div>
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Plate:</span>
                    <span class="text-sm text-gray-900">{{ $driver->vehicle_plate }}</span>
                </div>
                @if($driver->rating)
                <div class="flex items-center">
                    <span class="text-sm font-medium text-gray-500 w-24">Rating:</span>
                    <span class="text-sm text-gray-900">{{ $driver->rating }}/5.0</span>
                    <div class="ml-2">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star text-{{ $i <= $driver->rating ? 'yellow' : 'gray' }}-400 text-xs"></i>
                        @endfor
                    </div>
                </div>
                @endif

                <!-- Availability controls -->
                <div class="mt-4">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Availability</h4>
                    <div class="flex items-center gap-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="driver_status" value="active" class="driver-status-radio" {{ $driver->status === 'active' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Active</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="driver_status" value="inactive" class="driver-status-radio" {{ $driver->status === 'inactive' ? 'checked' : '' }}>
                            <span class="ml-2 text-sm">Inactive</span>
                        </label>
                    </div>

                    <div id="availability-range" class="mt-3 grid grid-cols-1 md:grid-cols-2 gap-3" style="display: {{ $driver->status === 'inactive' ? 'grid' : 'none' }};">
                        <div>
                            <label class="text-xs text-gray-500">From</label>
                            <input type="datetime-local" id="unavailable_from" class="mt-1 block w-full border rounded p-2" value="{{ $driver->unavailable_from ? $driver->unavailable_from->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500">To</label>
                            <input type="datetime-local" id="unavailable_to" class="mt-1 block w-full border rounded p-2" value="{{ $driver->unavailable_to ? $driver->unavailable_to->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <button id="save-availability" class="px-4 py-2 bg-indigo-600 text-white rounded">Save Availability</button>
                        <span id="availability-status" class="ml-3 text-sm text-gray-600"></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh dashboard every 30 seconds if there are new jobs
    @if($newJobsCount > 0)
    setTimeout(() => {
        location.reload();
    }, 30000);
    @endif

    // Availability controls
    (function(){
        function showToast(msg){
            var el = document.createElement('div'); el.className='fixed bottom-6 right-6 bg-black text-white px-4 py-2 rounded shadow'; el.textContent=msg; document.body.appendChild(el); setTimeout(function(){ el.remove(); }, 3000);
        }

        var radios = document.querySelectorAll('.driver-status-radio');
        var range = document.getElementById('availability-range');
        var saveBtn = document.getElementById('save-availability');
        var statusSpan = document.getElementById('availability-status');
        var badge = document.querySelector('[data-test="driver-status-badge"]') || document.querySelector('.inline-flex.items-center.px-2');

        var availabilityTimer = null;
        var availabilityInterval = null;

        function formatDuration(ms){
            if (ms <= 0) return '00:00:00';
            var total = Math.floor(ms / 1000);
            var hours = Math.floor(total / 3600); total %= 3600;
            var minutes = Math.floor(total / 60); var seconds = total % 60;
            return [hours, minutes, seconds].map(function(n){ return String(n).padStart(2,'0'); }).join(':');
        }

        function updateRangeVisibility(){
            var val = document.querySelector('input[name="driver_status"]:checked').value;
            if (val === 'inactive') range.style.display = 'grid'; else range.style.display = 'none';
        }

        radios.forEach(function(r){ r.addEventListener('change', updateRangeVisibility); });
        updateRangeVisibility();

        function clearAvailabilityTimer(){
            if (availabilityInterval) { clearInterval(availabilityInterval); availabilityInterval = null; }
            if (availabilityTimer) { clearTimeout(availabilityTimer); availabilityTimer = null; }
            statusSpan.textContent = '';
        }

        function startAvailabilityTimer(untilIso){
            clearAvailabilityTimer();
            if (!untilIso) return;
            var until = new Date(untilIso);
            var now = new Date();
            if (until <= now) {
                // already passed — try to reactivate immediately
                reactivateNow();
                return;
            }

            function tick(){
                var diff = new Date(until) - new Date();
                if (diff <= 0) {
                    clearAvailabilityTimer();
                    statusSpan.textContent = 'Reactivating...';
                    reactivateNow();
                    return;
                }
                statusSpan.textContent = 'Remaining: ' + formatDuration(diff);
            }

            tick();
            availabilityInterval = setInterval(tick, 1000);

            // Fallback: ensure reactivate even if interval missed
            availabilityTimer = setTimeout(function(){
                clearAvailabilityTimer();
                statusSpan.textContent = 'Reactivating...';
                reactivateNow();
            }, Math.max(0, new Date(until) - new Date()));
        }

        function reactivateNow(){
            // POST to set status active
            fetch('{{ route('driver.availability.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ status: 'active', unavailable_from: null, unavailable_to: null })
            }).then(function(r){ return r.json(); }).then(function(json){
                if (json && json.success) {
                    showToast('Your availability has ended — reactivated');
                    // Clear timers and update UI based on server response
                    clearAvailabilityTimer();
                    try {
                        var badgeEl = document.querySelector('[data-test="driver-status-badge"]') || document.querySelector('.inline-flex.items-center.px-2.5');

                        if (badgeEl) {
                            badgeEl.textContent = (json.driver && json.driver.status) ? json.driver.status.charAt(0).toUpperCase() + json.driver.status.slice(1) : 'Active';
                            badgeEl.classList.remove('bg-red-100','text-red-800'); badgeEl.classList.add('bg-green-100','text-green-800');
                        }

                        // hide range inputs and clear fields
                        range.style.display = 'none';
                        var activeRadio = document.querySelector('input[name="driver_status"][value="active"]');
                        if (activeRadio) activeRadio.checked = true;
                        var fromInput = document.getElementById('unavailable_from'); if (fromInput) fromInput.value = '';
                        var toInput = document.getElementById('unavailable_to'); if (toInput) toInput.value = '';
                    } catch (e){ console.error('UI update after reactivation failed', e); }

                    // show a short reactivated status and then clear
                    statusSpan.textContent = 'Reactivated';
                    setTimeout(function(){ statusSpan.textContent = ''; }, 3000);
                } else {
                    // Server said no
                    var message = (json && json.message) ? json.message : 'Failed to reactivate automatically — please refresh';
                    showToast(message);
                    statusSpan.textContent = message;
                    setTimeout(function(){ statusSpan.textContent = ''; }, 5000);
                }
            }).catch(function(){ showToast('Failed to reactivate automatically — please refresh'); statusSpan.textContent = ''; });
        }

        // Start timer on page load if status is inactive and there is an until value
        (function initFromServer(){
            var currentStatus = '{{ $driver->status }}';
            var toInput = document.getElementById('unavailable_to');
            if (currentStatus === 'inactive' && toInput && toInput.value) {
                startAvailabilityTimer(toInput.value);
            }
        })();

        if (saveBtn) {
            saveBtn.addEventListener('click', function(e){
                e.preventDefault();
                var status = document.querySelector('input[name="driver_status"]:checked').value;
                var from = document.getElementById('unavailable_from').value;
                var to = document.getElementById('unavailable_to').value;

                if (status === 'inactive') {
                    if (!from || !to) { showToast('Please provide both From and To datetimes when marking inactive'); return; }
                    if (new Date(to) <= new Date(from)) { showToast('End time must be after start time'); return; }
                }

                saveBtn.disabled = true; statusSpan.textContent = 'Saving...';

                fetch('{{ route('driver.availability.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status, unavailable_from: from || null, unavailable_to: to || null })
                }).then(function(r){ return r.json(); }).then(function(json){
                    if (json && json.success) {
                        showToast('Availability updated');
                        // update status badge on page
                        try {
                            var badgeEl = document.querySelector('.inline-flex.items-center.px-2.5');
                            if (badgeEl) {
                                badgeEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                                if (status === 'active') {
                                    badgeEl.classList.remove('bg-red-100','text-red-800'); badgeEl.classList.add('bg-green-100','text-green-800');
                                } else {
                                    badgeEl.classList.remove('bg-green-100','text-green-800'); badgeEl.classList.add('bg-red-100','text-red-800');
                                }
                            }
                        } catch (e){}

                        // If inactive, start client timer using 'to' value; otherwise clear
                        if (status === 'inactive' && to) {
                            startAvailabilityTimer(to);
                        } else {
                            clearAvailabilityTimer();
                        }
                    } else {
                        showToast((json && json.message) ? json.message : 'Failed to update availability');
                    }
                }).catch(function(err){ console.error(err); showToast('Error saving availability'); }).finally(function(){ saveBtn.disabled = false; statusSpan.textContent = ''; });
            });
        }
    })();

    // ========================================
    // GPS Location Sharing
    // ========================================
    (function(){
        let locationWatchId = null;
        let locationInterval = null;

        function startLocationSharing() {
            if (!navigator.geolocation) {
                console.log('Geolocation not supported by browser');
                return;
            }

            console.log('Starting GPS location sharing from dashboard...');

            // Get initial position
            navigator.geolocation.getCurrentPosition(
                sendLocationUpdate,
                handleLocationError,
                { enableHighAccuracy: true, timeout: 10000 }
            );

            // Watch for position changes
            locationWatchId = navigator.geolocation.watchPosition(
                sendLocationUpdate,
                handleLocationError,
                { enableHighAccuracy: true, maximumAge: 5000 }
            );

            // Also send location every 15 seconds as backup
            locationInterval = setInterval(() => {
                navigator.geolocation.getCurrentPosition(
                    sendLocationUpdate,
                    handleLocationError,
                    { enableHighAccuracy: true, timeout: 10000 }
                );
            }, 15000);
        }

        function stopLocationSharing() {
            if (locationWatchId !== null) {
                navigator.geolocation.clearWatch(locationWatchId);
                locationWatchId = null;
            }
            if (locationInterval !== null) {
                clearInterval(locationInterval);
                locationInterval = null;
            }
            console.log('Location sharing stopped');
        }

        function sendLocationUpdate(position) {
            const data = {
                latitude: position.coords.latitude,
                longitude: position.coords.longitude,
                accuracy: position.coords.accuracy,
                heading: position.coords.heading,
                speed: position.coords.speed
            };

            fetch('{{ route("driver.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    console.log('Location updated:', data.latitude.toFixed(4), data.longitude.toFixed(4));
                }
            })
            .catch(error => {
                console.error('Failed to send location:', error);
            });
        }

        function handleLocationError(error) {
            switch(error.code) {
                case error.PERMISSION_DENIED:
                    console.error('Location permission denied');
                    break;
                case error.POSITION_UNAVAILABLE:
                    console.error('Location unavailable');
                    break;
                case error.TIMEOUT:
                    console.error('Location request timeout');
                    break;
            }
        }

        // Start location sharing when page loads
        setTimeout(startLocationSharing, 1000);

        // Stop on page unload
        window.addEventListener('beforeunload', stopLocationSharing);
    })();
</script>
@endsection