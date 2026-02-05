<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Language extends Model
{
    protected $fillable = [
        'code',
        'name',
        'native_name',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public static function clearCache(): void
    {
        Cache::forget('languages.default');
        Cache::forget('languages.active');
        Cache::forget('languages.active_codes');
    }

    public static function getDefault(): ?self
    {
        return Cache::rememberForever('languages.default', function () {
            return static::where('is_default', true)->first();
        });
    }

    public static function getActive()
    {
        return Cache::rememberForever('languages.active', function () {
            return static::where('is_active', true)->orderBy('sort_order')->get();
        });
    }

    public static function getActiveCodes(): array
    {
        return Cache::rememberForever('languages.active_codes', function () {
            return static::where('is_active', true)->pluck('code')->toArray();
        });
    }
}
