<?php

namespace App\Http\Controllers\Admin\Pricing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PricingZone;
use App\Models\Zone;
use App\Models\PricingPostcodeCharge;
use Illuminate\Validation\Rule;
use Illuminate\Database\QueryException;

class ZoneController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->get('q');
        $items = PricingZone::with(['fromZone','toZone'])
            ->when($q, function($qb) use ($q){
                $qb->whereHas('fromZone', function($q2) use ($q){ $q2->where('zone_name', 'like', "%$q%"); })
                   ->orWhereHas('toZone', function($q2) use ($q){ $q2->where('zone_name', 'like', "%$q%"); });
            })->orderBy('id')->paginate(20);

        if ($request->get('partial') || $request->ajax()) {
            return view('admin.pricing.zones._list', compact('items','q'));
        }

        return view('admin.pricing.zones.index', compact('items','q'));
    }

    public function create(Request $request)
    {
        $zones = Zone::orderBy('zone_name')->get();
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.zones._modal_form', ['zones' => $zones]);
        }

        return view('admin.pricing.zones.create', ['zones' => $zones]);
    }

    // Show the map-based zone creation modal
    public function createMap(Request $request)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.zones._map_modal');
        }

        return view('admin.pricing.zones.create-map');
    }

    // Store a zone created from the map (polygon geojson accepted)
    public function storeMap(Request $request)
    {
        $data = $request->validate([
            'zone_name' => ['required','string','max:255', Rule::unique('zones','zone_name')],
            'polygon' => 'nullable|json'
        ]);

        $meta = [];
        $latitude = null; $longitude = null;

        if (!empty($data['polygon'])) {
            $polygonJson = $data['polygon'];
            $meta['polygon'] = json_decode($polygonJson, true);

            // server-side bbox check to avoid obvious overlaps, but fall back to precise polygon intersection checks
            try {
                $incomingBbox = $this->polygonBbox($meta['polygon']);
                $zones = Zone::whereNotNull('meta')->get()->filter(function($z){ return isset($z->meta['polygon']) && is_array($z->meta['polygon']); });
                foreach ($zones as $existing) {
                    if (!isset($existing->meta['polygon'])) continue;
                    $existingBbox = $this->polygonBbox($existing->meta['polygon']);
                    if ($this->bboxesIntersect($incomingBbox, $existingBbox)) {
                        // perform a more accurate polygon intersection test (ray-casting + segment intersections)
                        if ($this->polygonsIntersect($meta['polygon'], $existing->meta['polygon'])) {
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json(['success' => false, 'message' => 'Proposed zone overlaps an existing zone: ' . $existing->zone_name], 422);
                            }
                            return redirect()->back()->withErrors(['polygon' => 'Proposed zone overlaps an existing zone: ' . $existing->zone_name])->withInput();
                        }
                        // otherwise: bboxes intersect but polygons do not overlap (near-miss), allow
                    }
                }

                // ensure polygon stays inside UK bbox
                $coordsForCheck = $meta['polygon']['coordinates'][0] ?? [];
                foreach ($coordsForCheck as $pt) {
                    $lng = (float) ($pt[0] ?? 0);
                    $lat = (float) ($pt[1] ?? 0);
                    if ($lat < 49.5 || $lat > 61.0 || $lng < -8.6 || $lng > 2.1) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => 'Zone must be entirely within the UK'], 422);
                        }
                        return redirect()->back()->withErrors(['polygon' => 'Zone must be entirely within the UK'])->withInput();
                    }
                }
            } catch (\Exception $e) {
                // ignore bbox check failures
            }

            // compute simple centroid (average of coords from first ring)
            try {
                $coords = $meta['polygon']['coordinates'][0]; // [ [lng,lat], ... ]
                $sumLat = 0; $sumLng = 0; $count = 0;
                foreach ($coords as $pt) {
                    $lng = (float) $pt[0];
                    $lat = (float) $pt[1];
                    $sumLat += $lat; $sumLng += $lng; $count++;
                }
                if ($count) {
                    $latitude = $sumLat / $count;
                    $longitude = $sumLng / $count;
                }
            } catch (\Exception $e) {
                // ignore centroid on malformed polygon
            }
        }

        try {
            $zone = Zone::create([
                'zone_name' => $data['zone_name'],
                'latitude' => $latitude,
                'longitude' => $longitude,
                'meta' => $meta,
                'status' => 'active'
            ]);
        } catch (QueryException $ex) {
            // handle unique constraint fallback
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Zone name must be unique','errors' => ['zone_name' => ['Zone name must be unique']]], 422);
            }
            return redirect()->back()->withErrors(['zone_name' => 'Zone name must be unique'])->withInput();
        }

        if ($request->ajax() || $request->wantsJson()) {
            $optionHtml = view('admin.pricing.zones._option', ['zone' => $zone])->render();
            return response()->json(['success' => true, 'item' => $zone, 'option_html' => $optionHtml], 201);
        }

        return redirect()->route('admin.pricing.zones.index')->with('success','Zone created');
    }

    // Show the map modal preloaded for editing an existing zone's polygon
    public function editMap(Request $request, Zone $zone)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.zones._map_modal', ['item' => $zone]);
        }

        return view('admin.pricing.zones.edit_map', ['item' => $zone]);
    }

    // Show a full page map that renders ALL zones saved on the system
    public function mapIndex(Request $request)
    {
        $zones = Zone::whereNotNull('meta')->get()->filter(function($z){
            return isset($z->meta['polygon']) && is_array($z->meta['polygon']);
        })->values();

        // prepare GeoJSON feature collection
        $features = [];
        foreach ($zones as $z) {
            $poly = $z->meta['polygon'] ?? null;
            if (!$poly || !isset($poly['coordinates'][0])) continue;
            $features[] = [
                'type' => 'Feature',
                'properties' => [ 'id' => $z->id, 'name' => $z->zone_name ],
                'geometry' => $poly
            ];
        }

        $geojson = [ 'type' => 'FeatureCollection', 'features' => $features ];

        // Return partial view for AJAX requests (for dynamic tab loading)
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.zones._map_content', ['geojson' => $geojson, 'zones' => $zones]);
        }

        return view('admin.pricing.zones.map_index', ['geojson' => $geojson, 'zones' => $zones]);
    }

    // API: given a lat/lon, return the Zone that contains the point (if any)
    public function lookup(Request $request)
    {
        $data = $request->validate([
            'lat' => 'required|numeric',
            'lon' => 'required|numeric'
        ]);

        $lat = (float) $data['lat'];
        $lon = (float) $data['lon'];

        $zones = Zone::whereNotNull('meta')->get()->filter(function($z){ return isset($z->meta['polygon']) && is_array($z->meta['polygon']); });

        foreach ($zones as $z) {
            $poly = $z->meta['polygon'];
            if (!isset($poly['coordinates'][0]) || !is_array($poly['coordinates'][0])) continue;
            try {
                $incomingBbox = $this->polygonBbox(['coordinates' => $poly['coordinates']]);
                $existingBbox = $this->polygonBbox($poly);
                if ($this->bboxesIntersect($incomingBbox, $existingBbox)) {
                    // pointInPolygon expects [lng,lat] and ring coords in [lng,lat]
                    if ($this->pointInPolygon([$lon, $lat], $poly['coordinates'][0])) {
                        return response()->json(['success' => true, 'zone' => ['id' => $z->id, 'zone_name' => $z->zone_name]]);
                    }
                }
            } catch (\Exception $e) {
                // ignore and continue
            }
        }

        return response()->json(['success' => false, 'zone' => null], 200);
    }

    // API: quote price given pickup/dropoff coordinates and optional vehicle type
    public function quote(Request $request)
    {
        $data = $request->validate([
            'pickup_lat' => 'required|numeric',
            'pickup_lon' => 'required|numeric',
            'dropoff_lat' => 'required|numeric',
            'dropoff_lon' => 'required|numeric',
            'vehicle_type' => 'nullable|string',
            'pickup_postcode' => 'nullable|string|max:20',
            'dropoff_postcode' => 'nullable|string|max:20',
            'distance_miles' => 'nullable|numeric|min:0'
        ]);

        \Log::info('=== PRICE CALCULATION START ===');
        \Log::info('Pickup Coordinates', [
            'latitude' => $data['pickup_lat'],
            'longitude' => $data['pickup_lon'],
            'postcode' => $data['pickup_postcode'] ?? 'N/A'
        ]);
        \Log::info('Dropoff Coordinates', [
            'latitude' => $data['dropoff_lat'],
            'longitude' => $data['dropoff_lon'],
            'postcode' => $data['dropoff_postcode'] ?? 'N/A'
        ]);

        $pickupZone = $this->findZoneForPoint((float)$data['pickup_lat'], (float)$data['pickup_lon']);
        $dropZone = $this->findZoneForPoint((float)$data['dropoff_lat'], (float)$data['dropoff_lon']);

        // Check for airports from JSON file separately (for airport charges only)
        $pickupAirport = $this->findAirportForPoint((float)$data['pickup_lat'], (float)$data['pickup_lon']);
        $dropoffAirport = $this->findAirportForPoint((float)$data['dropoff_lat'], (float)$data['dropoff_lon']);

        // If no DB zone found, use airport from JSON for base pricing as well
        if (! $pickupZone && $pickupAirport) {
            $pickupZone = ['id' => null, 'zone_name' => $pickupAirport, 'airport' => true];
        }
        if (! $dropZone && $dropoffAirport) {
            $dropZone = ['id' => null, 'zone_name' => $dropoffAirport, 'airport' => true];
        }

        \Log::info('Zone Detection Result', [
            'pickup_zone' => $pickupZone ? ($pickupZone['zone_name'] . (isset($pickupZone['id']) && $pickupZone['id'] ? ' (ID: ' . $pickupZone['id'] . ')' : ' (airport from JSON)')) : 'No zone detected',
            'dropoff_zone' => $dropZone ? ($dropZone['zone_name'] . (isset($dropZone['id']) && $dropZone['id'] ? ' (ID: ' . $dropZone['id'] . ')' : ' (airport from JSON)')) : 'No zone detected'
        ]);

        $pricing = null; $pricingType = 'zone';

        // if both zones found, try zone pricing first
        if ($pickupZone && $dropZone) {
            $pricing = \App\Models\PricingZone::where('from_zone_id', $pickupZone['id'])->where('to_zone_id', $dropZone['id'])->first();
            if ($pricing) {
                \Log::info('Zone-based Pricing Found', [
                    'pricing_id' => $pricing->id,
                    'from_zone' => $pickupZone['zone_name'],
                    'to_zone' => $dropZone['zone_name'],
                    'saloon_price' => $pricing->saloon_price,
                    'business_price' => $pricing->business_price,
                    'mpv6_price' => $pricing->mpv6_price,
                    'mpv8_price' => $pricing->mpv8_price
                ]);
            }
        }

        // If pricing not found via zones, try postcode fallback if postcodes provided
        if (!$pricing) {
            $pp = $data['pickup_postcode'] ?? null;
            $dp = $data['dropoff_postcode'] ?? null;
            if ($pp && $dp) {
                \Log::info('Attempting Postcode-based Pricing', [
                    'pickup_postcode' => $pp,
                    'dropoff_postcode' => $dp
                ]);
                $normalize = function($s){ return strtoupper(preg_replace('/\s+/', '', trim($s))); };
                $npp = $normalize($pp);
                $ndp = $normalize($dp);
                try {
                    $pricing = \App\Models\PricingPostcodeCharge::whereRaw("upper(replace(pickup_postcode,' ','')) = ?", [$npp])
                                ->whereRaw("upper(replace(dropoff_postcode,' ','')) = ?", [$ndp])
                                ->first();
                    if ($pricing) {
                        $pricingType = 'postcode';
                        \Log::info('Postcode-based Pricing Found', [
                            'pricing_id' => $pricing->id,
                            'saloon_price' => $pricing->saloon_price,
                            'business_price' => $pricing->business_price,
                            'mpv6_price' => $pricing->mpv6_price,
                            'mpv8_price' => $pricing->mpv8_price
                        ]);
                    }
                } catch (\Exception $e) {
                    // fallback: try case-insensitive simple where
                    $pricing = \App\Models\PricingPostcodeCharge::where('pickup_postcode', $pp)->where('dropoff_postcode', $dp)->first();
                    if ($pricing) {
                        $pricingType = 'postcode';
                        \Log::info('Postcode-based Pricing Found (fallback)', [
                            'pricing_id' => $pricing->id,
                            'saloon_price' => $pricing->saloon_price,
                            'business_price' => $pricing->business_price,
                            'mpv6_price' => $pricing->mpv6_price,
                            'mpv8_price' => $pricing->mpv8_price
                        ]);
                    }
                }
            }
        }

        if (!$pricing) {
            \Log::info('No Zone/Postcode pricing found, attempting Mileage-based pricing');
            // No zone/postcode pricing found - attempt mileage-based fallback
            // If only one zone exists (pickup or dropoff), or only one postcode provided, or none, fallback to mileage
            try {
                    // if client provided an explicit driving distance, prefer it (this ensures server-side match mirrors the UI)
                if (isset($data['distance_miles']) && $data['distance_miles'] !== null) {
                    $miles = (float) $data['distance_miles'];
                    \Log::info('Using client-provided distance', ['miles' => $miles]);
                } else {
                    // compute approximate distance in miles using haversine
                    $lat1 = (float) $data['pickup_lat']; $lon1 = (float) $data['pickup_lon'];
                    $lat2 = (float) $data['dropoff_lat']; $lon2 = (float) $data['dropoff_lon'];
                    $toRad = function($d){ return $d * pi() / 180.0; };
                    $R = 6371; // km
                    $dLat = $toRad($lat2 - $lat1);
                    $dLon = $toRad($lon2 - $lon1);
                    $a = sin($dLat/2) * sin($dLat/2) + cos($toRad($lat1)) * cos($toRad($lat2)) * sin($dLon/2) * sin($dLon/2);
                    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                    $km = $R * $c;
                    $miles = $km * 0.621371;
                    \Log::info('Calculated distance using Haversine formula', ['km' => round($km, 2), 'miles' => round($miles, 2)]);
                }

                // find active mileage charge where start_mile <= miles and (end_mile is null or end_mile >= miles)
                $mileage = \App\Models\PricingMileageCharge::where('status','active')
                    ->where('start_mile', '<=', $miles)
                    ->where(function($q) use ($miles){ $q->whereNull('end_mile')->orWhere('end_mile', '>=', $miles); })
                    ->orderBy('start_mile', 'desc')
                    ->first();

                if ($mileage) {
                    $pricingType = 'mileage';
                    $pricing = $mileage; // reuse variable for uniform response
                    \Log::info('Mileage-based Pricing Found', [
                        'pricing_id' => $pricing->id,
                        'distance_miles' => round($miles, 2),
                        'range' => $pricing->start_mile . ' - ' . ($pricing->end_mile ?? '∞') . ' miles',
                        'is_fixed' => $pricing->is_fixed_charge,
                        'saloon_price' => $pricing->saloon_price,
                        'business_price' => $pricing->business_price,
                        'mpv6_price' => $pricing->mpv6_price,
                        'mpv8_price' => $pricing->mpv8_price
                    ]);
                } else {
                    // check if any matching mileage range exists but is not active
                    $inactive = \App\Models\PricingMileageCharge::where('start_mile', '<=', $miles)
                        ->where(function($q) use ($miles){ $q->whereNull('end_mile')->orWhere('end_mile', '>=', $miles); })
                        ->orderBy('start_mile', 'desc')
                        ->first();
                    if ($inactive) {
                        return response()->json(['success' => false, 'message' => 'Matching mileage range exists but is inactive', 'matching_mileage' => ['id' => $inactive->id, 'start_mile' => (float)$inactive->start_mile, 'end_mile' => $inactive->end_mile, 'status' => $inactive->status]], 200);
                    }
                    return response()->json(['success' => false, 'message' => 'No pricing available for selected zones, postcodes, or mileage', 'pickup_zone' => $pickupZone, 'dropoff_zone' => $dropZone], 200);
                }

            } catch (\Exception $e) {
                return response()->json(['success' => false, 'message' => 'Failed to compute mileage pricing', 'error' => $e->getMessage()], 500);
            }
        }

        // Check for airport addon charges ONLY from JSON file airports
        $pickupAddonCharge = 0;
        $dropoffAddonCharge = 0;
        $appliedCharges = [];
        
        // Map airport codes to full names for matching with addon charges
        $airportCodeMap = [
            'LHR' => 'Heathrow',
            'LGW' => 'Gatwick',
            'LTN' => 'Luton',
            'STN' => 'Stansted',
            'LCA' => 'City',
            'BRH' => 'Birmingham',
            'MAN' => 'Manchester'
        ];

        // Only check airport charges if the location is detected as an airport from JSON file
        if ($pickupAirport) {
            $zoneName = trim($pickupAirport);
            $searchTerm = $airportCodeMap[strtoupper($zoneName)] ?? $zoneName;

            // Get active airport addon charges only (filter by charge_name containing 'airport')
            $chargesList = \App\Models\PricingAddonCharge::where('status', 'active')
                ->where('active', true)
                ->where('charge_name', 'like', '%Airport%')
                ->get();
            $addonCharge = $this->selectBestAddonCharge($chargesList, $zoneName, $searchTerm);

            \Log::info('Pickup Airport Check', [
                'airport_name' => $zoneName,
                'search_term' => $searchTerm,
                'addon_charge_found' => $addonCharge ? true : false,
                'charge_name' => $addonCharge ? $addonCharge->charge_name : null,
                'pickup_price' => $addonCharge ? $addonCharge->pickup_price : null
            ]);
            
            if ($addonCharge && $addonCharge->pickup_price) {
                $pickupAddonCharge = (float) $addonCharge->pickup_price;
                $appliedCharges[] = [
                    'type' => 'pickup',
                    'zone' => $addonCharge->charge_name,
                    'charge_name' => $addonCharge->charge_name,
                    'amount' => $pickupAddonCharge
                ];
            }
        }

        if ($dropoffAirport) {
            $zoneName = trim($dropoffAirport);
            $searchTerm = $airportCodeMap[strtoupper($zoneName)] ?? $zoneName;

            // Get active airport addon charges only (filter by charge_name containing 'airport')
            $chargesList = \App\Models\PricingAddonCharge::where('status', 'active')
                ->where('active', true)
                ->where('charge_name', 'like', '%Airport%')
                ->get();
            $addonCharge = $this->selectBestAddonCharge($chargesList, $zoneName, $searchTerm);

            \Log::info('Dropoff Airport Check', [
                'airport_name' => $zoneName,
                'search_term' => $searchTerm,
                'addon_charge_found' => $addonCharge ? true : false,
                'charge_name' => $addonCharge ? $addonCharge->charge_name : null,
                'dropoff_price' => $addonCharge ? $addonCharge->dropoff_price : null
            ]);
            
            if ($addonCharge && $addonCharge->dropoff_price) {
                $dropoffAddonCharge = (float) $addonCharge->dropoff_price;
                $appliedCharges[] = [
                    'type' => 'dropoff',
                    'zone' => $addonCharge->charge_name,
                    'charge_name' => $addonCharge->charge_name,
                    'amount' => $dropoffAddonCharge
                ];
            }
        }

        // Calculate total addon charge
        $totalAddonCharge = $pickupAddonCharge + $dropoffAddonCharge;
        
        \Log::info('Airport Charges Calculation', [
            'pickup_charge' => $pickupAddonCharge,
            'dropoff_charge' => $dropoffAddonCharge,
            'total_airport_charges' => $totalAddonCharge,
            'applied_charges' => $appliedCharges
        ]);

        // Apply addon charges to all vehicle types
        $saloonPrice = (float) $pricing->saloon_price + $totalAddonCharge;
        $businessPrice = (float) $pricing->business_price + $totalAddonCharge;
        $mpv6Price = (float) $pricing->mpv6_price + $totalAddonCharge;
        $mpv8Price = (float) $pricing->mpv8_price + $totalAddonCharge;
        
        \Log::info('Final Prices Calculated', [
            'base_prices' => [
                'saloon' => $pricing->saloon_price,
                'business' => $pricing->business_price,
                'mpv6' => $pricing->mpv6_price,
                'mpv8' => $pricing->mpv8_price
            ],
            'airport_charges_added' => $totalAddonCharge,
            'final_prices' => [
                'saloon' => $saloonPrice,
                'business' => $businessPrice,
                'mpv6' => $mpv6Price,
                'mpv8' => $mpv8Price
            ]
        ]);
        \Log::info('=== PRICE CALCULATION END ===');

        // map vehicle type to price column
        $veh = $data['vehicle_type'] ?? null;
        $priceCols = ['saloon_price','business_price','mpv6_price','mpv8_price'];
        $selectedPrice = null;
        if ($veh) {
            // simple matching: check keys contain vehicle type string
            $vehicleTypePrices = [
                'saloon_price' => $saloonPrice,
                'business_price' => $businessPrice,
                'mpv6_price' => $mpv6Price,
                'mpv8_price' => $mpv8Price
            ];
            foreach ($priceCols as $col) {
                if (strtolower($col) === strtolower($veh.'_price')) { 
                    $selectedPrice = $vehicleTypePrices[$col]; 
                    break; 
                }
            }
        }
        // fallback to saloon if none selected
        if ($selectedPrice === null) $selectedPrice = $saloonPrice;

        $response = [
            'success' => true, 
            'pricing_type' => $pricingType, 
            'pricing' => [
                'id' => $pricing->id, 
                'base_saloon_price' => $pricing->saloon_price, 
                'base_business_price' => $pricing->business_price, 
                'base_mpv6_price' => $pricing->mpv6_price, 
                'base_mpv8_price' => $pricing->mpv8_price,
                'saloon_price' => $saloonPrice, 
                'business_price' => $businessPrice, 
                'mpv6_price' => $mpv6Price, 
                'mpv8_price' => $mpv8Price, 
                'selected_price' => $selectedPrice,
                'airport_charges' => $totalAddonCharge,
                'applied_charges' => $appliedCharges
            ]
        ];

        if ($pricingType === 'zone') {
            $response['pickup_zone'] = $pickupZone; $response['dropoff_zone'] = $dropZone;
        } else if ($pricingType === 'postcode') {
            $response['pickup_postcode'] = $pp ?? null; $response['dropoff_postcode'] = $dp ?? null;
        } else {
            // mileage
            $response['mileage'] = ['start_mile' => $pricing->start_mile, 'end_mile' => $pricing->end_mile, 'is_fixed' => $pricing->is_fixed_charge];
        }

        return response()->json($response);
    }

    // helper to find zone by lat/lon (returns array or null)
    protected function findZoneForPoint(float $lat, float $lon)
    {
        $zones = Zone::whereNotNull('meta')->get()->filter(function($z){ return isset($z->meta['polygon']) && is_array($z->meta['polygon']); });
        foreach ($zones as $z) {
            $poly = $z->meta['polygon'];
            if (!isset($poly['coordinates'][0]) || !is_array($poly['coordinates'][0])) continue;
            try {
                $bbox = $this->polygonBbox($poly);
                if ($this->bboxesIntersect($bbox, $bbox)) {
                    if ($this->pointInPolygon([$lon, $lat], $poly['coordinates'][0])) {
                        return ['id' => $z->id, 'zone_name' => $z->zone_name];
                    }
                }
            } catch (\Exception $e) { /* ignore and continue */ }
        }
        return null;
    }

    // helper: check airport polygons loaded from airport_zones.json
    protected function findAirportForPoint(float $lat, float $lon)
    {
        $zones = $this->loadAirportZones();
        if (! $zones || ! is_array($zones)) return null;

        foreach ($zones as $z) {
            $zoneName = trim($z['zone_name'] ?? ($z['name'] ?? ''));
            if (! $zoneName) continue;

            $meta = $z['meta'] ?? null;
            // meta may be a JSON string or an array
            if (is_string($meta)) {
                try { $meta = json_decode($meta, true); } catch (\Throwable $e) { $meta = null; }
            }

            $poly = $meta['polygon'] ?? ($meta['geometry'] ?? null);
            if (! $poly || ! isset($poly['coordinates'][0]) || ! is_array($poly['coordinates'][0])) continue;

            try {
                if ($this->pointInPolygon([$lon, $lat], $poly['coordinates'][0])) {
                    return $zoneName;
                }
            } catch (\Exception $e) { /* ignore and continue */ }
        }

        return null;
    }

    // helper: load airport_zones.json from project root and cache it
    protected function loadAirportZones(): array
    {
        static $cache = null;
        if ($cache !== null) return $cache;

        $path = base_path('airport_zones.json');
        if (! file_exists($path)) {
            $cache = [];
            return $cache;
        }

        try {
            $json = file_get_contents($path);
            $data = json_decode($json, true);
            if (! is_array($data)) $data = [];
            $cache = $data;
            return $cache;
        } catch (\Throwable $e) {
            $cache = [];
            return $cache;
        }
    }

    // helper: permissive match between addon charge and zone name
    protected function chargeMatchesZone(string $chargeName, string $zoneName, $searchTerm = null): bool
    {
        $cn = strtolower(trim($chargeName));
        $zn = strtolower(trim($zoneName));
        $st = strtolower(trim((string)($searchTerm ?? '')));

        if ($cn === '' || $zn === '') return false;

        // exact or substring matches (either direction)
        if ($cn === $st || $cn === $zn) return true;
        if (str_contains($cn, $st) || str_contains($cn, $zn) || str_contains($zn, $cn) || ($st && str_contains($st, $cn))) return true;

        // token overlap (words of length >=3) ignoring generic stopwords like 'airport'
        $stop = ['airport','london','the','charges','meet','greet','zone','congestion','vat'];
        $tokCn = array_filter(preg_split('/[^a-z0-9]+/', $cn));
        $tokZn = array_filter(preg_split('/[^a-z0-9]+/', $zn));
        $tokCn = array_filter($tokCn, fn($t) => strlen($t) >= 3 && ! in_array($t, $stop));
        $tokZn = array_filter($tokZn, fn($t) => strlen($t) >= 3 && ! in_array($t, $stop));
        foreach ($tokCn as $t) {
            foreach ($tokZn as $u) {
                if ($t === $u) return true;
            }
        }

        // try removing common words and match again
        $strip = function($s){ return trim(preg_replace('/\b(london|the|airport|airport\b)\b/','',$s)); };
        $cn2 = $strip($cn); $zn2 = $strip($zn);
        if ($cn2 && $zn2 && (str_contains($cn2, $zn2) || str_contains($zn2, $cn2))) return true;

        return false;
    }

    // choose the best matching addon charge from a list using simple scoring
    protected function selectBestAddonCharge($charges, string $zoneName, $searchTerm = null)
    {
        $best = null; $bestScore = 0;
        foreach ($charges as $ac) {
            $score = 0;
            $cn = strtolower(trim($ac->charge_name ?? ''));
            $zn = strtolower(trim($zoneName));
            $st = strtolower(trim((string)($searchTerm ?? '')));

            if ($cn === $zn || $cn === $st) { $score += 200; }
            if ($st && str_contains($cn, $st)) { $score += 80; }
            if (str_contains($cn, $zn)) { $score += 60; }
            if (str_contains($zn, $cn)) { $score += 40; }

            // token overlap weight (ignore stopwords)
            $stop = ['airport','london','the','charges','meet','greet','zone','congestion','vat'];
            $tokCn = array_filter(preg_split('/[^a-z0-9]+/', $cn));
            $tokZn = array_filter(preg_split('/[^a-z0-9]+/', $zn));
            $tokCn = array_filter($tokCn, fn($t) => strlen($t) >= 3 && ! in_array($t, $stop));
            $tokZn = array_filter($tokZn, fn($t) => strlen($t) >= 3 && ! in_array($t, $stop));
            $overlap = count(array_intersect($tokCn, $tokZn));
            $score += $overlap * 30;

            // small boost for longer exact matches
            $score += min(20, strlen($cn) / 4);

            if ($score > $bestScore) { $bestScore = $score; $best = $ac; }
        }

        return $bestScore > 0 ? $best : null;
    }

    // Delete a Zone (map polygon)
    public function destroyZone(Request $request, Zone $zone)
    {
        // Keep simple: delete the zone row. Higher-level data (pricing) will rely on foreign keys where applicable
        try {
            $zone->delete();
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Failed to delete zone'], 500);
            }
            return redirect()->back()->with('error', 'Failed to delete zone');
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->route('admin.pricing.zones.map')->with('success', 'Zone deleted');
    }

    // Helper: compute simple bbox for a polygon GeoJSON (first ring only)
    protected function polygonBbox(array $poly)
    {
        if (!isset($poly['coordinates'][0]) || !is_array($poly['coordinates'][0])) return null;
        $minX = $minY = PHP_FLOAT_MAX; $maxX = $maxY = -PHP_FLOAT_MAX;
        foreach ($poly['coordinates'][0] as $pt) {
            $lng = (float) ($pt[0] ?? 0);
            $lat = (float) ($pt[1] ?? 0);
            if ($lng < $minX) $minX = $lng;
            if ($lng > $maxX) $maxX = $lng;
            if ($lat < $minY) $minY = $lat;
            if ($lat > $maxY) $maxY = $lat;
        }
        return [$minX, $minY, $maxX, $maxY];
    }

    // Helper: test bbox intersection
    protected function bboxesIntersect($a, $b)
    {
        if (!$a || !$b) return false;
        // a = [minX,minY,maxX,maxY]
        return !($a[2] < $b[0] || $a[0] > $b[2] || $a[3] < $b[1] || $a[1] > $b[3]);
    }

    // Helper: robust polygon intersection test (segment intersections + point-in-polygon)
    protected function polygonsIntersect(array $polyA, array $polyB)
    {
        try {
            $coordsA = $polyA['coordinates'][0] ?? [];
            $coordsB = $polyB['coordinates'][0] ?? [];
            if (!$coordsA || !$coordsB) return false;

            // normalize: ensure rings are closed
            $closeRing = function(&$coords){ if (count($coords) > 0) { $first = $coords[0]; $last = $coords[count($coords)-1]; if ($first[0] !== $last[0] || $first[1] !== $last[1]) $coords[] = $first; } };
            $closeRing($coordsA); $closeRing($coordsB);

            // 1) segment intersection test
            for ($i = 0; $i < count($coordsA)-1; $i++) {
                $p1 = $coordsA[$i]; $p2 = $coordsA[$i+1];
                for ($j = 0; $j < count($coordsB)-1; $j++) {
                    $q1 = $coordsB[$j]; $q2 = $coordsB[$j+1];
                    if ($this->segmentsIntersect($p1, $p2, $q1, $q2)) return true;
                }
            }

            // 2) point-in-polygon tests (A point inside B or B point inside A)
            foreach ($coordsA as $pt) {
                if ($this->pointInPolygon($pt, $coordsB)) return true;
            }
            foreach ($coordsB as $pt) {
                if ($this->pointInPolygon($pt, $coordsA)) return true;
            }

            return false;
        } catch (\Exception $e) {
            // if anything fails, be conservative and report an intersection
            return true;
        }
    }

    // Helper: point-in-polygon using ray-casting (point and poly coords are [lng,lat])
    protected function pointInPolygon(array $point, array $ring)
    {
        $x = $point[0]; $y = $point[1];
        $inside = false;
        $j = count($ring) - 1;
        for ($i = 0; $i < count($ring); $i++) {
            $xi = $ring[$i][0]; $yi = $ring[$i][1];
            $xj = $ring[$j][0]; $yj = $ring[$j][1];
            $intersect = (($yi > $y) != ($yj > $y)) && ($x < ($xj - $xi) * ($y - $yi) / (($yj - $yi) ?: 1e-12) + $xi);
            if ($intersect) $inside = !$inside;
            $j = $i;
        }
        return $inside;
    }

    // Helper: check if segments p1-p2 and q1-q2 intersect. Points are [lng,lat]
    protected function segmentsIntersect(array $p1, array $p2, array $q1, array $q2)
    {
        // orientation test
        $orient = function($a, $b, $c){
            return ($b[1] - $a[1]) * ($c[0] - $b[0]) - ($b[0] - $a[0]) * ($c[1] - $b[1]);
        };

        $onSegment = function($a, $b, $c){ // c on segment ab
            return ($c[0] <= max($a[0], $b[0]) && $c[0] >= min($a[0], $b[0]) && $c[1] <= max($a[1], $b[1]) && $c[1] >= min($a[1], $b[1]));
        };

        $o1 = $orient($p1, $p2, $q1);
        $o2 = $orient($p1, $p2, $q2);
        $o3 = $orient($q1, $q2, $p1);
        $o4 = $orient($q1, $q2, $p2);

        if (($o1 > 0 && $o2 < 0 || $o1 < 0 && $o2 > 0) && ($o3 > 0 && $o4 < 0 || $o3 < 0 && $o4 > 0)) return true;

        // collinear checks
        if (abs($o1) < 1e-12 && $onSegment($p1, $p2, $q1)) return true;
        if (abs($o2) < 1e-12 && $onSegment($p1, $p2, $q2)) return true;
        if (abs($o3) < 1e-12 && $onSegment($q1, $q2, $p1)) return true;
        if (abs($o4) < 1e-12 && $onSegment($q1, $q2, $p2)) return true;

        return false;
    }
    // Update polygon (and optionally name) for an existing zone
    public function updateMap(Request $request, Zone $zone)
    {
        $data = $request->validate([
            'zone_name' => ['required','string','max:255', Rule::unique('zones','zone_name')->ignore($zone->id)],
            'polygon' => 'nullable|json'
        ]);

        $meta = $zone->meta ?? [];
        $latitude = $zone->latitude; $longitude = $zone->longitude;

        if (!empty($data['polygon'])) {
            $meta['polygon'] = json_decode($data['polygon'], true);

            // compute centroid
            try {
                $coords = $meta['polygon']['coordinates'][0];
                $sumLat = 0; $sumLng = 0; $count = 0;
                foreach ($coords as $pt) {
                    $lng = (float) $pt[0];
                    $lat = (float) $pt[1];
                    $sumLat += $lat; $sumLng += $lng; $count++;
                }
                if ($count) {
                    $latitude = $sumLat / $count;
                    $longitude = $sumLng / $count;
                }
            } catch (\Exception $e) {}
        }

        // server-side bbox check to avoid obvious overlaps (ignore self)
        try {
            if (!empty($meta['polygon'])) {
                $incomingBbox = $this->polygonBbox($meta['polygon']);
                $zones = Zone::whereNotNull('meta')->get()->filter(function($z) use ($zone){ return isset($z->meta['polygon']) && is_array($z->meta['polygon']) && $z->id != $zone->id; });
                foreach ($zones as $existing) {
                    $existingBbox = $this->polygonBbox($existing->meta['polygon']);
                    if ($this->bboxesIntersect($incomingBbox, $existingBbox)) {
                        // perform a more accurate polygon intersection test
                        if ($this->polygonsIntersect($meta['polygon'], $existing->meta['polygon'])) {
                            if ($request->ajax() || $request->wantsJson()) {
                                return response()->json(['success' => false, 'message' => 'Updated polygon overlaps an existing zone: ' . $existing->zone_name], 422);
                            }
                            return redirect()->back()->withErrors(['polygon' => 'Updated polygon overlaps an existing zone: ' . $existing->zone_name])->withInput();
                        }
                    }
                }

                // ensure polygon stays inside UK bbox
                $coordsForCheck = $meta['polygon']['coordinates'][0] ?? [];
                foreach ($coordsForCheck as $pt) {
                    $lng = (float) ($pt[0] ?? 0);
                    $lat = (float) ($pt[1] ?? 0);
                    if ($lat < 49.5 || $lat > 61.0 || $lng < -8.6 || $lng > 2.1) {
                        if ($request->ajax() || $request->wantsJson()) {
                            return response()->json(['success' => false, 'message' => 'Updated polygon must be entirely within the UK'], 422);
                        }
                        return redirect()->back()->withErrors(['polygon' => 'Updated polygon must be entirely within the UK'])->withInput();
                    }
                }
            }
        } catch (\Exception $e) {
            // ignore bbox failures
        }

        $zone->update([
            'zone_name' => $data['zone_name'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'meta' => $meta
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            $optionHtml = view('admin.pricing.zones._option', ['zone' => $zone])->render();
            return response()->json(['success' => true, 'item' => $zone, 'option_html' => $optionHtml], 200);
        }

        return redirect()->route('admin.pricing.zones.index')->with('success','Zone updated');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'from_zone_id' => 'required|exists:zones,id',
            // allow same-zone pricing (from == to)
            'to_zone_id' => 'required|exists:zones,id',
            'saloon_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'mpv6_price' => 'nullable|numeric|min:0',
            'mpv8_price' => 'nullable|numeric|min:0',
            'pricing_mode' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $created = PricingZone::create($data);

        // mirror pricing for reverse direction (to -> from) using same prices (skip if same-zone)
        try {
            if ($data['from_zone_id'] != $data['to_zone_id']) {
                $reverseData = $data;
                $reverseData['from_zone_id'] = $data['to_zone_id'];
                $reverseData['to_zone_id'] = $data['from_zone_id'];
                // use updateOrCreate to avoid unique constraint errors and ensure it matches
                PricingZone::updateOrCreate(
                    ['from_zone_id' => $reverseData['from_zone_id'], 'to_zone_id' => $reverseData['to_zone_id']],
                    $reverseData
                );
            }
        } catch (\Exception $e) {
            // ignore reverse creation failures, but log for debugging
            logger()->warning('Failed to create reverse pricing: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $created], 201);
        }

        return redirect()->route('admin.pricing.zones.index')->with('success','Zone pricing created');
    }

    public function edit(Request $request, PricingZone $zone)
    {
        $zones = Zone::orderBy('zone_name')->get();
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.pricing.zones._modal_form', ['zones' => $zones, 'item' => $zone]);
        }

        return view('admin.pricing.zones.edit', ['zones' => $zones, 'item' => $zone]);
    }

    public function update(Request $request, PricingZone $zone)
    {
        $data = $request->validate([
            'from_zone_id' => 'required|exists:zones,id',
            // allow same-zone pricing (from == to)
            'to_zone_id' => 'required|exists:zones,id',
            'saloon_price' => 'nullable|numeric|min:0',
            'business_price' => 'nullable|numeric|min:0',
            'mpv6_price' => 'nullable|numeric|min:0',
            'mpv8_price' => 'nullable|numeric|min:0',
            'pricing_mode' => 'nullable|string',
            'status' => 'required|in:active,inactive'
        ]);

        $zone->update($data);

        // NOTE: do not auto-update reverse pricing on updates. Reverse entries are created at store time only.
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'item' => $zone], 200);
        }

        return redirect()->route('admin.pricing.zones.index')->with('success','Zone pricing updated');
    }

    public function destroy(Request $request, PricingZone $zone)
    {
        // capture ids before deleting
        $from = $zone->from_zone_id;
        $to = $zone->to_zone_id;

        $zone->delete();

        // delete reverse pricing, if exists (skip if same-zone since original deletion already removed it)
        try {
            if ($from != $to) {
                PricingZone::where('from_zone_id', $to)->where('to_zone_id', $from)->delete();
            }
        } catch (\Exception $e) {
            logger()->warning('Failed to delete reverse pricing: ' . $e->getMessage());
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true], 200);
        }

        return redirect()->to(route('admin.pricing.index') . '#zone')->with('success','Zone pricing deleted');
    }
}
