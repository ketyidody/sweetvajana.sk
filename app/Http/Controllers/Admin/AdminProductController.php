<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('category')->latest()->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Products/Form', [
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:20480',
            'additional_images' => 'nullable|array|max:10',
            'additional_images.*' => 'image|max:20480',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('products', 'public')
            );
        }

        $images = [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $images[] = Storage::url($file->store('products', 'public'));
            }
        }
        $validated['images'] = $images;
        unset($validated['additional_images']);

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => $product,
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:20480',
            'additional_images' => 'nullable|array|max:10',
            'additional_images.*' => 'image|max:20480',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'string',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('image')) {
            $validated['image'] = Storage::url(
                $request->file('image')->store('products', 'public')
            );
        } else {
            unset($validated['image']);
        }

        $images = $request->input('existing_images', []) ?? [];
        if ($request->hasFile('additional_images')) {
            foreach ($request->file('additional_images') as $file) {
                $images[] = Storage::url($file->store('products', 'public'));
            }
        }
        $validated['images'] = $images;
        unset($validated['additional_images'], $validated['existing_images']);

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }
}
