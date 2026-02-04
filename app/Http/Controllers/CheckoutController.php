<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index');
        }

        $products = Product::whereIn('id', array_keys($cart))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0;

        foreach ($cart as $productId => $cartItem) {
            if ($products->has($productId)) {
                $product = $products[$productId];
                $quantity = min($cartItem['quantity'], $product->stock);

                if ($quantity <= 0) {
                    continue;
                }

                $lineTotal = $product->price * $quantity;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => $quantity,
                    'subtotal' => number_format($lineTotal, 2, '.', ''),
                ];
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Your cart items are no longer available.');
        }

        $user = $request->user();

        return Inertia::render('Checkout/Index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'total' => number_format($subtotal, 2, '.', ''),
            'customer' => $user ? [
                'name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone ?? '',
                'address' => $user->address ?? '',
            ] : null,
        ]);
    }

    public function store(CheckoutRequest $request)
    {
        $cart = $request->session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($request, $cart) {
            $products = Product::whereIn('id', array_keys($cart))
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = [];
            $subtotal = 0;

            foreach ($cart as $productId => $cartItem) {
                if (! $products->has($productId)) {
                    continue;
                }

                $product = $products[$productId];
                $quantity = min($cartItem['quantity'], $product->stock);

                if ($quantity <= 0) {
                    continue;
                }

                $lineTotal = $product->price * $quantity;
                $subtotal += $lineTotal;

                $items[] = [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $product->price,
                    'quantity' => $quantity,
                    'subtotal' => $lineTotal,
                ];

                $product->decrement('stock', $quantity);
            }

            if (empty($items)) {
                return null;
            }

            $orderNumber = 'SV-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));

            $order = Order::create([
                'user_id' => $request->user()?->id,
                'order_number' => $orderNumber,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'shipping_address' => $request->shipping_address,
                'billing_address' => $request->shipping_address,
                'subtotal' => $subtotal,
                'tax' => 0,
                'shipping' => 0,
                'total' => $subtotal,
                'status' => 'pending',
                'payment_method' => 'cash_on_delivery',
                'payment_status' => 'pending',
                'notes' => $request->notes,
            ]);

            foreach ($items as $item) {
                $order->items()->create($item);
            }

            return $order;
        });

        if (! $order) {
            return redirect()->route('cart.index')->with('error', 'The items in your cart are no longer available.');
        }

        $request->session()->forget('cart');
        $request->session()->put('last_order_id', $order->id);

        return redirect()->route('orders.confirmation', $order);
    }
}
