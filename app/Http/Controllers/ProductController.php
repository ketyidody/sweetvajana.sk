<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Corpus;
use App\Models\CreamFlavor;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        $product->load(['category.translations', 'translations']);

        $locale = app()->getLocale();

        $allImages = collect([$product->image])
            ->merge($product->images ?? [])
            ->values()
            ->all();

        $productData = [
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
            'corpuses' => [],
            'cream_flavors' => [],
        ];

        if (! $product->is_orderable_online) {
            $corpuses = Corpus::with('translations')->get();
            $creamFlavors = CreamFlavor::with('translations')->get();

            $productData['corpuses'] = $corpuses->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->translated('name', $locale),
            ]);
            $productData['cream_flavors'] = $creamFlavors->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->translated('name', $locale),
            ]);
        }

        return Inertia::render('Products/Show', ['product' => $productData]);
    }

    public function index(Request $request)
    {
        if (! $request->filled('category')) {
            $first = Category::whereNull('parent_id')
                ->where('is_active', true)
                ->orderBy('position')
                ->orderBy('name')
                ->first();

            if ($first) {
                return redirect($request->url().'?category='.$first->slug);
            }
        }

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
