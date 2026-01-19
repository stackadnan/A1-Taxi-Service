<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatus;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // quick status ids
        $statuses = BookingStatus::pluck('id', 'name')->toArray();

        $pickupToday = Booking::whereDate('pickup_date', $today)->count();

        $newBookings = Booking::where('status_id', $statuses['new'] ?? 0)->count();
        $inProgress = Booking::where('status_id', $statuses['in_progress'] ?? 0)->count();
        $confirmed = Booking::where('status_id', $statuses['confirmed'] ?? 0)->count();
        $completed = Booking::where('status_id', $statuses['completed'] ?? 0)->count();

        // this month
        $startMonth = $today->copy()->startOfMonth();
        $endMonth = $today->copy()->endOfMonth();

        $totalThisMonth = Booking::whereBetween('created_at', [$startMonth, $endMonth])->count();
        $confirmedThisMonth = Booking::whereBetween('created_at', [$startMonth, $endMonth])->where('status_id', $statuses['confirmed'] ?? 0)->count();
        $completedThisMonth = Booking::whereBetween('created_at', [$startMonth, $endMonth])->where('status_id', $statuses['completed'] ?? 0)->count();
        $cancelledThisMonth = Booking::whereBetween('created_at', [$startMonth, $endMonth])->where('status_id', $statuses['cancelled'] ?? 0)->count();

        // last month
        $lastStart = $startMonth->copy()->subMonth()->startOfMonth();
        $lastEnd = $startMonth->copy()->subMonth()->endOfMonth();

        $totalLastMonth = Booking::whereBetween('created_at', [$lastStart, $lastEnd])->count();
        $confirmedLastMonth = Booking::whereBetween('created_at', [$lastStart, $lastEnd])->where('status_id', $statuses['confirmed'] ?? 0)->count();
        $completedLastMonth = Booking::whereBetween('created_at', [$lastStart, $lastEnd])->where('status_id', $statuses['completed'] ?? 0)->count();
        $cancelledLastMonth = Booking::whereBetween('created_at', [$lastStart, $lastEnd])->where('status_id', $statuses['cancelled'] ?? 0)->count();

        // Load recent broadcasts (only those scheduled at or before now)
        $broadcasts = \App\Models\Broadcast::where(function($q){
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            })->orderBy('created_at','desc')->limit(5)->get();

        // Booking sections for dashboard
        $sections = [
            'new' => 'New Bookings',
            'pending' => 'Pending Jobs',
            'confirmed' => 'Confirmed Jobs',
            'completed' => 'Completed Jobs',
            'cancelled' => 'Canceled Jobs',
            'junk' => 'Junk',
        ];

        // Count bookings for each section
        $counts = [];
        foreach (array_keys($sections) as $key) {
            $counts[$key] = $this->countForSection($key);
        }

        // Active section (default to 'new')
        $active = request()->get('section', 'new');
        if (!array_key_exists($active, $sections)) {
            $active = 'new';
        }

        // Build query for active section
        $query = Booking::with('status');

        switch ($active) {
            case 'new':
                $query->whereHas('status', function($q){ $q->where('name', 'new'); });
                break;
            case 'pending':
                $query->whereHas('status', function($q){ $q->whereIn('name', ['pending','in_progress']); });
                break;
            case 'confirmed':
                $query->whereHas('status', function($q){ $q->where('name', 'confirmed'); });
                break;
            case 'completed':
                $query->whereHas('status', function($q){ $q->where('name', 'completed'); });
                break;
            case 'cancelled':
                $query->whereHas('status', function($q){ $q->where('name', 'cancelled'); });
                break;
            case 'junk':
                $query->where('meta->junk', true);
                break;
        }

        $bookings = $query->orderBy('pickup_date', 'desc')->orderBy('pickup_time', 'desc')->paginate(15);

        // If partial request, return only the bookings list
        if (request()->ajax() || request()->get('partial')) {
            return view('admin.bookings._list', compact('bookings'));
        }

        return view('admin.dashboard', compact(
            'pickupToday','newBookings','inProgress','confirmed','completed',
            'totalThisMonth','confirmedThisMonth','completedThisMonth','cancelledThisMonth',
            'totalLastMonth','confirmedLastMonth','completedLastMonth','cancelledLastMonth',
            'broadcasts',
            'sections', 'active', 'counts', 'bookings'
        ));
    }

    protected function countForSection(string $key): int
    {
        switch ($key) {
            case 'new':
                return Booking::whereHas('status', function($q){ $q->where('name', 'new'); })->count();
            case 'pending':
                return Booking::whereHas('status', function($q){ $q->whereIn('name', ['pending','in_progress']); })->count();
            case 'confirmed':
                return Booking::whereHas('status', function($q){ $q->where('name', 'confirmed'); })->count();
            case 'completed':
                return Booking::whereHas('status', function($q){ $q->where('name', 'completed'); })->count();
            case 'cancelled':
                return Booking::whereHas('status', function($q){ $q->where('name', 'cancelled'); })->count();
            case 'junk':
                return Booking::where('meta->junk', true)->count();
            default:
                return 0;
        }
    }
}
