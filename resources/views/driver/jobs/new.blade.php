@extends('driver.layouts.app')

@section('title', 'New Jobs')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">New Jobs</h1>
            <p class="text-gray-600">Jobs assigned to you that need a response</p>
        </div>
        <a href="{{ route('driver.dashboard') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
            <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
        </a>
    </div>

    @if($jobs->count() > 0)
        <!-- Jobs List -->
        <div class="space-y-4">
            @foreach($jobs as $job)
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <div class="flex flex-col md:flex-row md:items-center justify-between space-y-4 md:space-y-0">
                    <div class="flex-1">
                        <!-- Job Header -->
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Booking #{{ $job->id }}</h3>
                                <p class="text-sm text-gray-600">{{ $job->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $job->status->name ?? 'Confirmed' }}
                            </span>
                        </div>

                        <!-- Trip Details -->
                        <div class="space-y-3">
                            <!-- Pickup Date & Time - Prominent Display -->
                            @if($job->pickup_date && $job->pickup_time)
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-alt text-blue-500 mr-3 text-lg"></i>
                                    <div>
                                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Pickup Date & Time</p>
                                        <p class="text-lg font-bold text-blue-900">
                                            {{ \Carbon\Carbon::parse($job->pickup_date)->format('M d, Y') }}
                                            <span class="text-blue-600">at {{ $job->pickup_time }}</span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @elseif($job->scheduled_at)
                            <div class="bg-blue-50 rounded-lg p-3 border-l-4 border-blue-500">
                                <div class="flex items-center">
                                    <i class="fas fa-clock text-blue-500 mr-3 text-lg"></i>
                                    <div>
                                        <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Scheduled Pickup</p>
                                        <p class="text-lg font-bold text-blue-900">{{ \Carbon\Carbon::parse($job->scheduled_at)->format('M d, Y \a\t H:i') }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <div class="space-y-2">
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-map-marker-alt text-green-500 mr-2 w-4"></i>
                                    <span class="font-medium">From:</span>
                                    <span class="ml-2 text-gray-700">{{ $job->pickup_address }}</span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-map-marker-alt text-red-500 mr-2 w-4"></i>
                                    <span class="font-medium">To:</span>
                                    <span class="ml-2 text-gray-700">{{ $job->dropoff_address }}</span>
                                </div>
                                @if($job->passengers_count)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-users text-purple-500 mr-2 w-4"></i>
                                    <span class="font-medium">Passengers:</span>
                                    <span class="ml-2 text-gray-700">{{ $job->passengers_count }}</span>
                                </div>
                                @endif
                                @if($job->total_price)
                                <div class="flex items-center text-sm">
                                    <i class="fas fa-euro-sign text-green-600 mr-2 w-4"></i>
                                    <span class="font-medium">Price:</span>
                                    <span class="ml-2 text-gray-700">€{{ number_format($job->total_price, 2) }}</span>
                                    @if($job->driver_price)
                                    <span class="ml-2 text-sm text-gray-500">(Driver: €{{ number_format($job->driver_price, 2) }})</span>
                                    @endif
                                </div>
                                @endif
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

                    <!-- Action Buttons -->
                    <div class="flex flex-col space-y-3 md:ml-6">
                        <button
                            onclick="acceptJob({{ $job->id }})"
                            class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200"
                        >
                            <i class="fas fa-check mr-2"></i>Accept
                        </button>
                        <button
                            onclick="declineJob({{ $job->id }})"
                            class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-lg font-medium transition duration-200"
                        >
                            <i class="fas fa-times mr-2"></i>Decline
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($jobs->hasPages())
        <div class="flex justify-center">
            {{ $jobs->links() }}
        </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <i class="fas fa-inbox text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No New Jobs</h3>
            <p class="text-gray-600">You don't have any new jobs at the moment. Check back later!</p>
        </div>
    @endif
</div>
@endsection