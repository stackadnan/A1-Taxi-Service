<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminSettingsController extends Controller
{
    public function index(): View
    {
        return view('admin.settings.index', [
            'settings' => AdminSetting::allSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'booking_reference_prefix' => ['required', 'string', 'size:2', 'regex:/^[A-Za-z]{2}$/'],
            'driver_warning_two_hour_minutes' => ['required', 'integer', 'between:30,360'],
            'driver_warning_urgent_minutes' => ['required', 'integer', 'between:1,120', 'lt:driver_warning_two_hour_minutes'],
            'driver_warning_eta_buffer_minutes' => ['required', 'integer', 'between:0,120'],
            'stripe_public_key' => ['nullable', 'string', 'max:255'],
            'stripe_secret_key' => ['nullable', 'string', 'max:255'],
            'vat_percentage' => ['required', 'numeric', 'between:0,100'],
            'idle_timeout_minutes' => ['required', 'integer', 'between:10,15'],
            'admin_theme_mode' => ['required', 'in:light,dark'],
            'vehicle_row_color_saloon' => ['required', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'vehicle_row_color_business' => ['required', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'vehicle_row_color_mpv6' => ['required', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
            'vehicle_row_color_mpv8' => ['required', 'string', 'regex:/^#[A-Fa-f0-9]{6}$/'],
        ]);

        $validated['booking_reference_prefix'] = strtoupper($validated['booking_reference_prefix']);
        $validated['stripe_public_key'] = trim((string) ($validated['stripe_public_key'] ?? ''));
        $validated['stripe_secret_key'] = trim((string) ($validated['stripe_secret_key'] ?? ''));
        $validated['vat_percentage'] = round((float) ($validated['vat_percentage'] ?? 0), 2);

        AdminSetting::putMany($validated);

        return redirect()
            ->route('admin.settings.index')
            ->with('success', 'Admin settings updated successfully.');
    }
}
