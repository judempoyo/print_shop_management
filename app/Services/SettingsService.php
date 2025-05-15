<?php
namespace App\Services;

use App\Models\Setting;

class SettingsService
{
    protected static $cache = [];

    public static function get(string $key, $default = null)
    {
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        $setting = Setting::where('settings_key', $key)->first();
        $value = $setting ? $setting->value : $default;

        self::$cache[$key] = $value;
        return $value;
    }

    public static function set(string $key, $value): void
    {
        Setting::updateOrCreate(
            ['settings_key' => $key],
            ['value' => $value]
        );

        self::$cache[$key] = $value;
    }
}