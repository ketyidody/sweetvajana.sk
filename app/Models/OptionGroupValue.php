<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OptionGroupValue extends Model
{
    use HasTranslations;

    protected $fillable = ['option_group_id', 'name'];

    public function group(): BelongsTo
    {
        return $this->belongsTo(OptionGroup::class, 'option_group_id');
    }

    public static function translatableFields(): array
    {
        return ['name'];
    }
}
