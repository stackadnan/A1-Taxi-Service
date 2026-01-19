<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));

        $query = Driver::query();
        if ($q !== '') {
            $query->where(function($qq) use ($q){
                $qq->where('name', 'like', "%{$q}%")
                   ->orWhere('phone', 'like', "%{$q}%")
                   ->orWhere('email', 'like', "%{$q}%")
                   ->orWhere('coverage_area', 'like', "%{$q}%")
                   ->orWhere('badge_number', 'like', "%{$q}%")
                   ->orWhere('vehicle_plate', 'like', "%{$q}%");
            });
        }

        $drivers = $query->orderBy('name')->paginate(20)->withQueryString();

        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._list', compact('drivers'));
        }

        return view('admin.drivers.index', compact('drivers'));
    }

    public function show(Request $request, Driver $driver)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._show', compact('driver'));
        }

        return view('admin.drivers.show', compact('driver'));
    }

    public function create(Request $request)
    {
        // Full page create
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form');
        }
        return view('admin.drivers.create');
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate',
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number',
            'council_id' => 'nullable|integer|exists:councils,id',
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $driver = Driver::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'driver' => $driver], 201);
        }

        return redirect()->route('admin.drivers.index')->with('success', 'Driver created');
    }

    public function edit(Request $request, Driver $driver)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('driver'));
        }
        return view('admin.drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate,'.$driver->id,
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number,'.$driver->id,
            'council_id' => 'nullable|integer|exists:councils,id',
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $driver->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'driver' => $driver], 200);
        }

        return redirect()->route('admin.drivers.show', $driver)->with('success', 'Driver updated');
    }

    public function destroy(Request $request, Driver $driver)
    {
        $driver->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.drivers.index')->with('success', 'Driver deleted');
    }
}
