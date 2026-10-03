<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SystemSetting extends Model
{
    use HasFactory;

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'label',
        'description',
    ];

    /**
     * Get a setting value by key with optional default fallback.
     */
    public static function get(string $key, $default = null)
    {
        $setting = Cache::remember("system_setting_{$key}", 3600, function () use ($key) {
            return static::where('key', $key)->first();
        });

        if (!$setting) {
            return $default;
        }

        return match ($setting->type) {
            'integer', 'int' => (int) $setting->value,
            'boolean', 'bool' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($setting->value, true),
            'float' => (float) $setting->value,
            default => $setting->value,
        };
    }

    /**
     * Set / update a setting value by key.
     */
    public static function set(string $key, $value, ?string $type = null, ?string $label = null, ?string $group = 'security', ?string $description = null): self
    {
        $valString = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;

        $attributes = ['value' => $valString];
        if ($type !== null) $attributes['type'] = $type;
        if ($label !== null) $attributes['label'] = $label;
        if ($group !== null) $attributes['group'] = $group;
        if ($description !== null) $attributes['description'] = $description;

        $setting = static::updateOrCreate(
            ['key' => $key],
            $attributes
        );

        Cache::forget("system_setting_{$key}");
        Cache::forget('system_security_settings_all');

        return $setting;
    }

    /**
     * Get all security related settings.
     */
    public static function getSecuritySettings(): array
    {
        return [
            'max_login_attempts' => (int) static::get('max_login_attempts', 3),
            'lockout_duration_seconds' => (int) static::get('lockout_duration_seconds', 30),
        ];
    }
}
