<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = [
        'locale',
        'group',
        'key',
        'value',
    ];

    protected static function booted(): void
    {
        static::saved(fn (self $t) => Cache::forget("translations.{$t->locale}"));
        static::deleted(fn (self $t) => Cache::forget("translations.{$t->locale}"));
    }

    public static function clearCacheForLocale(string $locale): void
    {
        Cache::forget("translations.{$locale}");
    }

    public static function forLocale(string $locale): array
    {
        return Cache::rememberForever("translations.{$locale}", function () use ($locale) {
            $translations = [];

            static::where('locale', $locale)->get()->each(function ($t) use (&$translations) {
                $translations[$t->group][$t->key] = $t->value;
            });

            return $translations;
        });
    }
}
