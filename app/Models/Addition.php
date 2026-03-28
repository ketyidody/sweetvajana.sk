<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Addition extends Model
{
    use HasTranslations;

    protected $fillable = ['name', 'price'];

    protected $casts = [
        'price' => 'decimal:2',
    ];

    public static function translatableFields(): array
    {
        return ['name'];
    }
}
