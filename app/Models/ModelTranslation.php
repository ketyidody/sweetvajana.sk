<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ModelTranslation extends Model
{
    protected $table = 'model_translations';

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'locale',
        'field',
        'value',
    ];

    public function translatable(): MorphTo
    {
        return $this->morphTo();
    }
}
