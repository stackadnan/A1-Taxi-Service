<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReviewsController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with('driver')->whereNotNull('review_status');

        if ($request->filled('status') && in_array((string) $request->input('status'), ['0', '1', '2'], true)) {
            $query->where('review_status', (int) $request->input('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($inner) use ($search) {
                $inner->where('booking_code', 'like', "%{$search}%")
                    ->orWhere('passenger_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $bookings = $query
            ->orderByRaw('CASE WHEN review_status = 1 THEN 0 WHEN review_status = 2 THEN 1 WHEN review_status = 0 THEN 2 ELSE 3 END')
            ->orderByDesc('review_requested_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $totalCount = Booking::whereNotNull('review_status')->count();
        $pendingCount = Booking::where('review_status', Booking::REVIEW_PENDING)->count();
        $approvedCount = Booking::where('review_status', Booking::REVIEW_APPROVED)->count();
        $rejectedCount = Booking::where('review_status', Booking::REVIEW_REJECTED)->count();

        return view('admin.reviews.index', compact('bookings', 'totalCount', 'pendingCount', 'approvedCount', 'rejectedCount'));
    }

    public function approve(Request $request, Booking $booking)
    {
        if (!$booking->email) {
            return $this->respondReviewAction($request, false, 'Customer email is missing for this booking.', 422);
        }

        $booking->review_status = Booking::REVIEW_APPROVED;
        $booking->review_requested_at = $booking->review_requested_at ?: now();
        $booking->review_approved_at = now();
        $booking->review_rejected_at = null;
        $booking->save();

        if (!$booking->review_email_sent_at) {
            try {
                $this->sendReviewApprovalEmail($booking);
                $booking->review_email_sent_at = now();
                $booking->save();
            } catch (\Throwable $e) {
                logger()->error('Failed to send review approval email', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);

                return $this->respondReviewAction($request, false, 'Review approved, but email could not be sent. ' . (config('app.debug') ? $e->getMessage() : ''), 500);
            }
        }

        return $this->respondReviewAction($request, true, 'Review approved and email sent successfully.');
    }

    public function reject(Request $request, Booking $booking)
    {
        $booking->review_status = Booking::REVIEW_REJECTED;
        $booking->review_requested_at = $booking->review_requested_at ?: now();
        $booking->review_rejected_at = now();
        $booking->review_approved_at = null;
        $booking->review_email_sent_at = null;
        $booking->save();

        return $this->respondReviewAction($request, true, 'Review rejected successfully.');
    }

    protected function sendReviewApprovalEmail(Booking $booking): void
    {
        $customerEmail = trim((string) ($booking->email ?? ''));
        if ($customerEmail === '') {
            throw new \RuntimeException('Customer email is missing for this booking.');
        }

        $links = $this->reviewLinks();
        $bookingRef = $booking->booking_code ?? ('#' . $booking->id);
        $pickupDate = optional($booking->pickup_date)->format('Y-m-d') ?: '-';
        $pickupTime = $booking->pickup_time ?: '-';
        $driverName = $booking->driver->name ?? ($booking->driver_name ?? '-');

        $linkButtons = '';
        foreach ($links as $link) {
            $linkButtons .= '<a href="' . e($link['url']) . '" target="_blank" rel="noopener" style="display:inline-block;margin:8px 10px 0 0;padding:12px 18px;border-radius:8px;background:#1e293b;color:#ffffff;text-decoration:none;font-weight:600;">' . e($link['label']) . '</a>';
        }

        $body = '<div style="font-family:Arial,Helvetica,sans-serif;color:#111827;line-height:1.6;">'
            . '<p>Dear ' . e($booking->passenger_name ?: 'Customer') . ',</p>'
            . '<p>Thank you for travelling with us. We would appreciate it if you could leave a review for your recent trip.</p>'
            . '<p><strong>Booking Reference:</strong> ' . e($bookingRef) . '<br>'
            . '<strong>Pickup:</strong> ' . e($booking->pickup_address ?: '-') . '<br>'
            . '<strong>Dropoff:</strong> ' . e($booking->dropoff_address ?: '-') . '<br>'
            . '<strong>Pickup Date:</strong> ' . e($pickupDate) . '<br>'
            . '<strong>Pickup Time:</strong> ' . e($pickupTime) . '<br>'
            . '<strong>Driver:</strong> ' . e($driverName) . '</p>'
            . '<p>Please use one of the links below to leave your review:</p>'
            . '<div>' . $linkButtons . '</div>'
            . '<p style="margin-top:20px;">Thank you for choosing our service.</p>'
            . '</div>';

        $this->sendHtmlMail($customerEmail, 'Please review your journey - ' . $bookingRef, $body);
    }

    protected function reviewLinks(): array
    {
        $platforms = DB::table('review_platforms')
            ->where('status', 'active')
            ->orderBy('platform_name')
            ->get();

        $links = [];
        foreach ($platforms as $platform) {
            $name = trim((string) $platform->platform_name);
            if ($name === '') {
                continue;
            }

            $url = trim((string) ($platform->review_url ?? ''));
            if ($url === '') {
                $lower = strtolower($name);
                if (str_contains($lower, 'google')) {
                    $url = 'https://www.google.com/maps/search/?api=1&query=' . urlencode((string) config('app.name', 'Airport Services'));
                } elseif (str_contains($lower, 'trust')) {
                    $url = 'https://www.trustpilot.com/';
                } elseif (str_contains($lower, 'facebook')) {
                    $url = 'https://www.facebook.com/';
                } else {
                    $url = (string) config('app.url');
                }
            }

            $links[] = ['label' => $name, 'url' => $url];
        }

        if (empty($links)) {
            $links = [
                ['label' => 'Google Maps', 'url' => 'https://www.google.com/maps/search/?api=1&query=' . urlencode((string) config('app.name', 'Airport Services'))],
                ['label' => 'Trustpilot', 'url' => 'https://www.trustpilot.com/'],
                ['label' => 'Facebook', 'url' => 'https://www.facebook.com/'],
            ];
        }

        return $links;
    }

    protected function sendHtmlMail(string $to, string $subject, string $body): void
    {
        Mail::html($body, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    protected function respondReviewAction(Request $request, bool $success, string $message, int $status = 200)
    {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => $success, 'message' => $message], $status);
        }

        return $success
            ? redirect()->back()->with('success', $message)
            : redirect()->back()->with('error', $message);
    }
}