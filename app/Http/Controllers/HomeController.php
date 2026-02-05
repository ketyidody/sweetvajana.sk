<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::with(['category.translations'])
            ->withTranslations()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->latest()
            ->get()
            ->map(fn ($product) => [
                'id' => $product->id,
                'name' => $product->translated('name'),
                'slug' => $product->slug,
                'description' => $product->translated('description'),
                'price' => $product->price,
                'image' => $product->image,
                'stock' => $product->stock,
                'category' => $product->category->translated('name'),
            ]);

        return Inertia::render('Welcome', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
