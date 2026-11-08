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
            ->withExists('optionGroups as has_options')
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
                'category' => $product->category->translated('name'),
                'is_orderable_online' => $product->is_orderable_online,
                'has_options' => $product->has_options,
            ]);

        return Inertia::render('Welcome', [
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
