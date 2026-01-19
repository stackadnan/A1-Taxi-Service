<?php

namespace App\Http\Controllers\Admin\Pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingAddonCharge as OtherCharge;

class OtherController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $items = OtherCharge::when($q, function($qb) use ($q){ $qb->where('charge_name', 'like', "%$q%"); })->orderBy('id')->paginate(20);

        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.others._list', compact('items','q'));
        }

        return view('admin.pricing.others.index', compact('items','q'));
    }

    public function create(Request $request)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.others._modal_form');
        }
        return view('admin.pricing.others.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'charge_name' => 'required|string|max:255',
            'pickup_price' => 'nullable|numeric|min:0',
            'dropoff_price' => 'nullable|numeric|min:0',
            'vehicle_type' => 'nullable|string|max:100',
            'charge_type' => 'nullable|in:flat,percentage',
            'charge_value' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'active' => 'nullable|boolean'
        ]);

        $created = OtherCharge::create([
            'charge_name' => $data['charge_name'],
            'pickup_price' => $data['pickup_price'] ?? 0,
            'dropoff_price' => $data['dropoff_price'] ?? 0,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'charge_type' => $data['charge_type'] ?? 'flat',
            'charge_value' => $data['charge_value'] ?? 0,
            'status' => $data['status'] ?? (isset($data['active']) ? ($data['active'] ? 'active' : 'inactive') : 'inactive'),
            'active' => isset($data['active']) ? $data['active'] : ($data['status'] === 'active')
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $created], 201);
        }

        return redirect()->route('admin.pricing.others.index')->with('success', 'Other charge created');
    }

    public function edit(Request $request, OtherCharge $other)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.others._modal_form', ['item' => $other]);
        }

        return view('admin.pricing.others.edit', ['item' => $other]);
    }

    public function update(Request $request, OtherCharge $other)
    {
        $data = $request->validate([
            'charge_name' => 'required|string|max:255',
            'pickup_price' => 'nullable|numeric|min:0',
            'dropoff_price' => 'nullable|numeric|min:0',
            'vehicle_type' => 'nullable|string|max:100',
            'charge_type' => 'nullable|in:flat,percentage',
            'charge_value' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:active,inactive',
            'active' => 'nullable|boolean'
        ]);

        $other->update([
            'charge_name' => $data['charge_name'],
            'pickup_price' => $data['pickup_price'] ?? 0,
            'dropoff_price' => $data['dropoff_price'] ?? 0,
            'vehicle_type' => $data['vehicle_type'] ?? null,
            'charge_type' => $data['charge_type'] ?? 'flat',
            'charge_value' => $data['charge_value'] ?? 0,
            'status' => $data['status'] ?? (isset($data['active']) ? ($data['active'] ? 'active' : 'inactive') : 'inactive'),
            'active' => isset($data['active']) ? $data['active'] : ($data['status'] === 'active')
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $other], 200);
        }

        return redirect()->route('admin.pricing.others.index')->with('success', 'Other charge updated');
    }

    public function destroy(Request $request, OtherCharge $other)
    {
        $other->delete();
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }
        return redirect()->route('admin.pricing.others.index')->with('success', 'Other charge deleted');
    }
}
