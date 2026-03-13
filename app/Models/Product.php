<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasTranslations;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'images',
        'is_active',
        'is_featured',
        'is_orderable_online',
        'is_available_for_collection',
        'soonest_availability',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'images' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_orderable_online' => 'boolean',
        'is_available_for_collection' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public static function translatableFields(): array
    {
        return ['name', 'description'];
    }
}
