<?php

namespace App\Http\Controllers\Admin\Pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingMileageCharge;

class MileageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $items = PricingMileageCharge::when($q, function($qb) use ($q){
            $qb->where('start_mile', 'like', "%$q%")
               ->orWhere('end_mile', 'like', "%$q%");
        })->orderBy('start_mile')->paginate(20);

        if ($request->get('partial') || $request->ajax()) {
            return view('admin.pricing.mileage._list', compact('items','q'));
        }

        return view('admin.pricing.mileage.index', compact('items','q'));
    }

    public function create(Request $request)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.mileage._modal_form');
        }

        return view('admin.pricing.mileage.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'start_mile' => 'required|numeric|min:0',
            'end_mile' => 'nullable|numeric|min:0|gte:start_mile',
            'saloon_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'mpv6_price' => 'nullable|numeric|min:0',
            'mpv8_price' => 'nullable|numeric|min:0',
            'is_fixed_charge' => 'nullable|boolean',
            'status' => 'required|in:active,inactive'
        ]);

        // Validate overlap with existing mileage ranges
        $start = (float) $data['start_mile'];
        $end = isset($data['end_mile']) && $data['end_mile'] !== null && $data['end_mile'] !== '' ? (float) $data['end_mile'] : null;

        $conflicts = PricingMileageCharge::where(function($q) use ($start, $end){
            if ($end === null) {
                // new open-ended range overlaps any existing range that ends after start, or that is open-ended
                $q->whereNull('end_mile')->orWhere('end_mile', '>', $start);
            } else {
                // overlap exists when existing.end > new.start AND existing.start < new.end (touching endpoints are allowed)
                $q->where(function($qq) use ($start){ $qq->whereNull('end_mile')->orWhere('end_mile', '>', $start); })
                  ->where('start_mile', '<', $end);
            }
        });

        if ($conflicts->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Mileage range overlaps with existing ranges'], 422);
            }
            return redirect()->back()->withErrors(['overlap' => 'Mileage range overlaps with existing ranges'])->withInput();
        }

        // Enforce maximum of 10 mileage entries
        $total = PricingMileageCharge::count();
        if ($total >= 10) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Maximum of 10 mileage charges reached'], 422);
            }
            return redirect()->route('admin.pricing.mileage.index')->with('error', 'Maximum of 10 mileage charges reached');
        }

        $created = PricingMileageCharge::create(array_merge($data, ['is_fixed_charge' => (bool) ($data['is_fixed_charge'] ?? false)]));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $created], 201);
        }

        return redirect()->route('admin.pricing.mileage.index')->with('success','Mileage charge created');
    }

    public function edit(Request $request, PricingMileageCharge $mileage)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.mileage._modal_form', ['mileage' => $mileage]);
        }

        return view('admin.pricing.mileage.edit', ['mileage' => $mileage]);
    }

    public function update(Request $request, PricingMileageCharge $mileage)
    {
        $data = $request->validate([
            'start_mile' => 'required|numeric|min:0',
            'end_mile' => 'nullable|numeric|min:0|gte:start_mile',
            'saloon_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'mpv6_price' => 'nullable|numeric|min:0',
            'mpv8_price' => 'nullable|numeric|min:0',
            'is_fixed_charge' => 'nullable|boolean',
            'status' => 'required|in:active,inactive'
        ]);

        // Check for overlap with other records (exclude this one)
        $start = (float) $data['start_mile'];
        $end = isset($data['end_mile']) && $data['end_mile'] !== null && $data['end_mile'] !== '' ? (float) $data['end_mile'] : null;

        $conflicts = PricingMileageCharge::where('id', '!=', $mileage->id)
            ->where(function($q) use ($start, $end){
                if ($end === null) {
                    $q->whereNull('end_mile')->orWhere('end_mile', '>', $start);
                } else {
                    $q->where(function($qq) use ($start){ $qq->whereNull('end_mile')->orWhere('end_mile', '>', $start); })
                      ->where('start_mile', '<', $end);
                }
            });

        if ($conflicts->exists()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Mileage range overlaps with existing ranges'], 422);
            }
            return redirect()->back()->withErrors(['overlap' => 'Mileage range overlaps with existing ranges'])->withInput();
        }

        $mileage->update(array_merge($data, ['is_fixed_charge' => (bool) ($data['is_fixed_charge'] ?? false)]));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $mileage], 200);
        }

        return redirect()->route('admin.pricing.mileage.index')->with('success','Mileage charge updated');
    }

    public function destroy(Request $request, PricingMileageCharge $mileage)
    {
        $mileage->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->to(route('admin.pricing.index') . '#mileage')->with('success','Mileage charge deleted');
    }
}
