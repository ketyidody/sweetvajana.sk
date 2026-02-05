<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasTranslations;
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function translatableFields(): array
    {
        return ['title', 'content'];
    }
}
