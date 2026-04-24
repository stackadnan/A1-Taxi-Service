<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\Booking;
use App\Models\AdminSetting;
use App\Models\DriverInvoice;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class DriverController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $tab = $request->get('tab', 'active');
        $timingSettings = AdminSetting::driverWarningThresholds();

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
            $soon = \Carbon\Carbon::today()->addDays(15)->toDateString();
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
                        if ($expiryDate->lte($today->copy()->addDays(15))) {
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
                    $meta = $currentBooking->meta ?? [];
                    $isInRoute        = isset($meta['in_route']) && $meta['in_route'] === true;
                    $isArrivedPickup  = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;
                    $statusKey = $currentBooking->status->name ?? 'in_progress';
                    
                    // Priority: POB > arrived_at_pickup > in_route > other
                    if ($statusKey === 'pob') {
                        $label = 'POB';
                        $color = 'orange';
                        $sinceFrom = isset($meta['pob_marked_at']) ? $meta['pob_marked_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } elseif ($isArrivedPickup) {
                        $label = 'Arrived';
                        $color = 'blue';
                        $sinceFrom = isset($meta['arrived_at_pickup_at']) ? $meta['arrived_at_pickup_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } elseif ($isInRoute) {
                        $label = 'In Route';
                        $color = 'purple';
                        $sinceFrom = isset($meta['in_route_at']) ? $meta['in_route_at'] : ($currentBooking->updated_at ?? $drv->last_active_at);
                    } else {
                        $labelMap = [
                            'in_progress' => ['On Route', 'green'],
                            'confirmed' => ['Accepted', 'yellow'],
                            'new' => ['New', 'gray'],
                        ];
                        $label = $labelMap[$statusKey][0] ?? ucfirst(str_replace('_', ' ', $statusKey));
                        $color = $labelMap[$statusKey][1] ?? 'gray';
                        $sinceFrom = $drv->last_assigned_at ?? $currentBooking->updated_at ?? $drv->last_active_at;
                    }
                } else {
                    $label = 'Idle';
                    $color = 'yellow';

                    // Show idle time since the driver's last completed booking if available,
                    // otherwise fall back to driver's last_active_at
                    $lastCompleted = \App\Models\Booking::where('driver_id', $drv->id)
                        ->whereHas('status', function($q){ $q->where('name', 'completed'); })
                        ->orderBy('updated_at', 'desc')
                        ->first();

                    // Debug log to check what we're finding
                    if ($lastCompleted) {
                        $meta = $lastCompleted->meta ?? [];
                        $completed_at = $meta['completed_at'] ?? null;
                        \Log::info("Driver {$drv->id} last completed booking found", [
                            'booking_id' => $lastCompleted->id,
                            'updated_at' => $lastCompleted->updated_at,
                            'meta_completed_at' => $completed_at,
                            'meta_keys' => array_keys($meta)
                        ]);
                        $sinceFrom = $completed_at ?? $lastCompleted->updated_at ?? $drv->last_active_at;
                    } else {
                        \Log::info("Driver {$drv->id} no completed bookings found, using last_active_at", [
                            'last_active_at' => $drv->last_active_at
                        ]);
                        $sinceFrom = $drv->last_active_at;
                    }
                }

                $sinceStr = '-';
                if ($sinceFrom) {
                    try {
                        $sinceCarbon = \Carbon\Carbon::parse($sinceFrom);
                        // Format as "HH:MM DD/MM/YYYY"
                        $formatted = $sinceCarbon->format('H:i d/m/Y');

                        // POB should be prefixed with "since ", In Route and Idle show just the timestamp
                        if (isset($label) && $label === 'POB') {
                            $sinceStr =$formatted;
                        } else {
                            $sinceStr = $formatted;
                        }
                    } catch (\Exception $e) {
                        $sinceStr = '-';
                    }
                }

                $drv->status_label = $label;
                $drv->status_color = $color;
                $drv->status_since = $sinceStr;
            }
        }

        // Ensure drivers in the 'inactive' tab due to expired documents show status 'inactive'
        if ($tab === 'inactive') {
            $today = \Carbon\Carbon::today();
            foreach ($drivers as $drv) {
                // if already explicitly inactive skip
                if (($drv->status ?? '') === 'inactive') continue;
                $expiryFields = ['driving_license_expiry', 'private_hire_drivers_license_expiry', 'private_hire_vehicle_insurance_expiry', 'private_hire_vehicle_license_expiry', 'private_hire_vehicle_mot_expiry'];
                foreach ($expiryFields as $ef) {
                    if ($drv->{$ef} && \Carbon\Carbon::parse($drv->{$ef})->lt($today)) {
                        $drv->status = 'inactive';
                        break;
                    }
                }
            }
        }

        if ($request->ajax() || $request->get('partial')) {
            if ($tab === 'documents') {
                return view('admin.drivers._documents', compact('drivers'));
            }
            if ($tab === 'status') {
                return view('admin.drivers._status', compact('drivers', 'timingSettings'));
            }
            return view('admin.drivers._list', compact('drivers'));
        }

        return view('admin.drivers.index', compact('drivers', 'tab', 'timingSettings'));
    }

    public function show(Request $request, Driver $driver)
    {
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._show', compact('driver'));
        }

        return view('admin.drivers.show', compact('driver'));
    }

    /**
     * Show all jobs assigned to a specific driver.
     */
    public function jobs(Request $request, Driver $driver)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (! $startDate || ! $endDate) {
            $startDate = Carbon::today()->startOfMonth()->toDateString();
            $endDate = Carbon::today()->toDateString();
        }

        $jobsQuery = Booking::with('status')
            ->where('driver_id', $driver->id)
            ->orderByRaw("GREATEST(UNIX_TIMESTAMP(COALESCE(JSON_UNQUOTE(JSON_EXTRACT(COALESCE(meta,'{}'), '$.status_changed_at')), updated_at)), UNIX_TIMESTAMP(updated_at)) DESC");

        $this->applyDateRangeFilter($jobsQuery, $startDate, $endDate);

        $jobs = $jobsQuery
            ->orderBy('id', 'desc')
            ->paginate(20)
            ->withQueryString();

        $invoiceJobsQuery = Booking::with('status')
            ->where('driver_id', $driver->id)
            ->whereHas('status', function ($q) {
                $q->where('name', 'completed');
            });

        $this->applyDateRangeFilter($invoiceJobsQuery, $startDate, $endDate);

        $invoiceJobs = $invoiceJobsQuery
            ->orderBy('pickup_date')
            ->orderBy('pickup_time')
            ->get();

        $invoiceTotal = (float) $invoiceJobs->sum(function (Booking $booking) {
            $baseFare = $this->resolveDriverVisibleFare($booking);

            return $this->calculateDriverPriceFromBookingType(
                $baseFare,
                (string) ($booking->payment_type ?? ''),
                (float) ($booking->driver_price ?? 0)
            );
        });

        $invoiceAmountTotal = (float) $invoiceJobs->sum(function (Booking $booking) {
            return $this->resolveDriverVisibleFare($booking);
        });

        $invoiceDate = now()->toDateString();
        $invoiceNumber = sprintf('INV-DRV-%d-%s', $driver->id, now()->format('YmdHis'));

        return view('admin.drivers.jobs', compact(
            'driver',
            'jobs',
            'startDate',
            'endDate',
            'invoiceJobs',
            'invoiceTotal',
            'invoiceAmountTotal',
            'invoiceDate',
            'invoiceNumber'
        ));
    }

    /**
     * Show all invoices for a specific driver.
     */
    public function invoices(Request $request, Driver $driver)
    {
        $q = trim((string) $request->query('q', ''));

        $invoicesQuery = DriverInvoice::query()
            ->where('driver_id', $driver->id)
            ->orderByDesc('invoice_date')
            ->orderByDesc('id');

        if ($q !== '') {
            $invoicesQuery->where('invoice_number', 'like', '%' . $q . '%');
        }

        $invoices = $invoicesQuery->paginate(20)->withQueryString();

        return view('admin.drivers.invoices', compact('driver', 'invoices', 'q'));
    }

    /**
     * Generate and save an invoice draft, then show admin preview.
     */
    public function sendInvoice(Request $request, Driver $driver)
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $invoiceJobsQuery = Booking::with('status')
            ->where('driver_id', $driver->id)
            ->whereHas('status', function ($q) {
                $q->where('name', 'completed');
            })
            ->orderBy('pickup_date')
            ->orderBy('pickup_time');

        $this->applyDateRangeFilter($invoiceJobsQuery, $data['start_date'], $data['end_date']);

        $invoiceJobs = $invoiceJobsQuery->get();

        if ($invoiceJobs->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'No completed jobs found in the selected date range.');
        }

        $lineItems = $invoiceJobs->map(function (Booking $job) {
            $bookingType = (string) ($job->payment_type ?? '');
            $baseFare = $this->resolveDriverVisibleFare($job);
            $driverPrice = $this->calculateDriverPriceFromBookingType(
                $baseFare,
                $bookingType,
                (float) ($job->driver_price ?? 0)
            );

            return [
                'booking_id' => $job->id,
                'booking_code' => $job->booking_code ?? ('#' . $job->id),
                'pickup_date' => optional($job->pickup_date)->format('Y-m-d'),
                'pickup_time' => $job->pickup_time,
                'passenger_name' => $job->passenger_name,
                'pickup_address' => $job->pickup_address,
                'dropoff_address' => $job->dropoff_address,
                'total_price' => $baseFare,
                'driver_price' => $driverPrice,
                'partial_received_by_driver' => 0,
                'driver_fare' => $driverPrice,
                'booking_type' => $bookingType,
                'vehicle_type' => $job->vehicle_type,
                'status' => optional($job->status)->name,
                'phone' => $job->phone,
            ];
        })->values()->all();

        [$lineItems, $invoiceAmountTotal, $invoiceTotal] = $this->normalizeInvoiceLineItems($lineItems);

        $invoice = DriverInvoice::create([
            'driver_id' => $driver->id,
            'created_by_user_id' => auth()->id(),
            'invoice_number' => sprintf('INV-DRV-%d-%s', $driver->id, now()->format('YmdHis')),
            'invoice_date' => now()->toDateString(),
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'status' => 'draft',
            'jobs_count' => count($lineItems),
            'total_amount' => $invoiceAmountTotal,
            'total_driver_fare' => $invoiceTotal,
            'line_items' => $lineItems,
            'meta' => [
                'generated_at' => now()->toDateTimeString(),
            ],
        ]);

        $pdfContent = $this->renderInvoicePdf($driver, $invoice, collect($lineItems));
        $pdfPath = 'driver_invoices/' . $driver->id . '/' . $invoice->invoice_number . '.pdf';
        try {
            $this->writeInvoicePdf($pdfPath, $pdfContent);
            $invoice->update(['pdf_path' => $pdfPath]);
        } catch (\Throwable $e) {
            logger()->error('Failed to persist driver invoice PDF', [
                'driver_id' => $driver->id,
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.drivers.invoices.show', [
                'driver' => $driver->id,
                'invoice' => $invoice->id,
            ])
            ->with('success', 'Invoice draft generated and saved. Please review and click Send to Driver when ready.');
    }

    /**
     * Show saved invoice preview to admin.
     */
    public function showInvoice(Driver $driver, DriverInvoice $invoice)
    {
        if ((int) $invoice->driver_id !== (int) $driver->id) {
            abort(404);
        }

        [$normalizedLineItems, $invoiceAmountTotal, $invoiceTotal] = $this->normalizeInvoiceLineItems($invoice->line_items ?? []);
        $lineItems = collect($normalizedLineItems);

        return view('admin.drivers.invoice_show', compact('driver', 'invoice', 'lineItems', 'invoiceAmountTotal', 'invoiceTotal'));
    }

    /**
     * Save edits on a draft invoice (partial received values, totals, regenerated PDF).
     */
    public function updateInvoiceDraft(Request $request, Driver $driver, DriverInvoice $invoice)
    {
        if ((int) $invoice->driver_id !== (int) $driver->id) {
            abort(404);
        }

        $editedItems = $this->extractInvoiceLineItemsFromRequest($request, true);

        try {
            $this->applyInvoiceLineItemEdits($driver, $invoice, $editedItems);
        } catch (\Throwable $e) {
            logger()->error('Failed to update driver invoice draft', [
                'driver_id' => $driver->id,
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Unable to save draft changes. ' . (config('app.debug') ? $e->getMessage() : 'Please try again.'));
        }

        return redirect()
            ->route('admin.drivers.invoices.show', ['driver' => $driver->id, 'invoice' => $invoice->id])
            ->with('success', 'Invoice draft updated successfully.');
    }

    /**
     * Send a previously saved invoice to the driver email.
     */
    public function sendSavedInvoice(Request $request, Driver $driver, DriverInvoice $invoice)
    {
        if ((int) $invoice->driver_id !== (int) $driver->id) {
            abort(404);
        }

        $editedItems = $this->extractInvoiceLineItemsFromRequest($request, false);
        if (!empty($editedItems)) {
            try {
                $invoice = $this->applyInvoiceLineItemEdits($driver, $invoice, $editedItems);
            } catch (\Throwable $e) {
                logger()->error('Failed to apply invoice edits before email send', [
                    'driver_id' => $driver->id,
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()
                    ->back()
                    ->with('error', 'Unable to apply draft edits before sending email.');
            }
        }

        if (empty($driver->email)) {
            return redirect()
                ->back()
                ->with('error', 'Driver email is missing. Please add an email before sending this invoice.');
        }

        [$normalizedLineItems, $invoiceAmountTotal, $invoiceTotal] = $this->normalizeInvoiceLineItems($invoice->line_items ?? []);
        $lineItems = collect($normalizedLineItems);

        if ($lineItems->isEmpty()) {
            return redirect()
                ->back()
                ->with('error', 'Invoice has no line items and cannot be sent.');
        }

        if (! $this->invoicePdfExists($invoice->pdf_path)) {
            $pdfContent = $this->renderInvoicePdf($driver, $invoice, $lineItems);
            $pdfPath = 'driver_invoices/' . $driver->id . '/' . $invoice->invoice_number . '.pdf';
            $this->writeInvoicePdf($pdfPath, $pdfContent);
            $invoice->update(['pdf_path' => $pdfPath]);
        }

        $pdfContent = $this->readInvoicePdf($invoice->pdf_path);

        try {
            Mail::send('emails.driver_invoice', [
                'driver' => $driver,
                'startDate' => optional($invoice->start_date)->toDateString(),
                'endDate' => optional($invoice->end_date)->toDateString(),
                'invoiceNumber' => $invoice->invoice_number,
                'invoiceDate' => optional($invoice->invoice_date)->toDateString(),
                'invoiceJobs' => $lineItems,
                'invoiceTotal' => (float) $invoiceTotal,
                'invoiceAmountTotal' => (float) $invoiceAmountTotal,
            ], function ($message) use ($driver, $invoice, $pdfContent) {
                $message->to($driver->email)
                    ->subject('Driver Invoice ' . $invoice->invoice_number)
                    ->attachData($pdfContent, $invoice->invoice_number . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
            });
        } catch (\Throwable $e) {
            logger()->error('Failed to send saved driver invoice email', [
                'driver_id' => $driver->id,
                'invoice_id' => $invoice->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('error', 'Invoice email failed. ' . (config('app.debug') ? $e->getMessage() : 'Please try again.'));
        }

        $meta = is_array($invoice->meta) ? $invoice->meta : [];
        $meta['sent_to_email_at'] = now()->toDateTimeString();

        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
            'sent_to_email' => $driver->email,
            'meta' => $meta,
        ]);

        return redirect()
            ->route('admin.drivers.invoices.show', ['driver' => $driver->id, 'invoice' => $invoice->id])
            ->with('success', 'Invoice sent to driver successfully.');
    }

    /**
     * Send a previously saved invoice summary to the driver app (push notification).
     */
    public function sendSavedInvoiceToApp(Request $request, Driver $driver, DriverInvoice $invoice)
    {
        if ((int) $invoice->driver_id !== (int) $driver->id) {
            abort(404);
        }

        $editedItems = $this->extractInvoiceLineItemsFromRequest($request, false);
        if (!empty($editedItems)) {
            try {
                $invoice = $this->applyInvoiceLineItemEdits($driver, $invoice, $editedItems);
            } catch (\Throwable $e) {
                logger()->error('Failed to apply invoice edits before app send', [
                    'driver_id' => $driver->id,
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ]);

                return redirect()
                    ->back()
                    ->with('error', 'Unable to apply draft edits before sending to app.');
            }
        }

        [$normalizedLineItems, $invoiceAmountTotal, $invoiceTotal] = $this->normalizeInvoiceLineItems($invoice->line_items ?? []);
        if (empty($normalizedLineItems)) {
            return redirect()
                ->back()
                ->with('error', 'Invoice has no line items and cannot be sent.');
        }

        if (! $this->invoicePdfExists($invoice->pdf_path)) {
            $pdfContent = $this->renderInvoicePdf($driver, $invoice, collect($normalizedLineItems));
            $pdfPath = $invoice->pdf_path ?: ('driver_invoices/' . $driver->id . '/' . $invoice->invoice_number . '.pdf');
            $this->writeInvoicePdf($pdfPath, $pdfContent);
            $invoice->update(['pdf_path' => $pdfPath]);
        }

        \App\Models\DriverNotification::create([
            'driver_id' => $driver->id,
            'title' => 'New Invoice Available',
            'message' => sprintf(
                'Invoice %s (%s to %s) is ready. Total amount: £%.2f. Driver fare: £%.2f. Open Invoices in the app to view and download.',
                $invoice->invoice_number,
                optional($invoice->start_date)->toDateString(),
                optional($invoice->end_date)->toDateString(),
                (float) $invoiceAmountTotal,
                (float) $invoiceTotal
            ),
        ]);

        $meta = is_array($invoice->meta) ? $invoice->meta : [];
        $meta['sent_to_app_at'] = now()->toDateTimeString();
        $meta['visible_in_driver_app'] = true;
        $meta['app_send_count'] = (int) ($meta['app_send_count'] ?? 0) + 1;

        $invoice->update([
            'status' => 'sent',
            'sent_at' => $invoice->sent_at ?: now(),
            'meta' => $meta,
        ]);

        return redirect()
            ->route('admin.drivers.invoices.show', ['driver' => $driver->id, 'invoice' => $invoice->id])
            ->with('success', 'Invoice sent to driver app successfully.');
    }

    private function extractInvoiceLineItemsFromRequest(Request $request, bool $required): array
    {
        $validated = $request->validate([
            'line_items' => ($required ? 'required' : 'sometimes') . '|array|min:1',
            'line_items.*.booking_id' => 'required_with:line_items|integer',
            'line_items.*.partial_received_by_driver' => 'nullable|numeric|min:0',
        ]);

        return $validated['line_items'] ?? [];
    }

    private function applyInvoiceLineItemEdits(Driver $driver, DriverInvoice $invoice, array $editedItems): DriverInvoice
    {
        $editedPartialsByBookingId = [];
        foreach ($editedItems as $row) {
            $bookingId = (int) ($row['booking_id'] ?? 0);
            if ($bookingId <= 0) {
                continue;
            }
            $editedPartialsByBookingId[$bookingId] = max(0, (float) ($row['partial_received_by_driver'] ?? 0));
        }

        $updatedLineItems = collect($invoice->line_items ?? [])->map(function ($item) use ($editedPartialsByBookingId) {
            $line = is_array($item) ? $item : (array) $item;
            $bookingId = (int) ($line['booking_id'] ?? 0);
            if ($bookingId > 0 && array_key_exists($bookingId, $editedPartialsByBookingId)) {
                $line['partial_received_by_driver'] = $editedPartialsByBookingId[$bookingId];
            }

            return $line;
        })->all();

        [$normalizedLineItems, $invoiceAmountTotal, $invoiceTotal] = $this->normalizeInvoiceLineItems($updatedLineItems);

        $invoice->update([
            'line_items' => $normalizedLineItems,
            'jobs_count' => count($normalizedLineItems),
            'total_amount' => $invoiceAmountTotal,
            'total_driver_fare' => $invoiceTotal,
        ]);

        $pdfContent = $this->renderInvoicePdf($driver, $invoice, collect($normalizedLineItems));
        $pdfPath = $invoice->pdf_path ?: ('driver_invoices/' . $driver->id . '/' . $invoice->invoice_number . '.pdf');
        $this->writeInvoicePdf($pdfPath, $pdfContent);
        $invoice->update(['pdf_path' => $pdfPath]);

        return $invoice->fresh();
    }

    private function normalizeInvoiceLineItems($lineItems): array
    {
        $rawItems = collect($lineItems)->map(function ($row) {
            return is_array($row) ? $row : (array) $row;
        });

        $bookingIds = $rawItems
            ->pluck('booking_id')
            ->filter(function ($id) {
                return is_numeric($id) && (int) $id > 0;
            })
            ->map(function ($id) {
                return (int) $id;
            })
            ->unique()
            ->values()
            ->all();

        $bookingsById = collect();
        if (!empty($bookingIds)) {
            $bookingsById = Booking::query()
                ->whereIn('id', $bookingIds)
                ->get(['id', 'total_price', 'meta'])
                ->keyBy('id');
        }

        $normalized = $rawItems->map(function ($item) use ($bookingsById) {

            $bookingType = strtolower(trim((string) ($item['booking_type'] ?? ($item['payment_type'] ?? ''))));
            $bookingId = (int) ($item['booking_id'] ?? 0);
            $baseFare = round((float) ($item['total_price'] ?? 0), 2);
            if ($bookingId > 0) {
                $sourceBooking = $bookingsById->get($bookingId);
                if ($sourceBooking instanceof Booking) {
                    $baseFare = $this->resolveDriverVisibleFare($sourceBooking);
                }
            }
            $fallbackDriverPrice = round((float) ($item['driver_price'] ?? ($item['driver_fare'] ?? 0)), 2);
            $driverPrice = round($this->calculateDriverPriceFromBookingType($baseFare, $bookingType, $fallbackDriverPrice), 2);
            $partialReceived = round(max(0, (float) ($item['partial_received_by_driver'] ?? 0)), 2);
            $driverFare = round($driverPrice - $partialReceived, 2);

            return [
                'booking_id' => $item['booking_id'] ?? null,
                'booking_code' => $item['booking_code'] ?? (!empty($item['booking_id']) ? ('#' . $item['booking_id']) : '-'),
                'pickup_date' => $item['pickup_date'] ?? null,
                'pickup_time' => $item['pickup_time'] ?? null,
                'passenger_name' => $item['passenger_name'] ?? null,
                'pickup_address' => $item['pickup_address'] ?? null,
                'dropoff_address' => $item['dropoff_address'] ?? null,
                'total_price' => $baseFare,
                'driver_price' => $driverPrice,
                'partial_received_by_driver' => $partialReceived,
                'driver_fare' => $driverFare,
                'booking_type' => $bookingType !== '' ? $bookingType : null,
                'vehicle_type' => $item['vehicle_type'] ?? null,
                'status' => $item['status'] ?? null,
                'phone' => $item['phone'] ?? null,
            ];
        })->values();

        $invoiceAmountTotal = round((float) $normalized->sum(function ($item) {
            return (float) ($item['total_price'] ?? 0);
        }), 2);

        $invoiceTotal = round((float) $normalized->sum(function ($item) {
            return (float) ($item['driver_fare'] ?? 0);
        }), 2);

        return [$normalized->all(), $invoiceAmountTotal, $invoiceTotal];
    }

    private function resolveDriverVisibleFare(Booking $booking): float
    {
        $meta = is_array($booking->meta) ? $booking->meta : [];
        $driverVisible = data_get($meta, 'driver_display_price');

        if (is_numeric($driverVisible)) {
            return round(max(0, (float) $driverVisible), 2);
        }

        return round((float) ($booking->total_price ?? 0), 2);
    }

    private function calculateDriverPriceFromBookingType(float $totalPrice, ?string $bookingType, float $fallbackDriverPrice = 0.0): float
    {
        $type = strtolower(trim((string) $bookingType));

        if ($type === 'card') {
            return round($totalPrice * 0.8, 2);
        }

        if ($type === 'cash') {
            return round($totalPrice * -0.2, 2);
        }

        return round($fallbackDriverPrice, 2);
    }

    /**
     * Render invoice PDF content from saved invoice payload.
     */
    private function renderInvoicePdf(Driver $driver, DriverInvoice $invoice, $lineItems): string
    {
        $invoiceRows = collect($lineItems)->map(function ($row) {
            return is_array($row) ? (object) $row : $row;
        });

        $pdfHtml = view('admin.drivers.invoice_pdf', [
            'driver' => $driver,
            'startDate' => optional($invoice->start_date)->toDateString(),
            'endDate' => optional($invoice->end_date)->toDateString(),
            'invoiceJobs' => $invoiceRows,
            'invoiceTotal' => (float) $invoice->total_driver_fare,
            'invoiceAmountTotal' => (float) $invoice->total_amount,
            'invoiceDate' => optional($invoice->invoice_date)->toDateString(),
            'invoiceNumber' => $invoice->invoice_number,
        ])->render();

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($pdfHtml);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    /**
     * Store invoice PDF directly in storage/app without Flysystem finfo dependency.
     */
    private function writeInvoicePdf(string $relativePath, string $content): void
    {
        $fullPath = storage_path('app/' . ltrim(str_replace('\\', '/', $relativePath), '/'));
        $directory = dirname($fullPath);

        if (!is_dir($directory) && !mkdir($directory, 0755, true) && !is_dir($directory)) {
            throw new \RuntimeException('Unable to create invoice directory: ' . $directory);
        }

        if (file_put_contents($fullPath, $content) === false) {
            throw new \RuntimeException('Unable to write invoice PDF file: ' . $fullPath);
        }
    }

    private function invoicePdfExists(?string $relativePath): bool
    {
        if (!$relativePath) {
            return false;
        }

        $fullPath = storage_path('app/' . ltrim(str_replace('\\', '/', $relativePath), '/'));
        return is_file($fullPath);
    }

    private function readInvoicePdf(string $relativePath): string
    {
        $fullPath = storage_path('app/' . ltrim(str_replace('\\', '/', $relativePath), '/'));
        $content = @file_get_contents($fullPath);

        if ($content === false) {
            throw new \RuntimeException('Unable to read invoice PDF file: ' . $fullPath);
        }

        return $content;
    }

    /**
     * Apply selected date range to bookings query.
     */
    private function applyDateRangeFilter($query, string $startDate, string $endDate): void
    {
        $query->whereRaw('DATE(COALESCE(pickup_date, scheduled_at, created_at)) BETWEEN ? AND ?', [$startDate, $endDate]);
    }

    /**
     * Check availability and documents for a driver (AJAX helper used by booking edit UI)
     */
    public function checkAvailability(Request $request, Driver $driver)
    {
        $result = ['success' => true, 'driver' => ['id' => $driver->id, 'name' => $driver->name], 'now' => now()->toIso8601String()];

        // Reactivate if window expired
        try {
            if (method_exists($driver, 'reactivateIfExpired')) {
                if ($driver->reactivateIfExpired()) {
                    $result['reactivated'] = true;
                    $driver = $driver->fresh();
                }
            }
        } catch (\Exception $e) {
            logger()->warning('checkAvailability reactivate failed: ' . $e->getMessage(), ['driver_id' => $driver->id]);
        }

        // Include unavailable window if present
        if ($driver->unavailable_from) $result['unavailable_from'] = $driver->unavailable_from;
        if ($driver->unavailable_to) $result['unavailable_to'] = $driver->unavailable_to;

        // Check for expired or soon-to-expire documents (15 day window)
        try {
            $docsList = [];
            $docs = [
                'driving_license_expiry' => 'Driving License',
                'private_hire_drivers_license_expiry' => 'Private Hire Drivers License',
                'private_hire_vehicle_insurance_expiry' => 'Private Hire Vehicle Insurance',
                'private_hire_vehicle_license_expiry' => 'Private Hire Vehicle License',
                'private_hire_vehicle_mot_expiry' => 'Private Hire Vehicle MOT',
            ];
            $today = \Carbon\Carbon::today();
            $threshold = $today->copy()->addDays(15);
            $hasExpired = false;

            foreach ($docs as $field => $label) {
                if ($driver->{$field}) {
                    $expiry = \Carbon\Carbon::parse($driver->{$field});
                    if ($expiry->lt($today)) {
                        $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expired'];
                        $hasExpired = true;
                    } elseif ($expiry->lte($threshold)) {
                        $docsList[] = ['label' => $label, 'expiry' => $expiry->toDateString(), 'status' => 'expiring'];
                    }
                }
            }
            if (!empty($docsList)) {
                $result['documents'] = $docsList;
                $result['has_expired'] = $hasExpired;
                // Ensure we flag the driver status to the client as well
                $result['status'] = $driver->status;
            }
        } catch (\Exception $e) {
            logger()->warning('checkAvailability document check failed: ' . $e->getMessage(), ['driver_id' => $driver->id]);
        }

        return response()->json($result, 200);
    }

    /**
     * Show live tracking page for driver
     */
    public function track(Driver $driver, \App\Models\Booking $booking)
    {
        // Ensure booking belongs to this driver
        if ((int) $booking->driver_id !== (int) $driver->id) {
            abort(403, 'This booking is not assigned to the selected driver.');
        }

        // Check if booking is in trackable status (POB, arrived_at_pickup, or in_route)
        $meta = $booking->meta ?? [];
        $isInRoute       = isset($meta['in_route']) && $meta['in_route'] === true;
        $isArrivedPickup = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;
        $isPob = $booking->status && $booking->status->name === 'pob';
        
        if (!$isPob && !$isInRoute && !$isArrivedPickup) {
            abort(400, 'Driver tracking is available for jobs in In Route, Arrived at Pickup, or POB status.');
        }

        return view('admin.drivers.track', compact('driver', 'booking'));
    }


    /**
     * Get driver's current location (API endpoint)
     * If bookingId is 0 or booking not found, just return driver location (for non-POB tracking)
     */
    public function getLocation(Driver $driver, $bookingId = 0)
    {
        try {
            // Try to find the booking if a valid booking ID is provided
            $booking = null;
            if ($bookingId && $bookingId > 0) {
                $booking = \App\Models\Booking::find($bookingId);
                
                // Ensure booking belongs to this driver
                if ($booking && (int) $booking->driver_id !== (int) $driver->id) {
                    return response()->json(['error' => 'Unauthorized'], 403);
                }
            }

            \Log::info('Getting driver location', [
                'driver_id' => $driver->id,
                'booking_id' => $booking ? $booking->id : 'none',
                'booking_status' => $booking ? (optional($booking->status)->name) : 'no booking'
            ]);

            // Get driver's last known location from driver_locations table
            $driverLocation = $driver->currentLocation;
            
            $location = null;
            $lastUpdate = null;
            
            if ($driverLocation) {
                $location = [
                    'lat' => (float) $driverLocation->latitude,
                    'lng' => (float) $driverLocation->longitude,
                    'accuracy' => $driverLocation->accuracy,
                ];
                $lastUpdate = $driverLocation->updated_at->toDateTimeString();
                \Log::info('Found real driver location', [
                    'driver_id' => $driver->id,
                    'lat' => $location['lat'],
                    'lng' => $location['lng'],
                    'updated_at' => $lastUpdate
                ]);
            } else {
                \Log::warning('No location found for driver', ['driver_id' => $driver->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Driver location not available. Driver may not have shared location yet.'
                ], 404);
            }
            
            // Get booking destination - provide defaults if missing
            $destination = [
                'lat' => $booking ? ($booking->to_latitude ?? 51.5164) : null,
                'lng' => $booking ? ($booking->to_longitude ?? -0.1276) : null,
                'address' => $booking ? ($booking->to_address ?? 'London, UK') : null
            ];
            
            // Get pickup location - provide defaults if missing
            $pickup = [
                'lat' => $booking ? ($booking->from_latitude ?? 51.5014) : null,
                'lng' => $booking ? ($booking->from_longitude ?? -0.1419) : null,
                'address' => $booking ? ($booking->from_address ?? 'Pickup Location, London, UK') : null
            ];

            // Check if booking is in "in_route" or "arrived_at_pickup" status
            $meta = $booking ? ($booking->meta ?? []) : [];
            $isInRoute       = isset($meta['in_route']) && $meta['in_route'] === true;
            $isArrivedPickup = isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true;

            return response()->json([
                'success' => true,
                'in_route'          => $isInRoute,
                'arrived_at_pickup' => $isArrivedPickup,
                'driver' => [
                    'latitude' => $location['lat'],
                    'longitude' => $location['lng'],
                    'id' => $driver->id,
                    'name' => $driver->name,
                    'phone' => $driver->phone,
                    'vehicle_plate' => $driver->vehicle_plate,
                    'accuracy' => $location['accuracy'] ?? null,
                    'heading' => $location['heading'] ?? null,
                    'speed' => $location['speed'] ?? null
                ],
                'booking' => $booking ? [
                    'id' => $booking->id,
                    'booking_code' => $booking->booking_code,
                    'status' => optional($booking->status)->name ?? 'unknown'
                ] : null,
                'pickup' => [
                    'latitude' => $pickup['lat'],
                    'longitude' => $pickup['lng'],
                    'address' => $pickup['address']
                ],
                'destination' => [
                    'latitude' => $destination['lat'],
                    'longitude' => $destination['lng'],
                    'address' => $destination['address']
                ],
                'last_update' => $lastUpdate
            ]);

        } catch (\Exception $e) {
            \Log::error('Error getting driver location', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'driver_id' => $driver->id ?? 'unknown',
                'booking_id' => $bookingId ?? 'unknown'
            ]);
            return response()->json(['error' => 'Failed to get location: ' . $e->getMessage()], 500);
        }
    }

    public function create(Request $request)
    {
        $councils = DB::table('councils')->orderBy('council_name')->get();

        // Ensure $driver variable exists for form bindings (create vs edit)
        $driver = new Driver();
        
        // Full page create
        if ($request->ajax() || $request->get('partial')) {
            return view('admin.drivers._modal_form', compact('councils', 'driver'));
        }
        return view('admin.drivers.create', compact('councils', 'driver'));
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
            // let the Driver model's setPasswordAttribute handle hashing
            // assign plain password so it gets hashed by the model
            // (avoid double-hashing which breaks login)
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
            // let the Driver model's setPasswordAttribute handle hashing
            // assign plain password so it gets hashed by the model
            // (avoid double-hashing which breaks login)
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

    /**
     * Return pickup timing data for accepted drivers (used by the status-tab AJAX poller).
     * Accepts optional ?ids=1,2,3 to scope to specific driver IDs visible on screen.
     */
    public function getBookingTiming(Request $request)
    {
        $finishedStatusIds = \App\Models\BookingStatus::whereIn('name', ['completed', 'cancelled'])->pluck('id')->toArray();

        $query = Driver::where('status', 'active');

        // Scope to the driver IDs currently visible on the admin page
        if ($request->filled('ids')) {
            $ids = array_filter(array_map('intval', explode(',', $request->get('ids'))));
            if (!empty($ids)) {
                $query->whereIn('id', $ids);
            }
        }

        $drivers = $query->get(['id', 'name']);

        $data = [];
        foreach ($drivers as $driver) {
            $booking = \App\Models\Booking::where('driver_id', $driver->id)
                ->whereNotIn('status_id', $finishedStatusIds)
                ->orderBy('scheduled_at', 'asc')
                ->orderBy('id', 'desc')
                ->first();

            $item = [
                'driver_id'         => $driver->id,
                'driver_name'       => $driver->name,
                'booking_id'        => null,
                'booking_code'      => null,
                'scheduled_at'      => null,
                'remaining_minutes' => null,
                'is_in_route'       => false,
                'is_pob'            => false,
                'pickup_address'    => null,
            ];

            if ($booking) {
                // Resolve pickup datetime: pickup_date+pickup_time first, scheduled_at as fallback
                $pickupAt = null;
                if ($booking->pickup_date && $booking->pickup_time) {
                    $pickupAt = \Carbon\Carbon::parse($booking->pickup_date->format('Y-m-d') . ' ' . $booking->pickup_time);
                } elseif ($booking->scheduled_at) {
                    $pickupAt = $booking->scheduled_at;
                }

                if ($pickupAt) {
                    $meta             = $booking->meta ?? [];
                    $isInRoute        = (isset($meta['in_route']) && $meta['in_route'] === true)
                                     || (isset($meta['arrived_at_pickup']) && $meta['arrived_at_pickup'] === true);
                    $isPob            = optional($booking->status)->name === 'pob';
                    $remainingMinutes = (int) now()->diffInMinutes($pickupAt, false);

                    $item['booking_id']        = $booking->id;
                    $item['booking_code']      = $booking->booking_code;
                    $item['scheduled_at']      = $pickupAt->toIso8601String();
                    $item['remaining_minutes'] = $remainingMinutes;
                    $item['is_in_route']       = $isInRoute;
                    $item['is_pob']            = $isPob;
                    $item['pickup_address']    = $booking->pickup_address;
                }
            }

            $data[] = $item;
        }

        return response()->json(['success' => true, 'drivers' => $data]);
    }

    /**
     * Send a late-warning notification to the driver (and admin for urgent warnings).
     * Called by the status-tab AJAX poller when thresholds are crossed.
     */
    public function sendLateWarning(Request $request, Driver $driver)
    {
        $validated = $request->validate([
            'booking_id'        => 'required|integer',
            'reason'            => 'required|string|in:two_hour_warning,urgent_warning',
            'remaining_minutes' => 'required|integer',
            'eta_minutes'       => 'nullable|integer',
        ]);

        $booking = \App\Models\Booking::find($validated['booking_id']);
        if (!$booking || (int) $booking->driver_id !== (int) $driver->id) {
            return response()->json(['success' => false, 'message' => 'Invalid booking'], 404);
        }

        // Deduplication: skip if same warning type was sent too recently
        $meta       = $booking->meta ?? [];
        $warningKey = 'late_warning_' . $validated['reason'] . '_sent_at';
        $lastSent   = isset($meta[$warningKey]) ? \Carbon\Carbon::parse($meta[$warningKey]) : null;

        if ($validated['reason'] === 'two_hour_warning' && $lastSent && $lastSent->diffInMinutes(now()) < 25) {
            return response()->json(['success' => false, 'message' => 'Already sent recently', 'skipped' => true]);
        }
        if ($validated['reason'] === 'urgent_warning' && $lastSent && $lastSent->diffInSeconds(now()) < 55) {
            return response()->json(['success' => false, 'message' => 'Already sent recently', 'skipped' => true]);
        }

        $remainingText = $this->formatMinutesText($validated['remaining_minutes']);
        $etaText       = isset($validated['eta_minutes']) ? $this->formatMinutesText($validated['eta_minutes']) : null;

        if ($validated['reason'] === 'two_hour_warning') {
            $title = 'Please Select "In Route"';
            $body  = "Your pickup is in {$remainingText}."
                   . ($etaText ? " ETA to pickup location: {$etaText}." : '')
                   . ' Please mark yourself as In Route now.';
        } else {
            $title = 'Urgent: Select "In Route" NOW';
            $body  = "Only {$remainingText} until your pickup! You must select In Route immediately.";
        }

        // Create driver notification - model observer auto-sends Expo push
        \App\Models\DriverNotification::create([
            'driver_id' => $driver->id,
            'title'     => $title,
            'message'   => $body,
        ]);

        // For urgent warnings, also notify all admins
        if ($validated['reason'] === 'urgent_warning') {
            $adminTitle = "Driver Not In Route – {$driver->name}";
            $adminMsg   = "Driver {$driver->name} has NOT selected In Route."
                        . " Booking: {$booking->booking_code}."
                        . " Time remaining: {$remainingText}."
                        . ($etaText ? " ETA to pickup: {$etaText}." : '');
            \App\Models\UserNotification::createForAdmins($adminTitle, $adminMsg);
        }

        // Record warning timestamp in booking meta (saveQuietly avoids model events)
        $meta[$warningKey] = now()->toIso8601String();
        $booking->meta     = $meta;
        $booking->saveQuietly();

        \Log::info("Late warning sent [{$validated['reason']}]", [
            'driver_id'         => $driver->id,
            'booking_id'        => $booking->id,
            'remaining_minutes' => $validated['remaining_minutes'],
            'eta_minutes'       => $validated['eta_minutes'] ?? null,
        ]);

        return response()->json(['success' => true, 'message' => 'Warning sent']);
    }

    /** Format minutes as human-readable string, e.g. "3h 45m" */
    private function formatMinutesText(int $minutes): string
    {
        if ($minutes < 0) return 'overdue';
        $hours = intdiv($minutes, 60);
        $mins  = $minutes % 60;
        if ($hours > 0 && $mins > 0) return "{$hours}h {$mins}m";
        if ($hours > 0) return "{$hours}h";
        return "{$mins}m";
    }
}
