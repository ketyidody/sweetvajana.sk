<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    use HasTranslations;

    protected $fillable = ['name'];

    public static function translatableFields(): array
    {
        return ['name'];
    }
}
