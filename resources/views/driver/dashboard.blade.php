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
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- New Jobs Card -->
        <a href="{{ route('driver.jobs.new') }}" class="block">
            <div class="stat-card new-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-bell text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">New Jobs</h3>
                    <p class="text-3xl font-bold">{{ $newJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Accepted Jobs Card -->
        <a href="{{ route('driver.jobs.accepted') }}" class="block">
            <div class="stat-card accepted-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-check-circle text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Accepted Jobs</h3>
                    <p class="text-3xl font-bold">{{ $acceptedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Completed Jobs Card -->
        <a href="{{ route('driver.jobs.completed') }}" class="block">
            <div class="stat-card completed-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-trophy text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Completed Jobs</h3>
                    <p class="text-3xl font-bold">{{ $completedJobsCount }}</p>
                </div>
            </div>
        </a>

        <!-- Declined Jobs Card -->
        <a href="{{ route('driver.jobs.declined') }}" class="block">
            <div class="stat-card declined-jobs rounded-lg p-6 text-center job-card">
                <div class="flex flex-col items-center justify-center h-full">
                    <i class="fas fa-times-circle text-3xl mb-3 opacity-90"></i>
                    <h3 class="text-lg font-semibold mb-2">Jobs Declined</h3>
                    <p class="text-3xl font-bold">{{ $declinedJobsCount }}</p>
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
                View New Jobs ({{ $newJobsCount }})
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
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $driver->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
</script>
@endsection