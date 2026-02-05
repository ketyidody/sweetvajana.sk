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
                'stock' => $product->stock,
                'image' => $product->image,
                'images' => $allImages,
                'category' => $product->category->translated('name'),
                'category_slug' => $product->category->slug,
            ],
        ]);
    }

    public function index(Request $request)
    {
        $query = Product::with(['category.translations'])
            ->withTranslations()
            ->where('is_active', true);

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
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
                'stock' => $product->stock,
                'category' => $product->category->translated('name'),
            ]);

        $categories = Category::withTranslations()
            ->where('is_active', true)
            ->orderBy('name')
            ->get()
            ->map(fn ($cat) => [
                'name' => $cat->translated('name'),
                'slug' => $cat->slug,
            ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => [
                'category' => $request->category,
                'min_price' => $request->min_price,
                'max_price' => $request->max_price,
            ],
        ]);
    }
}
