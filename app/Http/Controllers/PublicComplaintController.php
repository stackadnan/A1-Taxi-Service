<?php

namespace App\Http\Controllers;

use App\Models\ComplaintLostFound;
use Illuminate\Http\Request;

class PublicComplaintController extends Controller
{
    public function preflight(Request $request)
    {
        return response('', 200)->withHeaders($this->corsHeaders($request));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'booking_id' => ['nullable', 'string', 'max:255'],
            'concern' => ['required', 'string', 'max:5000'],
            'lost_found' => ['required', 'string', 'max:5000'],
            'source_url' => ['nullable', 'string', 'max:500'],
        ]);

        $complaint = ComplaintLostFound::create([
            'booking_id' => $this->blankToNull($data['booking_id'] ?? null),
            'name' => trim($data['name']),
            'email' => strtolower(trim($data['email'])),
            'concern' => trim($data['concern']),
            'lost_found' => trim($data['lost_found']),
            'status' => ComplaintLostFound::STATUS_NEW,
            'source_ip' => $request->ip(),
            'source_url' => $data['source_url'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Your complaint/lost-found request has been submitted successfully.',
            'id' => $complaint->id,
        ])->withHeaders($this->corsHeaders($request));
    }

    protected function blankToNull(mixed $value): mixed
    {
        if (!is_string($value)) {
            return $value;
        }

        $value = trim($value);

        return $value === '' ? null : $value;
    }

    protected function corsHeaders(Request $request): array
    {
        $origin = $request->header('Origin', '*');

        $allowed = ['executiveairportcars.com', 'www.executiveairportcars.com', 'admin.executiveairportcars.com'];
        $host = parse_url($origin, PHP_URL_HOST) ?? '';
        $allowedOrigin = (in_array($host, $allowed) || str_ends_with($host, '.executiveairportcars.com') || empty($host))
            ? $origin
            : '*';

        return [
            'Access-Control-Allow-Origin' => $allowedOrigin,
            'Access-Control-Allow-Methods' => 'POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Content-Type, X-Requested-With, Accept',
            'Access-Control-Allow-Credentials' => 'false',
            'Access-Control-Max-Age' => '86400',
        ];
    }
}
