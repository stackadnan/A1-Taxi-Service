<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $tab = $request->get('tab', 'active');

        $query = Driver::query();

        // Apply search filter
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

        // Tab filters
        $today = \Carbon\Carbon::today()->toDateString();
        if ($tab === 'active') {
            // Active drivers must have status=active AND have no expired documents (all expiry fields null or >= today)
            $query->where('status', '=', 'active')
                  ->where(function($q) use ($today) { $q->whereNull('driving_license_expiry')->orWhere('driving_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_drivers_license_expiry')->orWhere('private_hire_drivers_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_insurance_expiry')->orWhere('private_hire_vehicle_insurance_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_license_expiry')->orWhere('private_hire_vehicle_license_expiry', '>=', $today); })
                  ->where(function($q) use ($today) { $q->whereNull('private_hire_vehicle_mot_expiry')->orWhere('private_hire_vehicle_mot_expiry', '>=', $today); });
        } elseif ($tab === 'inactive') {
            // Inactive drivers include those explicitly inactive or those with any expired document (< today)
            $query->where(function($q) use ($today){
                $q->where('status', '=', 'inactive')
                  ->orWhere(function($q2) use ($today){
                      $q2->whereNotNull('driving_license_expiry')->where('driving_license_expiry', '<', \Carbon\Carbon::today()->toDateString())
                         ->orWhere(function($q3){ $q3->whereNotNull('private_hire_drivers_license_expiry')->where('private_hire_drivers_license_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q4){ $q4->whereNotNull('private_hire_vehicle_insurance_expiry')->where('private_hire_vehicle_insurance_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q5){ $q5->whereNotNull('private_hire_vehicle_license_expiry')->where('private_hire_vehicle_license_expiry', '<', \Carbon\Carbon::today()->toDateString()); })
                         ->orWhere(function($q6){ $q6->whereNotNull('private_hire_vehicle_mot_expiry')->where('private_hire_vehicle_mot_expiry', '<', \Carbon\Carbon::today()->toDateString()); });
                  });
            });
        } elseif ($tab === 'documents') {
            $soon = \Carbon\Carbon::today()->addDays(30)->toDateString();
            $query->where(function($qdoc) use ($soon){
                $qdoc->whereNotNull('driving_license_expiry')->where('driving_license_expiry', '<=', $soon)
                     ->orWhere(function($q2) use ($soon){ $q2->whereNotNull('private_hire_drivers_license_expiry')->where('private_hire_drivers_license_expiry', '<=', $soon); })
                     ->orWhere(function($q3) use ($soon){ $q3->whereNotNull('private_hire_vehicle_insurance_expiry')->where('private_hire_vehicle_insurance_expiry', '<=', $soon); })
                     ->orWhere(function($q4) use ($soon){ $q4->whereNotNull('private_hire_vehicle_license_expiry')->where('private_hire_vehicle_license_expiry', '<=', $soon); })
                     ->orWhere(function($q5) use ($soon){ $q5->whereNotNull('private_hire_vehicle_mot_expiry')->where('private_hire_vehicle_mot_expiry', '<=', $soon); });
            });
        }

        $drivers = $query->orderBy('name')->paginate(20)->withQueryString();

        // Prepare additional data for documents tab
        if ($tab === 'documents') {
            $today = \Carbon\Carbon::today();
            foreach ($drivers as $drv) {
                $docs = [];
                $fields = [
                    'driving_license' => 'Driving License',
                    'private_hire_drivers_license' => 'Private Hire Drivers License',
                    'private_hire_vehicle_insurance' => 'Private Hire Vehicle Insurance',
                    'private_hire_vehicle_license' => 'Private Hire Vehicle License',
                    'private_hire_vehicle_mot' => 'Private Hire Vehicle MOT',
                ];
                foreach ($fields as $field => $label) {
                    $expiryField = $field . '_expiry';
                    $expiry = $drv->{$expiryField} ?? null;
                    if ($expiry) {
                        $expiryDate = \Carbon\Carbon::parse($expiry);
                        if ($expiryDate->lte($today->copy()->addDays(30))) {
                            $docs[] = ['field' => $field, 'label' => $label, 'expiry' => $expiryDate, 'status' => $expiryDate->lt($today) ? 'expired' : 'expiring'];
                        }
                    }
                }
                // sort by expiry asc
                usort($docs, function($a,$b){ return $a['expiry']->timestamp <=> $b['expiry']->timestamp; });
                $drv->expiring_documents = $docs;
            }
        }

        // Prepare driver status data for the 'status' tab
        if ($tab === 'status') {
            // Get status ids that mean a booking is finished
            $finishedStatusIds = \App\Models\BookingStatus::whereIn('name', ['completed', 'cancelled'])->pluck('id')->toArray();

            foreach ($drivers as $drv) {
                // find most relevant active booking for this driver (not completed/cancelled)
                $currentBooking = \App\Models\Booking::where('driver_id', $drv->id)
                    ->whereNotIn('status_id', $finishedStatusIds)
                    ->orderBy('scheduled_at', 'desc')
                    ->orderBy('id', 'desc')
                    ->first();

                $drv->current_booking = $currentBooking;

                if ($currentBooking) {
                    $statusKey = $currentBooking->status->name ?? 'in_progress';
                    $labelMap = [
                        'in_progress' => ['On Route', 'green'],
                        'confirmed' => ['Confirmed', 'yellow'],
                        'new' => ['New', 'gray'],
                    ];
                    $label = $labelMap[$statusKey][0] ?? ucfirst(str_replace('_', ' ', $statusKey));
                    $color = $labelMap[$statusKey][1] ?? 'gray';
                    $sinceFrom = $drv->last_assigned_at ?? $currentBooking->updated_at ?? $drv->last_active_at;
                } else {
                    $label = 'Idle';
                    $color = 'yellow';
                    $sinceFrom = $drv->last_active_at;
                }

                $sinceStr = '-';
                if ($sinceFrom) {
                    $sinceCarbon = \Carbon\Carbon::parse($sinceFrom);
                    $diffSeconds = \Carbon\Carbon::now()->diffInSeconds($sinceCarbon);
                    $h = floor($diffSeconds / 3600);
                    $m = floor(($diffSeconds % 3600) / 60);
                    $s = $diffSeconds % 60;
                    $sinceStr = sprintf('%02d:%02d:%02d', $h, $m, $s);
                }

                $drv->status_label = $label;
                $drv->status_color = $color;
                $drv->status_since = $sinceStr;
            }
        }

        if ($request->ajax() || $request->get('partial')) {
            if ($tab === 'documents') {
                return view('admin.drivers._documents', compact('drivers'));
            }
            if ($tab === 'status') {
                return view('admin.drivers._status', compact('drivers'));
            }
            return view('admin.drivers._list', compact('drivers'));
        }

        return view('admin.drivers.index', compact('drivers','tab'));
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
        $councils = DB::table('councils')->orderBy('council_name')->get();
        
        // Full page create
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('councils'));
        }
        return view('admin.drivers.create', compact('councils'));
    }


    public function store(Request $request)
    {
        $data = $request->validate([
            // Driver Info
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'council_id' => 'nullable|integer|exists:councils,id',
            'driver_lives' => 'nullable|string|max:500',
            'driver_address' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'driver_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Vehicle Info
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate',
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'passenger_capacity' => 'nullable|integer|min:1|max:20',
            'luggage_capacity' => 'nullable|integer|min:0|max:50',
            'vehicle_license_number' => 'nullable|string|max:100',
            'vehicle_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Driver Documents
            'driving_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driving_license_expiry' => 'nullable|date',
            'private_hire_drivers_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_drivers_license_expiry' => 'nullable|date',
            'private_hire_vehicle_insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_insurance_expiry' => 'nullable|date',
            'private_hire_vehicle_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_license_expiry' => 'nullable|date',
            'private_hire_vehicle_mot' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_mot_expiry' => 'nullable|date',
            
            // Other
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number',
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            $data['driver_picture'] = $request->file('driver_picture')->store('drivers/pictures', 'public');
        }

        // Handle document uploads
        $documents = ['driving_license', 'private_hire_drivers_license', 'private_hire_vehicle_insurance', 'private_hire_vehicle_license', 'private_hire_vehicle_mot'];
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                $data[$doc] = $request->file($doc)->store('drivers/documents', 'public');
            }
        }

        // Handle multiple vehicle pictures
        if ($request->hasFile('vehicle_pictures')) {
            $vehiclePictures = [];
            foreach ($request->file('vehicle_pictures') as $file) {
                $vehiclePictures[] = $file->store('drivers/vehicles', 'public');
            }
            $data['vehicle_pictures'] = $vehiclePictures;
        }

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
        $councils = DB::table('councils')->orderBy('council_name')->get();
        
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('driver', 'councils'));
        }
        return view('admin.drivers.edit', compact('driver', 'councils'));
    }

    public function update(Request $request, Driver $driver)
    {
        $data = $request->validate([
            // Driver Info
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'license_number' => 'nullable|string|max:100',
            'council_id' => 'nullable|integer|exists:councils,id',
            'driver_lives' => 'nullable|string|max:500',
            'driver_address' => 'nullable|string|max:500',
            'working_hours' => 'nullable|string|max:255',
            'bank_name' => 'nullable|string|max:255',
            'account_title' => 'nullable|string|max:255',
            'sort_code' => 'nullable|string|max:20',
            'account_number' => 'nullable|string|max:50',
            'driver_picture' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Vehicle Info
            'vehicle_make' => 'nullable|string|max:100',
            'vehicle_model' => 'nullable|string|max:100',
            'vehicle_plate' => 'nullable|string|max:50|unique:drivers,vehicle_plate,'.$driver->id,
            'car_type' => 'nullable|string|max:100',
            'car_color' => 'nullable|string|max:50',
            'passenger_capacity' => 'nullable|integer|min:1|max:20',
            'luggage_capacity' => 'nullable|integer|min:0|max:50',
            'vehicle_license_number' => 'nullable|string|max:100',
            'vehicle_pictures.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            
            // Driver Documents
            'driving_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'driving_license_expiry' => 'nullable|date',
            'private_hire_drivers_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_drivers_license_expiry' => 'nullable|date',
            'private_hire_vehicle_insurance' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_insurance_expiry' => 'nullable|date',
            'private_hire_vehicle_license' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_license_expiry' => 'nullable|date',
            'private_hire_vehicle_mot' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'private_hire_vehicle_mot_expiry' => 'nullable|date',
            
            // Other
            'coverage_area' => 'nullable|string|max:255',
            'badge_number' => 'nullable|string|max:100|unique:drivers,badge_number,'.$driver->id,
            'time_slot' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6|confirmed',
            'status' => 'nullable|string|max:50',
        ]);

        // Handle file uploads
        if ($request->hasFile('driver_picture')) {
            // Delete old picture if exists
            if ($driver->driver_picture && \Storage::disk('public')->exists($driver->driver_picture)) {
                \Storage::disk('public')->delete($driver->driver_picture);
            }
            $data['driver_picture'] = $request->file('driver_picture')->store('drivers/pictures', 'public');
        }

        // Handle document uploads
        $documents = ['driving_license', 'private_hire_drivers_license', 'private_hire_vehicle_insurance', 'private_hire_vehicle_license', 'private_hire_vehicle_mot'];
        foreach ($documents as $doc) {
            if ($request->hasFile($doc)) {
                // Delete old document if exists
                if ($driver->$doc && \Storage::disk('public')->exists($driver->$doc)) {
                    \Storage::disk('public')->delete($driver->$doc);
                }
                $data[$doc] = $request->file($doc)->store('drivers/documents', 'public');
            }
        }

        // Handle multiple vehicle pictures
        if ($request->hasFile('vehicle_pictures')) {
            // Delete old pictures if exist
            if ($driver->vehicle_pictures) {
                foreach ($driver->vehicle_pictures as $oldPicture) {
                    if (\Storage::disk('public')->exists($oldPicture)) {
                        \Storage::disk('public')->delete($oldPicture);
                    }
                }
            }
            
            $vehiclePictures = [];
            foreach ($request->file('vehicle_pictures') as $file) {
                $vehiclePictures[] = $file->store('drivers/vehicles', 'public');
            }
            $data['vehicle_pictures'] = $vehiclePictures;
        }

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
