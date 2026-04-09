<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ComplaintLostFound;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComplaintLostFoundController extends Controller
{
    public function index(Request $request): View
    {
        $query = ComplaintLostFound::query();

        if ($request->filled('search')) {
            $s = $request->input('search');
            $query->where(function ($q) use ($s) {
                $q->where('booking_id', 'like', "%{$s}%")
                    ->orWhere('name', 'like', "%{$s}%")
                    ->orWhere('email', 'like', "%{$s}%")
                    ->orWhere('concern', 'like', "%{$s}%")
                    ->orWhere('lost_found', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $complaints = $query->orderByDesc('id')->paginate(20)->withQueryString();

        $totalCount = ComplaintLostFound::count();
        $newCount = ComplaintLostFound::where('status', ComplaintLostFound::STATUS_NEW)->count();
        $pendingCount = ComplaintLostFound::where('status', ComplaintLostFound::STATUS_PENDING)->count();
        $resolvedCount = ComplaintLostFound::where('status', ComplaintLostFound::STATUS_RESOLVED)->count();

        return view('admin.complaints.index', compact(
            'complaints',
            'totalCount',
            'newCount',
            'pendingCount',
            'resolvedCount'
        ));
    }

    public function edit(ComplaintLostFound $complaint): View
    {
        return view('admin.complaints.edit', compact('complaint'));
    }

    public function update(Request $request, ComplaintLostFound $complaint): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'booking_id' => ['nullable', 'string', 'max:255'],
            'concern' => ['required', 'string', 'max:5000'],
            'lost_found' => ['required', 'string', 'max:5000'],
            'status' => ['required', 'in:new,pending,resolved'],
        ]);

        $complaint->update($data);

        return redirect()
            ->route('admin.complaints.index')
            ->with('status', 'Complaint/Lost Found record updated successfully.');
    }
}
