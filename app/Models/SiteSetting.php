<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public const TRANSLATABLE_KEYS = [
        'site_name',
        'hero_title',
        'hero_subtitle',
        'hero_cta_text',
        'about_text',
        'contact_text',
        'meta_description',
        'footer_text',
    ];

    public function translations(): HasMany
    {
        return $this->hasMany(SiteSettingTranslation::class);
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return static::where('key', $key)->value('value') ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
    }

    public static function allAsArray(?string $locale = null): array
    {
        $settings = static::pluck('value', 'key')->toArray();

        if ($locale === null) {
            return $settings;
        }

        $defaultLocale = Language::getDefault()?->code ?? config('app.fallback_locale', 'sk');

        if ($locale === $defaultLocale) {
            return $settings;
        }

        $translations = SiteSettingTranslation::whereHas('siteSetting', function ($q) {
            $q->whereIn('key', static::TRANSLATABLE_KEYS);
        })
            ->where('locale', $locale)
            ->whereNotNull('value')
            ->where('value', '!=', '')
            ->with('siteSetting:id,key')
            ->get();

        foreach ($translations as $translation) {
            $settings[$translation->siteSetting->key] = $translation->value;
        }

        return $settings;
    }

    public static function translationsForAdmin(): array
    {
        $result = [];

        $settings = static::whereIn('key', static::TRANSLATABLE_KEYS)
            ->with('translations')
            ->get();

        foreach ($settings as $setting) {
            foreach ($setting->translations as $translation) {
                $result[$translation->locale][$setting->key] = $translation->value;
            }
        }

        return $result;
    }
}
