<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PublicQuoteRequest;
use App\Models\Booking;

class QuotesController extends Controller
{
    public function index(Request $request)
    {
        $query = PublicQuoteRequest::query();

        // Search filter
        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('quote_ref', 'like', "%{$s}%")
                  ->orWhere('pickup_address', 'like', "%{$s}%")
                  ->orWhere('dropoff_address', 'like', "%{$s}%")
                  ->orWhere('source_ip', 'like', "%{$s}%");
            });
        }

        // Vehicle type filter
        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->input('vehicle_type'));
        }

        // Trip type filter
        if ($request->filled('trip_type')) {
            $query->where('trip_type', $request->input('trip_type'));
        }

        // Date filter
        if ($request->filled('date')) {
            $query->whereDate('pickup_date', $request->input('date'));
        }

        $quotes = $query->orderBy('id', 'desc')->paginate(25)->withQueryString();

        $convertedQuoteRefMap = [];
        $pageQuoteRefs = $quotes->getCollection()
            ->pluck('quote_ref')
            ->filter()
            ->unique()
            ->values()
            ->all();

        if (!empty($pageQuoteRefs)) {
            $convertedRefs = Booking::query()
                ->where(function ($q) use ($pageQuoteRefs) {
                    foreach ($pageQuoteRefs as $ref) {
                        $q->orWhere('meta->quote_ref', $ref)
                          ->orWhere('meta->return_ref', $ref);
                    }
                })
                ->get(['meta'])
                ->flatMap(function ($booking) {
                    $meta = is_array($booking->meta) ? $booking->meta : [];

                    return [
                        data_get($meta, 'quote_ref'),
                        data_get($meta, 'return_ref'),
                    ];
                })
                ->filter()
                ->unique()
                ->values()
                ->all();

            $convertedQuoteRefMap = array_fill_keys($convertedRefs, true);
        }

        $totalCount      = PublicQuoteRequest::count();
        $oneWayCount     = PublicQuoteRequest::where('trip_type', 'one-way')->count();
        $returnCount     = PublicQuoteRequest::where('trip_type', 'return')->count();
        $todayCount      = PublicQuoteRequest::whereDate('created_at', today())->count();

        return view('admin.quotes.index', compact('quotes', 'totalCount', 'oneWayCount', 'returnCount', 'todayCount', 'convertedQuoteRefMap'));
    }
}
