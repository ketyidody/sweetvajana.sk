<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Size;
use Illuminate\Http\Request;

class AdminProductSizeController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'size_id' => 'required|integer|exists:sizes,id',
            'price' => 'required|numeric|min:0',
        ]);

        $product->sizes()->attach($validated['size_id'], ['price' => $validated['price']]);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Size added to product.');
    }

    public function update(Request $request, Product $product, Size $size)
    {
        $request->validate(['price' => 'required|numeric|min:0']);

        $product->sizes()->updateExistingPivot($size->id, ['price' => $request->price]);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Size price updated.');
    }

    public function destroy(Product $product, Size $size)
    {
        $product->sizes()->detach($size->id);

        return redirect()->route('admin.products.edit', $product)->with('success', 'Size removed from product.');
    }
}
