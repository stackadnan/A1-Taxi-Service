@extends('driver.layouts.app')

@section('title', 'Accepted Jobs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Accepted Jobs</h1>
            <p class="text-gray-600">Jobs you have accepted and need to complete</p>
        </div>
        <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    @if($jobs->count() > 0)
        <!-- Jobs List -->
        <div id="accepted-jobs-container">
          <div class="space-y-4" id="accepted-jobs-list">
            @foreach($jobs as $job)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-orange-500">
                <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                    <div class="flex-1">
                        <!-- Job Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Booking #{{ $job->id }}</h3>
                                <p class="text-sm text-gray-600">{{ $job->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                Accepted
                            </span>
                        </div>

                        <!-- Trip Details -->
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-green-500 mr-2 w-4"></i>
                                <span class="font-medium">From:</span>
                                <span class="ml-2 text-gray-700">
                                  @if($job->pickup_address)
                                    <a href="#" class="js-open-directions inline-flex items-center gap-1 text-indigo-600 hover:underline" data-destination="{{ $job->pickup_address }}" data-role="pickup" title="Open in Maps" aria-label="Open directions to pickup location">{{ $job->pickup_address }}
                                      <svg class="ml-1 w-4 h-4 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1112 6.5 2.5 2.5 0 0112 11.5z"/></svg>
                                    </a>
                                  @else
                                    -
                                  @endif
                                </span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2 w-4"></i>
                                <span class="font-medium">To:</span>
                                <span class="ml-2 text-gray-700">
                                  @if($job->dropoff_address)
                                    <a href="#" class="js-open-directions inline-flex items-center gap-1 text-indigo-600 hover:underline" data-destination="{{ $job->dropoff_address }}" data-role="dropoff" title="Open in Maps" aria-label="Open directions to dropoff location">{{ $job->dropoff_address }}
                                      <svg class="ml-1 w-4 h-4 text-indigo-400" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5A2.5 2.5 0 1112 6.5 2.5 2.5 0 0112 11.5z"/></svg>
                                    </a>
                                  @else
                                    -
                                  @endif
                                </span>
                            </div>
                            @if($job->scheduled_at)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-clock text-blue-500 mr-2 w-4"></i>
                                <span class="font-medium">Pickup:</span>
                                <span class="ml-2 text-gray-700">{{ \Carbon\Carbon::parse($job->scheduled_at)->format('M d, Y H:i') }}</span>
                            </div>
                            @endif
                            @if($job->passengers_count)
                            <div class="flex items-center text-sm">
                                <i class="fas fa-users text-purple-500 mr-2 w-4"></i>
                                <span class="font-medium">Passengers:</span>
                                <span class="ml-2 text-gray-700">{{ $job->passengers_count }}</span>
                            </div>
                            @endif
                            <div class="flex items-center text-sm">
                                <i class="fas fa-euro-sign text-green-600 mr-2 w-4"></i>
                                <span class="font-medium">Price:</span>
                                <span class="ml-2 text-gray-700">
                                  @if($job->driver_price)
                                    €{{ number_format($job->driver_price, 2) }}
                                  @else
                                    -
                                  @endif
                                </span>
                            </div> 
                        </div>

                        @if($job->message_to_driver)
                        <div class="mt-3 p-3 bg-gray-50 rounded">
                            <p class="text-sm text-gray-700">
                                <i class="fas fa-comment text-gray-400 mr-2"></i>
                                <strong>Notes:</strong> {{ $job->message_to_driver }}
                            </p>
                        </div>
                        @endif
                    </div>

                    <!-- Status Info -->
                    <div class="text-center md:ml-6 space-y-2">
                        @if($job->status && $job->status->name === 'pob')
                            <div class="bg-yellow-100 rounded-lg p-4">
                                <i class="fas fa-clipboard-check text-yellow-500 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-yellow-800">POB Status</p>
                                <p class="text-xs text-yellow-600">Ready to complete</p>
                            </div>
                        @else
                            <div class="bg-orange-100 rounded-lg p-4">
                                <i class="fas fa-hourglass-half text-orange-500 text-2xl mb-2"></i>
                                <p class="text-sm font-medium text-orange-800">In Progress</p>
                                <p class="text-xs text-orange-600">Mark as POB first</p>
                            </div>
                        @endif
                        <div class="space-y-2">
                            @if($job->status && $job->status->name === 'pob')
                                <button 
                                    onclick="markAsCompleted({{ $job->id }})" 
                                    class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                                    id="completed-btn-{{ $job->id }}"
                                >
                                    <i class="fas fa-flag-checkered mr-2"></i>Mark as Completed
                                </button>
                            @else
                                <button 
                                    onclick="markAsPOB({{ $job->id }})" 
                                    class="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition-colors"
                                    id="pob-btn-{{ $job->id }}"
                                >
                                    <i class="fas fa-check mr-2"></i>Mark as POB
                                </button>
                            @endif
                            <a href="{{ route('driver.jobs.show', $job) }}" class="block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-center">View Full Details</a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
          </div>

          <!-- Pagination -->
          @if($jobs->hasPages())
          <div class="flex justify-center" id="accepted-jobs-pagination">
              {{ $jobs->links() }}
          </div>
          @endif
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-clipboard-check text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Accepted Jobs</h3>
            <p class="text-gray-600">You don't have any accepted jobs at the moment.</p>
        </div>
    @endif
</div>
@endsection

<script>
// POB functionality - Global scope
window.markAsPOB = function(bookingId) {
    const btn = document.getElementById(`pob-btn-${bookingId}`);
    if (!btn) {
        console.error('POB button not found for booking:', bookingId);
        return;
    }
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Marking as POB...';
    
    fetch('{{ route("driver.jobs.pob", ":bookingId") }}'.replace(':bookingId', bookingId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Job marked as POB successfully! You can now complete it.', 'success');
            
            // Update the button to "Mark as Completed"
            const jobCard = btn.closest('.bg-white');
            if (jobCard) {
                // Find the button container and update it
                const buttonContainer = btn.parentElement;
                buttonContainer.innerHTML = `
                    <button 
                        onclick="markAsCompleted(${bookingId})" 
                        class="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition-colors"
                        id="completed-btn-${bookingId}"
                    >
                        <i class="fas fa-flag-checkered mr-2"></i>Mark as Completed
                    </button>
                    <a href="{{ route('driver.jobs.show', ':id') }}".replace(':id', bookingId) class="block px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 text-center mt-2">View Full Details</a>
                `;
                
                // Update status indicator
                const statusDiv = jobCard.querySelector('.bg-orange-100');
                if (statusDiv) {
                    statusDiv.className = 'bg-yellow-100 rounded-lg p-4';
                    statusDiv.innerHTML = `
                        <i class="fas fa-clipboard-check text-yellow-500 text-2xl mb-2"></i>
                        <p class="text-sm font-medium text-yellow-800">POB Status</p>
                        <p class="text-xs text-yellow-600">Ready to complete</p>
                    `;
                }
            }
            
            // Update counts in dashboard
            if (data.counts && window.updateJobCounts) {
                window.updateJobCounts(data.counts);
            }
        } else {
            showNotification(data.error || 'Failed to mark job as POB', 'error');
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Mark as POB';
        }
    })
    .catch(error => {
        console.error('POB Error:', error);
        showNotification('Failed to mark job as POB', 'error');
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Mark as POB';
    });
};

// Completed functionality - Global scope
window.markAsCompleted = function(bookingId) {
    const btn = document.getElementById(`completed-btn-${bookingId}`);
    if (!btn) {
        console.error('Completed button not found for booking:', bookingId);
        return;
    }
    
    // Disable button and show loading state
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Completing Job...';
    
    fetch('{{ route("driver.jobs.complete", ":bookingId") }}'.replace(':bookingId', bookingId), {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showNotification('Job completed successfully!', 'success');
            
            // Remove the job from the accepted jobs list
            const jobCard = btn.closest('.bg-white');
            if (jobCard) {
                jobCard.style.transition = 'opacity 0.3s';
                jobCard.style.opacity = '0';
                setTimeout(() => {
                    jobCard.remove();
                    // Check if no more jobs
                    const jobsList = document.getElementById('accepted-jobs-list');
                    if (jobsList && jobsList.children.length === 0) {
                        location.reload(); // Reload to show empty state
                    }
                }, 300);
            }
            
            // Update counts in dashboard
            if (data.counts && window.updateJobCounts) {
                window.updateJobCounts(data.counts);
            }
        } else {
            showNotification(data.error || 'Failed to complete job', 'error');
            // Reset button
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-flag-checkered mr-2"></i>Mark as Completed';
        }
    })
    .catch(error => {
        console.error('Complete Error:', error);
        showNotification('Failed to complete job', 'error');
        // Reset button
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-flag-checkered mr-2"></i>Mark as Completed';
    });
};
</script>

@push('scripts')
<script>
// POB functionality
function markAsPOB(bookingId) {
    // Call the global function
    window.markAsPOB(bookingId);
}

// Completed functionality
function markAsCompleted(bookingId) {
    // Call the global function
    window.markAsCompleted(bookingId);
}

// Notification system
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg transition-all duration-300 ${
        type === 'success' ? 'bg-green-500 text-white' : 
        type === 'error' ? 'bg-red-500 text-white' : 
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center">
            <i class="fas ${
                type === 'success' ? 'fa-check-circle' : 
                type === 'error' ? 'fa-exclamation-circle' : 
                'fa-info-circle'
            } mr-2"></i>
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.opacity = '0';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

// SSE for real-time updates
let eventSource;

function initializeSSE() {
    if (typeof(EventSource) !== "undefined") {
        eventSource = new EventSource('{{ route("driver.notifications.stream") }}');
        
        eventSource.onopen = function() {
            console.log('SSE connection opened');
        };
        
        eventSource.onmessage = function(event) {
            try {
                const data = JSON.parse(event.data);
                if (data.type === 'booking_updated' && data.action === 'pob') {
                    showNotification('Job status updated to POB!', 'info');
                }
            } catch (e) {
                console.error('Error parsing SSE data:', e);
            }
        };
        
        eventSource.onerror = function(event) {
            console.error('SSE error:', event);
            // Attempt to reconnect after 5 seconds
            setTimeout(() => {
                if (eventSource.readyState === EventSource.CLOSED) {
                    initializeSSE();
                }
            }, 5000);
        };
    } else {
        console.log('Your browser does not support server-sent events.');
    }
}

// Global function to update job counts
window.updateJobCounts = function(counts) {
    try {
        const elements = {
            'new-jobs-count': counts.new,
            'accepted-jobs-count': counts.accepted,
            'completed-jobs-count': counts.completed,
            'declined-jobs-count': counts.declined
        };

        Object.keys(elements).forEach(id => {
            const element = document.getElementById(id);
            if (element && elements[id] !== undefined) {
                element.textContent = elements[id];
            }
        });

        console.log('Job counts updated:', counts);
    } catch (error) {
        console.error('Error updating job counts:', error);
    }
};\n\n// Initialize SSE when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeSSE();
});

// Cleanup SSE when page unloads
window.addEventListener('beforeunload', function() {
    if (eventSource) {
        eventSource.close();
    }
    stopLocationSharing();
});

// ========================================
// GPS Location Sharing
// ========================================
let locationWatchId = null;
let locationInterval = null;
let isLocationSharingEnabled = false;

// Check if driver has active POB jobs and start location sharing
function checkAndStartLocationSharing() {
    // Check if there are any jobs with POB status on the page
    const hasPobJobs = document.querySelector('.bg-yellow-100');
    
    if (hasPobJobs && !isLocationSharingEnabled) {
        startLocationSharing();
    }
}

function startLocationSharing() {
    if (!navigator.geolocation) {
        console.log('Geolocation not supported by browser');
        return;
    }

    if (isLocationSharingEnabled) {
        console.log('Location sharing already enabled');
        return;
    }

    isLocationSharingEnabled = true;
    console.log('Starting GPS location sharing...');

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

    // Also send location every 10 seconds as backup
    locationInterval = setInterval(() => {
        navigator.geolocation.getCurrentPosition(
            sendLocationUpdate,
            handleLocationError,
            { enableHighAccuracy: true, timeout: 10000 }
        );
    }, 10000);
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
    isLocationSharingEnabled = false;
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
            console.log('Location updated:', data.latitude, data.longitude);
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

// Initialize location sharing when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Start location sharing for all accepted jobs
    // This allows admin to track driver even before POB
    setTimeout(startLocationSharing, 1000);
});


</script>
@endpush