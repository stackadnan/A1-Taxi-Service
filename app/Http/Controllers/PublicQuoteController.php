<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\Pricing\ZoneController;

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
}
