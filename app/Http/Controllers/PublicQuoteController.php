<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Pricing\ZoneController;
use App\Models\PublicQuoteRequest;

/**
 * Public-facing quote API controller.
 * No authentication required. CORS headers added for cross-domain calls
 * from executiveairportcars.com → admin.executiveairportcars.com.
 */
class PublicQuoteController extends Controller
{
    /**
     * Handle CORS pre-flight OPTIONS request.
     */
    public function preflight(Request $request)
    {
        return response('', 200)->withHeaders($this->corsHeaders($request));
    }

    /**
     * Calculate a price quote for a given pickup/dropoff.
     * Delegates to the existing ZoneController pricing engine.
     *
     * Expected POST fields (from frontend after geocoding):
     *   pickup_lat, pickup_lon, dropoff_lat, dropoff_lon  — required
     *   pickup_address, dropoff_address                   — optional, for logging
     *   pickup_postcode, dropoff_postcode                 — optional, improves postcode pricing
     *   distance_miles                                    — optional, client-computed driving distance
     *   date                                              — optional, stored in log
     *   source_url                                        — optional, tracked for analytics
     */
    public function quote(Request $request)
    {
        // --- Log the public inquiry for analytics (non-blocking) ---
        try {
            \Log::channel('daily')->info('Public quote request', [
                'ip'              => $request->ip(),
                'source_url'      => $request->input('source_url'),
                'pickup_address'  => $request->input('pickup_address'),
                'dropoff_address' => $request->input('dropoff_address'),
                'date'            => $request->input('date'),
                'pickup_lat'      => $request->input('pickup_lat'),
                'pickup_lon'      => $request->input('pickup_lon'),
                'dropoff_lat'     => $request->input('dropoff_lat'),
                'dropoff_lon'     => $request->input('dropoff_lon'),
            ]);
        } catch (\Throwable $e) {
            // Never let logging break the quote response
        }

        // --- Delegate to the existing pricing engine ---
        try {
            /** @var ZoneController $zoneController */
            $zoneController = app(ZoneController::class);
            $response = $zoneController->quote($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide valid pickup and dropoff coordinates.',
                'errors'  => $e->errors(),
            ], 422)->withHeaders($this->corsHeaders($request));
        } catch (\Throwable $e) {
            \Log::error('PublicQuoteController: pricing engine error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Unable to calculate quote at this time. Please call us directly.',
            ], 500)->withHeaders($this->corsHeaders($request));
        }

        // Attach CORS headers to whatever the pricing engine returned
        return $response->withHeaders($this->corsHeaders($request));
    }

    /**
     * Build CORS response headers.
     * Allows requests from the main domain and localhost for development.
     */
    protected function corsHeaders(Request $request): array
    {
        $origin = $request->header('Origin', '*');

        // Only echo back origins we trust; fall back to wildcard for direct/curl calls
        $allowed = ['executiveairportcars.com', 'www.executiveairportcars.com', 'admin.executiveairportcars.com'];
        $host = parse_url($origin, PHP_URL_HOST) ?? '';
        $allowedOrigin = (in_array($host, $allowed) || str_ends_with($host, '.executiveairportcars.com') || empty($host))
            ? $origin
            : '*';

        return [
            'Access-Control-Allow-Origin'      => $allowedOrigin,
            'Access-Control-Allow-Methods'     => 'POST, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Content-Type, X-Requested-With, Accept',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Max-Age'           => '86400',
        ];
    }

    /**
     * Save a quote request to the database.
     * If trip_type is 'return', two rows are created (outbound + return) with linked_quote_ref.
     *
     * Expected POST fields:
     *   pickup_address, dropoff_address, pickup_date
     *   vehicle_type, price, trip_type (one-way|return)
     *   source_url  — optional
     */
    public function save(Request $request)
    {
        $data = $request->validate([
            'pickup_address'  => 'required|string|max:500',
            'dropoff_address' => 'required|string|max:500',
            'pickup_date'     => 'nullable|string|max:30',
            'vehicle_type'    => 'required|string|max:50',
            'price'           => 'required|numeric|min:0',
            'trip_type'       => 'required|in:one-way,return',
            'source_url'      => 'nullable|string|max:500',
        ]);

        $ip         = $request->ip();
        $sourceUrl  = $data['source_url'] ?? null;
        $pickupDate = $data['pickup_date'] ?? null;

        try {
            if ($data['trip_type'] === 'return') {
                // Two rows: outbound (one-way price) + return (full return price)
                $oneWayPrice   = round($data['price'] / 2, 2); // price passed is already doubled
                $returnPrice   = $data['price'];

                $ref1 = $this->generateQuoteRef();
                $ref2 = $this->generateQuoteRef();

                // Outbound row
                $row1 = PublicQuoteRequest::create([
                    'quote_ref'        => $ref1,
                    'pickup_address'   => $data['pickup_address'],
                    'dropoff_address'  => $data['dropoff_address'],
                    'pickup_date'      => $pickupDate,
                    'source_ip'        => $ip,
                    'source_url'       => $sourceUrl,
                    'vehicle_type'     => $data['vehicle_type'],
                    'price'            => $oneWayPrice,
                    'trip_type'        => 'return',
                    'linked_quote_ref' => $ref2,
                ]);

                // Return leg row (swapped addresses)
                $row2 = PublicQuoteRequest::create([
                    'quote_ref'        => $ref2,
                    'pickup_address'   => $data['dropoff_address'],
                    'dropoff_address'  => $data['pickup_address'],
                    'pickup_date'      => $pickupDate,
                    'source_ip'        => $ip,
                    'source_url'       => $sourceUrl,
                    'vehicle_type'     => $data['vehicle_type'],
                    'price'            => $oneWayPrice,
                    'trip_type'        => 'return',
                    'linked_quote_ref' => $ref1,
                ]);

                \Log::channel('daily')->info('Public quote saved (return)', [
                    'ref1' => $ref1, 'ref2' => $ref2, 'ip' => $ip,
                    'vehicle' => $data['vehicle_type'], 'price_each' => $oneWayPrice,
                ]);

                return response()->json([
                    'success'     => true,
                    'trip_type'   => 'return',
                    'quote_ref'   => $ref1,
                    'return_ref'  => $ref2,
                    'rows'        => [$row1->toArray(), $row2->toArray()],
                ])->withHeaders($this->corsHeaders($request));

            } else {
                $ref = $this->generateQuoteRef();

                $row = PublicQuoteRequest::create([
                    'quote_ref'       => $ref,
                    'pickup_address'  => $data['pickup_address'],
                    'dropoff_address' => $data['dropoff_address'],
                    'pickup_date'     => $pickupDate,
                    'source_ip'       => $ip,
                    'source_url'      => $sourceUrl,
                    'vehicle_type'    => $data['vehicle_type'],
                    'price'           => $data['price'],
                    'trip_type'       => 'one-way',
                ]);

                \Log::channel('daily')->info('Public quote saved (one-way)', [
                    'ref' => $ref, 'ip' => $ip,
                    'vehicle' => $data['vehicle_type'], 'price' => $data['price'],
                ]);

                return response()->json([
                    'success'   => true,
                    'trip_type' => 'one-way',
                    'quote_ref' => $ref,
                    'row'       => $row->toArray(),
                ])->withHeaders($this->corsHeaders($request));
            }
        } catch (\Throwable $e) {
            \Log::error('PublicQuoteController::save error', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Could not save quote request.',
            ], 500)->withHeaders($this->corsHeaders($request));
        }
    }

    /**
     * Generate a unique quote reference like QR123456.
     */
    protected function generateQuoteRef(): string
    {
        for ($i = 0; $i < 10; $i++) {
            $ref = 'QR' . random_int(100000, 999999);
            if (! PublicQuoteRequest::where('quote_ref', $ref)->exists()) {
                return $ref;
            }
        }
        // last resort — append microseconds
        return 'QR' . substr(str_replace('.', '', microtime(true)), -8);
    }
}
