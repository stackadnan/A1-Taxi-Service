<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingStatus;
use App\Models\UserNotification;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function index()
    {
        // Log admin dashboard page load for debugging SSE / notification issues
        try { \Log::info('AdminController:index served', ['user_id' => auth()->id(), 'route' => request()->path()]); } catch (\Exception $e) {}

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

        // Prefer recent status changes (status_changed_at in meta) but fallback to updated_at
        $bookings = $query
            ->orderByRaw("GREATEST(UNIX_TIMESTAMP(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta,'{}'), '$.status_changed_at')), updated_at)), UNIX_TIMESTAMP(updated_at)) DESC")
            ->orderBy('id', 'desc')
            ->paginate(15);

        // If partial request, return only the bookings list
        if (request()->ajax() || request()->get('partial')) {
            return view('admin.bookings._list', compact('bookings', 'active'));
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

    /**
     * Get unread notifications for the current admin user
     */
    public function getUnreadNotifications()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['count' => 0, 'notifications' => []]);
        }

        $notifications = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications
        ]);
    }

    /**
     * Mark notifications as read
     */
    public function markNotificationsRead()
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['success' => false]);
        }

        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);

        return response()->json(['success' => true]);
    }
}
