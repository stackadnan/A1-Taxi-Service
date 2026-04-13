@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
@php
  $settings = $settings ?? [];
@endphp

<div class="bg-white p-6 rounded shadow">
  <div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-semibold">Admin Settings</h1>
    <span class="text-xs px-2 py-1 rounded bg-indigo-50 text-indigo-700">Live Configuration</span>
  </div>

  <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-8">
    @csrf
    @method('PUT')

    <section class="border rounded-lg p-4">
      <h2 class="text-lg font-semibold mb-4">Booking & Driver Operations</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="booking_reference_prefix" class="block text-sm font-medium text-gray-700 mb-1">Booking Prefix (2 letters)</label>
          <input
            id="booking_reference_prefix"
            name="booking_reference_prefix"
            type="text"
            maxlength="2"
            value="{{ old('booking_reference_prefix', $settings['booking_reference_prefix'] ?? 'CD') }}"
            class="w-full border rounded px-3 py-2 uppercase"
            required
          >
          @error('booking_reference_prefix') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="idle_timeout_minutes" class="block text-sm font-medium text-gray-700 mb-1">Admin Idle Logout (minutes)</label>
          <input
            id="idle_timeout_minutes"
            name="idle_timeout_minutes"
            type="number"
            min="10"
            max="15"
            value="{{ old('idle_timeout_minutes', $settings['idle_timeout_minutes'] ?? 10) }}"
            class="w-full border rounded px-3 py-2"
            required
          >
          <p class="text-xs text-gray-500 mt-1">Allowed range: 10 to 15 minutes.</p>
          @error('idle_timeout_minutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="driver_warning_two_hour_minutes" class="block text-sm font-medium text-gray-700 mb-1">In Route Reminder Window (minutes before pickup)</label>
          <input
            id="driver_warning_two_hour_minutes"
            name="driver_warning_two_hour_minutes"
            type="number"
            min="30"
            max="360"
            value="{{ old('driver_warning_two_hour_minutes', $settings['driver_warning_two_hour_minutes'] ?? 120) }}"
            class="w-full border rounded px-3 py-2"
            required
          >
          @error('driver_warning_two_hour_minutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="driver_warning_urgent_minutes" class="block text-sm font-medium text-gray-700 mb-1">Urgent Reminder Threshold (minutes)</label>
          <input
            id="driver_warning_urgent_minutes"
            name="driver_warning_urgent_minutes"
            type="number"
            min="1"
            max="120"
            value="{{ old('driver_warning_urgent_minutes', $settings['driver_warning_urgent_minutes'] ?? 25) }}"
            class="w-full border rounded px-3 py-2"
            required
          >
          @error('driver_warning_urgent_minutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="driver_warning_eta_buffer_minutes" class="block text-sm font-medium text-gray-700 mb-1">ETA Buffer Required (minutes)</label>
          <input
            id="driver_warning_eta_buffer_minutes"
            name="driver_warning_eta_buffer_minutes"
            type="number"
            min="0"
            max="120"
            value="{{ old('driver_warning_eta_buffer_minutes', $settings['driver_warning_eta_buffer_minutes'] ?? 30) }}"
            class="w-full border rounded px-3 py-2"
            required
          >
          @error('driver_warning_eta_buffer_minutes') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </section>

    <section class="border rounded-lg p-4">
      <h2 class="text-lg font-semibold mb-4">Stripe</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="stripe_public_key" class="block text-sm font-medium text-gray-700 mb-1">Stripe Publishable Key</label>
          <input
            id="stripe_public_key"
            name="stripe_public_key"
            type="text"
            value="{{ old('stripe_public_key', $settings['stripe_public_key'] ?? '') }}"
            class="w-full border rounded px-3 py-2"
            placeholder="pk_live_..."
          >
          @error('stripe_public_key') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label for="stripe_secret_key" class="block text-sm font-medium text-gray-700 mb-1">Stripe Secret Key</label>
          <input
            id="stripe_secret_key"
            name="stripe_secret_key"
            type="password"
            value="{{ old('stripe_secret_key', $settings['stripe_secret_key'] ?? '') }}"
            class="w-full border rounded px-3 py-2"
            placeholder="sk_live_..."
          >
          @error('stripe_secret_key') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>
    </section>

    <section class="border rounded-lg p-4">
      <h2 class="text-lg font-semibold mb-4">Theme & Vehicle Row Colors</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label for="admin_theme_mode" class="block text-sm font-medium text-gray-700 mb-1">Theme Mode</label>
          <select id="admin_theme_mode" name="admin_theme_mode" class="w-full border rounded px-3 py-2">
            <option value="light" @selected(old('admin_theme_mode', $settings['admin_theme_mode'] ?? 'light') === 'light')>Light</option>
            <option value="dark" @selected(old('admin_theme_mode', $settings['admin_theme_mode'] ?? 'light') === 'dark')>Dark</option>
          </select>
          @error('admin_theme_mode') <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        @php
          $colorFields = [
            'vehicle_row_color_saloon' => 'Saloon Row Color',
            'vehicle_row_color_business' => 'Business Row Color',
            'vehicle_row_color_mpv6' => 'MPV6 Row Color',
            'vehicle_row_color_mpv8' => 'MPV8 Row Color',
          ];
        @endphp

        @foreach($colorFields as $field => $label)
          @php $colorValue = old($field, $settings[$field] ?? '#ffffff'); @endphp
          <div>
            <label for="{{ $field }}" class="block text-sm font-medium text-gray-700 mb-1">{{ $label }}</label>
            <div class="flex items-center gap-2">
              <input
                id="{{ $field }}"
                name="{{ $field }}"
                type="text"
                value="{{ $colorValue }}"
                class="w-full border rounded px-3 py-2 uppercase"
                pattern="^#[A-Fa-f0-9]{6}$"
                required
              >
              <input
                type="color"
                value="{{ $colorValue }}"
                class="h-10 w-14 border rounded p-1"
                data-sync-color="{{ $field }}"
              >
            </div>
            @error($field) <p class="text-sm text-red-600 mt-1">{{ $message }}</p> @enderror
          </div>
        @endforeach
      </div>
    </section>

    <div class="pt-2">
      <button type="submit" class="px-5 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Save Settings</button>
    </div>
  </form>
</div>

<script>
  (function () {
    document.querySelectorAll('[data-sync-color]').forEach(function (picker) {
      picker.addEventListener('input', function () {
        var targetId = picker.getAttribute('data-sync-color');
        var input = document.getElementById(targetId);
        if (input) {
          input.value = picker.value.toUpperCase();
        }
      });
    });
  })();
</script>
@endsection
