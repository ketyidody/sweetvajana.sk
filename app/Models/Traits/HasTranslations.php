<?php

namespace App\Models\Traits;

use App\Models\ModelTranslation;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasTranslations
{
    public function translations(): MorphMany
    {
        return $this->morphMany(ModelTranslation::class, 'translatable');
    }

    public function translated(string $field, ?string $locale = null): ?string
    {
        $locale = $locale ?? app()->getLocale();
        $defaultLocale = config('app.fallback_locale', 'sk');

        if ($locale === $defaultLocale) {
            return $this->{$field};
        }

        $translation = $this->translations
            ->where('locale', $locale)
            ->where('field', $field)
            ->first();

        return $translation?->value ?: $this->{$field};
    }

    public function scopeWithTranslations($query, ?string $locale = null)
    {
        $locale = $locale ?? app()->getLocale();

        return $query->with(['translations' => function ($q) use ($locale) {
            $q->where('locale', $locale);
        }]);
    }

    public static function translatableFields(): array
    {
        return [];
    }
}
