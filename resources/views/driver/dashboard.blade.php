@extends('driver.layouts.app')

@section('title', 'Driver Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl shadow-lg px-6 py-5 flex items-center gap-4">
        <div class="w-12 h-12 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white/30 shrink-0">
            <i class="fas fa-user text-white text-xl"></i>
        </div>
        <div>
            <h1 class="text-xl font-bold text-white leading-tight">Welcome back, {{ $driver->name }}!</h1>
            <p class="text-indigo-200 text-sm mt-0.5">Here's your job overview for today</p>
        </div>
    </div>

    <!-- Stats Cards Grid -->
    @php
        $docCount     = $expiredDocsCount ?? 0;
        $expiringCount = $expiringDocsCount ?? 0;
        $allClear     = $docCount === 0 && $expiringCount === 0;
        $docTheme     = $docCount > 0 ? 'docs-danger' : ($expiringCount > 0 ? 'docs-warning' : 'docs-clear');
        $docIcon      = $allClear ? 'fa-shield-alt' : ($docCount > 0 ? 'fa-exclamation-triangle' : 'fa-file-alt');
        $docLabel     = $allClear ? 'All Documents Clear' : 'Expiring Documents';
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">

        <!-- New Jobs Card -->
        <a href="{{ route('driver.jobs.new') }}" class="block">
            <div class="stat-card new-jobs rounded-2xl p-5 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="stat-icon-wrap">
                        <i class="fas fa-bell text-white text-2xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">New Jobs</p>
                    <p id="new-jobs-count" class="text-4xl font-extrabold text-white leading-none">{{ $newJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Accepted Jobs Card -->
        <a href="{{ route('driver.jobs.accepted') }}" class="block">
            <div class="stat-card accepted-jobs rounded-2xl p-5 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="stat-icon-wrap">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">Accepted</p>
                    <p id="accepted-jobs-count" class="text-4xl font-extrabold text-white leading-none">{{ $acceptedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Completed Jobs Card -->
        <a href="{{ route('driver.jobs.completed') }}" class="block">
            <div class="stat-card completed-jobs rounded-2xl p-5 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="stat-icon-wrap">
                        <i class="fas fa-trophy text-white text-2xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">Completed</p>
                    <p id="completed-jobs-count" class="text-4xl font-extrabold text-white leading-none">{{ $completedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Declined Jobs Card -->
        <a href="{{ route('driver.jobs.declined') }}" class="block">
            <div class="stat-card declined-jobs rounded-2xl p-5 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="stat-icon-wrap">
                        <i class="fas fa-times-circle text-white text-2xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">Declined</p>
                    <p id="declined-jobs-count" class="text-4xl font-extrabold text-white leading-none">{{ $declinedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Documents Card -->
        <a href="{{ route('driver.documents.expired') }}" class="block">
            <div class="stat-card {{ $docTheme }} rounded-2xl p-5 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <div class="stat-icon-wrap">
                        <i class="fas {{ $docIcon }} text-white text-2xl"></i>
                    </div>
                    <p class="text-white/80 text-xs font-semibold uppercase tracking-widest mb-1">{{ $docLabel }}</p>
                    @if(!$allClear)
                        <p id="expired-docs-count" class="text-4xl font-extrabold text-white leading-none">{{ $docCount ?: $expiringCount }}</p>
                        @if($docCount > 0 && $expiringCount > 0)
                            <p class="mt-1 text-xs text-white/70">+{{ $expiringCount }} expiring soon</p>
                        @endif
                    @endif
                </div>
            </div>
        </a>

    </div>

    <!-- Quick Actions -->
    @if($newJobsCount > 0)
    <div class="bg-white rounded-2xl shadow p-5 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                <i class="fas fa-bolt text-indigo-500"></i>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400">Quick Action</p>
                <p class="text-sm font-semibold text-gray-800">You have new jobs waiting</p>
            </div>
        </div>
        <a href="{{ route('driver.jobs.new') }}" class="shrink-0 flex items-center gap-2 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white px-5 py-2.5 rounded-xl font-semibold text-sm shadow transition-all">
            <i class="fas fa-eye"></i>
            View Jobs&nbsp;<span class="bg-white/25 rounded-full px-2 py-0.5 text-xs font-bold" id="new-jobs-quick-count">{{ $newJobsCount }}</span>
        </a>
    </div>
    @endif

    <!-- Driver Info -->
    <div class="bg-white rounded-2xl shadow-md overflow-hidden">
        <!-- Card Header with gradient -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-5 flex items-center gap-4">
            <div class="w-14 h-14 rounded-full bg-white/20 flex items-center justify-center ring-2 ring-white/40 shrink-0">
                <i class="fas fa-user text-white text-2xl"></i>
            </div>
            <div>
                <h2 class="text-white text-xl font-bold leading-tight">{{ $driver->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span data-test="driver-status-badge" class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $driver->status === 'active' ? 'bg-green-400/30 text-green-100 ring-1 ring-green-300/50' : 'bg-red-400/30 text-red-100 ring-1 ring-red-300/50' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $driver->status === 'active' ? 'bg-green-300' : 'bg-red-300' }} inline-block"></span>
                        {{ ucfirst($driver->status) }}
                    </span>
                    @if($driver->rating)
                        <span class="flex items-center gap-1 text-yellow-300 text-xs font-medium">
                            <i class="fas fa-star text-yellow-300 text-xs"></i>
                            {{ number_format($driver->rating, 1) }}/5.0
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Contact & Info Column -->
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Contact Details</p>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-lg bg-indigo-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-envelope text-indigo-500 text-sm"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs text-gray-400">Email</p>
                        <p class="text-sm font-medium text-gray-800 truncate">{{ $driver->email }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-lg bg-green-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-phone text-green-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Phone</p>
                        <p class="text-sm font-medium text-gray-800">{{ $driver->phone }}</p>
                    </div>
                </div>

                @if($driver->rating)
                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-lg bg-yellow-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-star text-yellow-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Rating</p>
                        <div class="flex items-center gap-1.5">
                            <span class="text-sm font-medium text-gray-800">{{ number_format($driver->rating, 1) }}/5.0</span>
                            <div class="flex">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-{{ $i <= $driver->rating ? 'yellow' : 'gray' }}-400 text-xs"></i>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Vehicle & Availability Column -->
            <div class="space-y-4">
                <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-2">Vehicle & Availability</p>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-car text-blue-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Vehicle</p>
                        <p class="text-sm font-medium text-gray-800">{{ $driver->vehicle_make }} {{ $driver->vehicle_model }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                    <div class="w-9 h-9 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                        <i class="fas fa-id-card text-purple-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs text-gray-400">Number Plate</p>
                        <p class="text-sm font-bold text-gray-800 tracking-widest uppercase">{{ $driver->vehicle_plate }}</p>
                    </div>
                </div>

                <!-- Availability Toggle -->
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gray-400 mb-3">Set Availability</p>
                    <div class="flex items-center gap-3 mb-3">
                        <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer px-3 py-2 rounded-lg border-2 transition-all driver-avail-btn {{ $driver->status === 'active' ? 'border-green-500 bg-green-50 text-green-700' : 'border-gray-200 bg-white text-gray-500' }}">
                            <input type="radio" name="driver_status" value="active" class="driver-status-radio sr-only" {{ $driver->status === 'active' ? 'checked' : '' }}>
                            <i class="fas fa-circle text-xs {{ $driver->status === 'active' ? 'text-green-500' : 'text-gray-300' }}"></i>
                            <span class="text-sm font-medium">Active</span>
                        </label>
                        <label class="flex-1 flex items-center justify-center gap-2 cursor-pointer px-3 py-2 rounded-lg border-2 transition-all driver-avail-btn {{ $driver->status === 'inactive' ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 bg-white text-gray-500' }}">
                            <input type="radio" name="driver_status" value="inactive" class="driver-status-radio sr-only" {{ $driver->status === 'inactive' ? 'checked' : '' }}>
                            <i class="fas fa-circle text-xs {{ $driver->status === 'inactive' ? 'text-red-400' : 'text-gray-300' }}"></i>
                            <span class="text-sm font-medium">Inactive</span>
                        </label>
                    </div>

                    <div id="availability-range" class="grid grid-cols-1 gap-3 mb-3" style="display: {{ $driver->status === 'inactive' ? 'grid' : 'none' }};">
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Unavailable From</label>
                            <input type="datetime-local" id="unavailable_from" class="mt-1 block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" value="{{ $driver->unavailable_from ? $driver->unavailable_from->format('Y-m-d\TH:i') : '' }}">
                        </div>
                        <div>
                            <label class="text-xs text-gray-500 font-medium">Unavailable To</label>
                            <input type="datetime-local" id="unavailable_to" class="mt-1 block w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" value="{{ $driver->unavailable_to ? $driver->unavailable_to->format('Y-m-d\TH:i') : '' }}">
                        </div>
                    </div>

                    <button id="save-availability" class="w-full py-2.5 bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-600 hover:to-purple-700 text-white text-sm font-semibold rounded-lg shadow transition-all">
                        <i class="fas fa-save mr-2"></i>Save Availability
                    </button>
                    <p id="availability-status" class="mt-2 text-xs text-center text-gray-500"></p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Auto-refresh disabled - using real-time SSE updates instead
    @if($newJobsCount > 0)
    // Real-time updates via SSE - no need for periodic refresh
    console.log('Dashboard has {{ $newJobsCount }} new jobs - using SSE for real-time updates');
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

        function updateAvailBtnStyles(){
            var val = document.querySelector('input[name="driver_status"]:checked').value;
            document.querySelectorAll('.driver-avail-btn').forEach(function(btn){
                var radio = btn.querySelector('input[type="radio"]');
                var icon = btn.querySelector('.fas.fa-circle');
                if (!radio) return;
                if (radio.value === 'active') {
                    if (val === 'active') {
                        btn.className = btn.className.replace(/border-gray-200|bg-white|text-gray-500/g,'').trim();
                        btn.classList.add('border-green-500','bg-green-50','text-green-700');
                        btn.classList.remove('border-gray-200','bg-white','text-gray-500');
                        if (icon) { icon.classList.remove('text-gray-300'); icon.classList.add('text-green-500'); }
                    } else {
                        btn.classList.remove('border-green-500','bg-green-50','text-green-700');
                        btn.classList.add('border-gray-200','bg-white','text-gray-500');
                        if (icon) { icon.classList.remove('text-green-500'); icon.classList.add('text-gray-300'); }
                    }
                } else {
                    if (val === 'inactive') {
                        btn.classList.remove('border-gray-200','bg-white','text-gray-500');
                        btn.classList.add('border-red-400','bg-red-50','text-red-700');
                        if (icon) { icon.classList.remove('text-gray-300'); icon.classList.add('text-red-400'); }
                    } else {
                        btn.classList.remove('border-red-400','bg-red-50','text-red-700');
                        btn.classList.add('border-gray-200','bg-white','text-gray-500');
                        if (icon) { icon.classList.remove('text-red-400'); icon.classList.add('text-gray-300'); }
                    }
                }
            });
        }

        radios.forEach(function(r){ r.addEventListener('change', function(){ updateRangeVisibility(); updateAvailBtnStyles(); }); });
        updateRangeVisibility();
        updateAvailBtnStyles();

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