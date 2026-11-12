<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use HasFactory;

    protected $fillable = ['setting_key', 'setting_value'];

    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('site-settings');
        });

        static::deleted(function () {
            Cache::forget('site-settings');
        });
    }

    public static function values(): array
    {
        return Cache::remember('site-settings', 600, function () {
            return self::query()->pluck('setting_value', 'setting_key')->all();
        });
    }

    public static function value(string $key, $default = null)
    {
        return self::values()[$key] ?? $default;
    }
}
