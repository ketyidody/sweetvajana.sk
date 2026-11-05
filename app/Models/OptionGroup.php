<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OptionGroup extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public function values(): HasMany
    {
        return $this->hasMany(OptionGroupValue::class)->orderBy('id');
    }

    public static function translatableFields(): array
    {
        return ['name'];
    }
}
