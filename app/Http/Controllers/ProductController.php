<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['category.translations', 'translations']);

        $allImages = collect([$product->image])
            ->merge($product->images ?? [])
            ->values()
            ->all();

        return Inertia::render('Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->translated('name'),
                'slug' => $product->slug,
                'description' => $product->translated('description'),
                'price' => $product->price,
                'image' => $product->image,
                'images' => $allImages,
                'category' => $product->category->translated('name'),
                'category_slug' => $product->category->slug,
                'is_orderable_online' => $product->is_orderable_online,
                'soonest_availability' => $product->soonest_availability,
                'image_url' => $product->image ? url($product->image) : null,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Product::with(['category.translations'])
            ->withTranslations()
            ->where('is_active', true);

        if ($request->category === 'available-for-collection') {
            $query->where('is_available_for_collection', true);
        } elseif ($request->filled('category')) {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $categoryIds = $this->getCategoryAndDescendantIds($category->id);
                $query->whereIn('category_id', $categoryIds);
            }
        }

        $products = $query->latest()
            ->paginate(12)
            ->withQueryString()
            ->through(fn ($product) => [
                'id' => $product->id,
                'name' => $product->translated('name'),
                'slug' => $product->slug,
                'description' => $product->translated('description'),
                'price' => $product->price,
                'image' => $product->image,
                'category' => $product->category->translated('name'),
                'is_orderable_online' => $product->is_orderable_online,
                'is_available_for_collection' => $product->is_available_for_collection,
            ]);

        $tree = Category::getTree();
        $tree->load('translations');
        $categories = $tree
            ->filter(fn ($c) => $c->is_active)
            ->map(fn ($cat) => [
                'name' => $cat->translated('name'),
                'slug' => $cat->slug,
                'depth' => $cat->depth,
            ])
            ->values();

        $availableForCollectionCount = Product::where('is_active', true)
            ->where('is_available_for_collection', true)
            ->count();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'availableForCollectionCount' => $availableForCollectionCount,
            'filters' => [
                'category' => $request->category,
            ],
        ]);
    }

    private function getCategoryAndDescendantIds(int $categoryId): array
    {
        $ids = [$categoryId];
        $children = Category::where('parent_id', $categoryId)->pluck('id');

        foreach ($children as $childId) {
            $ids = array_merge($ids, $this->getCategoryAndDescendantIds($childId));
        }

        return $ids;
    }
}
