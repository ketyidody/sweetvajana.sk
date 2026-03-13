<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);
        $items = [];
        $subtotal = 0;

        if (! empty($cart)) {
            $products = Product::whereIn('id', array_keys($cart))
                ->where('is_active', true)
                ->withTranslations()
                ->get()
                ->keyBy('id');

            foreach ($cart as $productId => $cartItem) {
                if ($products->has($productId)) {
                    $product = $products[$productId];
                    $quantity = $cartItem['quantity'];
                    $lineTotal = $product->price * $quantity;
                    $subtotal += $lineTotal;

                    $items[] = [
                        'product_id' => $product->id,
                        'name' => $product->translated('name'),
                        'slug' => $product->slug,
                        'price' => $product->price,
                        'image' => $product->image,
                        'quantity' => $quantity,
                        'subtotal' => number_format($lineTotal, 2, '.', ''),
                    ];
                }
            }
        }

        return Inertia::render('Cart/Index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'total' => number_format($subtotal, 2, '.', ''),
        ]);
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'integer|min:1',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('is_active', true)
            ->where('is_orderable_online', true)
            ->firstOrFail();

        $cart = $request->session()->get('cart', []);
        $currentQty = $cart[$product->id]['quantity'] ?? 0;
        $newQty = $currentQty + ($request->quantity ?? 1);

        if ($newQty > 0) {
            $cart[$product->id] = ['quantity' => $newQty];
        }

        $request->session()->put('cart', $cart);

        return back()->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, int $productId)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $request->session()->get('cart', []);

        if ($request->quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId] = [
                'quantity' => $request->quantity,
            ];
        }

        $request->session()->put('cart', $cart);

        return back();
    }

    public function remove(Request $request, int $productId)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$productId]);
        $request->session()->put('cart', $cart);

        return back();
    }
}
