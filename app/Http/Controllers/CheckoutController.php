<?php

namespace App\Http\Controllers;

use App\Delivery\DeliveryDriver;
use App\Delivery\DeliveryManager;
use App\Http\Requests\CheckoutRequest;
use App\Models\Language;
use App\Models\Order;
use App\Services\CartService;
use App\Services\GoPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
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

        $cartService = app(CartService::class);
        $lines = $cartService->lines($cart);
        $subtotal = $lines->sum('subtotal');

        $items = $lines->map(fn ($line) => [
            'key' => $line['key'],
            'product_id' => $line['product']->id,
            'name' => $line['product']->translated('name'),
            'price' => $line['product']->price,
            'image' => $line['product']->image,
            'quantity' => $line['quantity'],
            'options' => $cartService->describeOptions($line['product'], $line['options']),
            'subtotal' => number_format($line['subtotal'], 2, '.', ''),
        ])->values()->all();

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'Your cart items are no longer available.');
        }

        $user = $request->user();

        return Inertia::render('Checkout/Index', [
            'items' => $items,
            'subtotal' => number_format($subtotal, 2, '.', ''),
            'deliveryMethods' => app(DeliveryManager::class)->available()
                ->map(fn (DeliveryDriver $driver) => $driver->toArray($subtotal)),
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
            $cartService = app(CartService::class);
            $lines = $cartService->lines($cart, lockForUpdate: true);
            $defaultLocale = Language::getDefault()?->code ?? 'sk';
            $subtotal = $lines->sum('subtotal');
            $delivery = app(DeliveryManager::class)->findAvailable($request->delivery_method);
            $shippingCost = $delivery->price($subtotal);
            $shippingAddress = $delivery->requiresAddress() ? $request->shipping_address : null;

            $items = $lines->map(fn ($line) => [
                'product_id' => $line['product']->id,
                'product_name' => $line['product']->name,
                'options' => $cartService->describeOptions($line['product'], $line['options'], $defaultLocale) ?: null,
                'price' => $line['product']->price,
                'quantity' => $line['quantity'],
                'subtotal' => $line['subtotal'],
            ])->all();

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
                'delivery_method' => $delivery->key(),
                'shipping_address' => $shippingAddress,
                'billing_address' => $request->billing_address ?: $shippingAddress,
                'company_name' => $request->company_name,
                'company_id' => $request->company_id,
                'vat_number' => $request->vat_number,
                'subtotal' => $subtotal,
                'tax' => 0,
                'shipping' => $shippingCost,
                'total' => $subtotal + $shippingCost,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'notes' => $request->notes,
                'locale' => app()->getLocale(),
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

        if ($order->payment_method === 'gopay') {
            return $this->handleGoPayPayment($request, $order);
        }

        $request->session()->put('last_order_id', $order->id);

        return redirect()->route('orders.confirmation', $order);
    }

    private function handleGoPayPayment(Request $request, Order $order)
    {
        $goPayService = app(GoPayService::class);

        $returnUrl = URL::signedRoute('gopay.return', ['order_id' => $order->id]);
        $notifyUrl = route('gopay.notify');

        $order->load('items');
        $response = $goPayService->createPayment($order, $returnUrl, $notifyUrl);

        if ($response->hasSucceed()) {
            $order->update(['gopay_payment_id' => (string) $response->json['id']]);

            return Inertia::location($response->json['gw_url']);
        }

        Log::error('GoPay payment creation failed', [
            'order_id' => $order->id,
            'status_code' => $response->statusCode,
            'response' => $response->json,
        ]);

        $request->session()->put('last_order_id', $order->id);

        return redirect()->route('orders.confirmation', $order);
    }
}
