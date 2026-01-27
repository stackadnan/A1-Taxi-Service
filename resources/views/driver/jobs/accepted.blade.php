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
        <div class="space-y-4">
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
                                <span class="ml-2 text-gray-700">{{ $job->pickup_address }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <i class="fas fa-map-marker-alt text-red-500 mr-2 w-4"></i>
                                <span class="font-medium">To:</span>
                                <span class="ml-2 text-gray-700">{{ $job->dropoff_address }}</span>
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
                    <div class="text-center md:ml-6">
                        <div class="bg-orange-100 rounded-lg p-4">
                            <i class="fas fa-hourglass-half text-orange-500 text-2xl mb-2"></i>
                            <p class="text-sm font-medium text-orange-800">In Progress</p>
                            <p class="text-xs text-orange-600">Complete this job</p>
                        </div>
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
            <i class="fas fa-clipboard-check text-gray-400 text-6xl mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">No Accepted Jobs</h3>
            <p class="text-gray-600">You don't have any accepted jobs at the moment.</p>
        </div>
    @endif
</div>
@endsection