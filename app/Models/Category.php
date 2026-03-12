<?php

namespace App\Models;

use App\Models\Traits\HasTranslations;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasTranslations;

    const MAX_DEPTH = 3;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'image',
        'is_active',
        'parent_id',
        'position',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public static function translatableFields(): array
    {
        return ['name', 'description'];
    }

    /**
     * Returns a flat collection ordered as a depth-first tree.
     * Each item gets a `depth` attribute.
     */
    public static function getTree(?int $excludeId = null): Collection
    {
        $categories = static::orderBy('position')->orderBy('name')->get();

        if ($excludeId) {
            $excludeIds = static::getDescendantIdsFromCollection($categories, $excludeId);
            $excludeIds[] = $excludeId;
            $categories = $categories->reject(fn ($c) => in_array($c->id, $excludeIds));
        }

        $grouped = $categories->groupBy(fn ($c) => $c->parent_id ?? 0);

        $result = new Collection;
        static::buildTree($grouped, 0, 0, $result);

        return $result;
    }

    private static function buildTree(Collection $grouped, int|string $parentId, int $depth, Collection $result): void
    {
        $children = $grouped->get($parentId, collect());

        foreach ($children as $child) {
            $child->depth = $depth;
            $result->push($child);
            static::buildTree($grouped, $child->id, $depth + 1, $result);
        }
    }

    private static function getDescendantIdsFromCollection(Collection $categories, int $parentId): array
    {
        $ids = [];
        $children = $categories->where('parent_id', $parentId);

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, static::getDescendantIdsFromCollection($categories, $child->id));
        }

        return $ids;
    }
}
