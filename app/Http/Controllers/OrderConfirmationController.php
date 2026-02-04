<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderConfirmationController extends Controller
{
    public function show(Request $request, Order $order)
    {
        $isOwner = $request->user() && $request->user()->id === $order->user_id;
        $isSessionOrder = $request->session()->get('last_order_id') === $order->id;

        if (! $isOwner && ! $isSessionOrder) {
            abort(403);
        }

        $order->load('items');

        return Inertia::render('Orders/Confirmation', [
            'order' => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'subtotal' => $order->subtotal,
                'tax' => $order->tax,
                'shipping' => $order->shipping,
                'total' => $order->total,
                'status' => $order->status,
                'notes' => $order->notes,
                'items' => $order->items->map(fn ($item) => [
                    'product_name' => $item->product_name,
                    'price' => $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                ]),
            ],
        ]);
    }
}
