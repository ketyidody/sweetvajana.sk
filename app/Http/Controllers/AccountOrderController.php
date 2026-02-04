<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Inertia\Inertia;

class AccountOrderController extends Controller
{
    public function index()
    {
        return Inertia::render('Account/Orders/Index', [
            'orders' => auth()->user()->orders()->latest()->get(),
        ]);
    }

    public function show(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return Inertia::render('Account/Orders/Show', [
            'order' => $order->load('items'),
        ]);
    }
}
