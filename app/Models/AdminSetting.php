<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $table = 'admin_settings';

    protected $fillable = [
        'app_name',
        'default_language',
        'timezone',
        'misc',
    ];

    protected $casts = [
        'misc' => 'array',
    ];

    private static ?array $settingsCache = null;

    public static function defaults(): array
    {
        return [
            'booking_reference_prefix' => 'CD',
            'driver_warning_two_hour_minutes' => 120,
            'driver_warning_urgent_minutes' => 25,
            'driver_warning_eta_buffer_minutes' => 30,
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
            'idle_timeout_minutes' => 10,
            'admin_theme_mode' => 'light',
            'vehicle_row_color_saloon' => '#d4edda',
            'vehicle_row_color_business' => '#fff3cd',
            'vehicle_row_color_mpv6' => '#d1ecf1',
            'vehicle_row_color_mpv8' => '#e2d9f3',
        ];
    }

    public static function allSettings(): array
    {
        if (self::$settingsCache !== null) {
            return self::$settingsCache;
        }

        $defaults = static::defaults();

        try {
            $row = static::query()->first();

            if (!$row) {
                $row = static::query()->create([
                    'app_name' => 'AirportServices',
                    'default_language' => 'en',
                    'timezone' => 'UTC',
                    'misc' => [],
                ]);
            }

            $misc = is_array($row->misc) ? $row->misc : [];
            self::$settingsCache = array_merge($defaults, $misc);
        } catch (\Throwable $e) {
            self::$settingsCache = $defaults;
        }

        return self::$settingsCache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::allSettings();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function putMany(array $values): void
    {
        $row = static::query()->first();

        if (!$row) {
            $row = static::query()->create([
                'app_name' => 'AirportServices',
                'default_language' => 'en',
                'timezone' => 'UTC',
                'misc' => [],
            ]);
        }

        $misc = is_array($row->misc) ? $row->misc : [];

        foreach ($values as $key => $value) {
            $misc[$key] = $value;
        }

        $row->misc = $misc;
        $row->save();

        self::$settingsCache = null;
    }

    public static function vehicleRowColors(): array
    {
        return [
            'saloon' => (string) static::get('vehicle_row_color_saloon', '#d4edda'),
            'business' => (string) static::get('vehicle_row_color_business', '#fff3cd'),
            'mpv6' => (string) static::get('vehicle_row_color_mpv6', '#d1ecf1'),
            'mpv8' => (string) static::get('vehicle_row_color_mpv8', '#e2d9f3'),
        ];
    }

    public static function driverWarningThresholds(): array
    {
        $twoHour = (int) static::get('driver_warning_two_hour_minutes', 120);
        $urgent = (int) static::get('driver_warning_urgent_minutes', 25);
        $etaBuffer = (int) static::get('driver_warning_eta_buffer_minutes', 30);

        if ($twoHour < 30) {
            $twoHour = 120;
        }
        if ($urgent < 1 || $urgent >= $twoHour) {
            $urgent = 25;
        }
        if ($etaBuffer < 0) {
            $etaBuffer = 30;
        }

        return [
            'two_hour_minutes' => $twoHour,
            'urgent_minutes' => $urgent,
            'eta_buffer_minutes' => $etaBuffer,
        ];
    }
}
