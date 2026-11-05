<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function __construct(private CartService $cartService) {}

    public function index(Request $request)
    {
        $lines = $this->cartService->lines($request->session()->get('cart', []));
        $subtotal = $lines->sum('subtotal');

        $items = $lines->map(fn ($line) => [
            'key' => $line['key'],
            'product_id' => $line['product']->id,
            'name' => $line['product']->translated('name'),
            'slug' => $line['product']->slug,
            'price' => $line['product']->price,
            'image' => $line['product']->image,
            'quantity' => $line['quantity'],
            'options' => $this->cartService->describeOptions($line['product'], $line['options']),
            'subtotal' => number_format($line['subtotal'], 2, '.', ''),
        ])->values();

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
            'options' => 'nullable|array',
            'options.*' => 'array',
        ]);

        $product = Product::where('id', $request->product_id)
            ->where('is_active', true)
            ->where('is_orderable_online', true)
            ->firstOrFail();

        $options = $this->cartService->validateOptions($product, $request->input('options', []));
        $key = $this->cartService->lineKey($product->id, $options);

        $cart = $request->session()->get('cart', []);
        $currentQty = $cart[$key]['quantity'] ?? 0;

        $cart[$key] = [
            'product_id' => $product->id,
            'quantity' => $currentQty + ($request->quantity ?? 1),
            'options' => $options,
        ];

        $request->session()->put('cart', $cart);

        return back()->with('success', "{$product->name} added to cart.");
    }

    public function update(Request $request, string $key)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $cart = $request->session()->get('cart', []);

        if (! isset($cart[$key])) {
            return back();
        }

        if ($request->quantity <= 0) {
            unset($cart[$key]);
        } else {
            $cart[$key]['quantity'] = $request->quantity;
        }

        $request->session()->put('cart', $cart);

        return back();
    }

    public function remove(Request $request, string $key)
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$key]);
        $request->session()->put('cart', $cart);

        return back();
    }
}
