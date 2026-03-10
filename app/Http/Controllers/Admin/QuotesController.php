<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PublicQuoteRequest;

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

        $totalCount      = PublicQuoteRequest::count();
        $oneWayCount     = PublicQuoteRequest::where('trip_type', 'one-way')->count();
        $returnCount     = PublicQuoteRequest::where('trip_type', 'return')->count();
        $todayCount      = PublicQuoteRequest::whereDate('created_at', today())->count();

        return view('admin.quotes.index', compact('quotes', 'totalCount', 'oneWayCount', 'returnCount', 'todayCount'));
    }
}
