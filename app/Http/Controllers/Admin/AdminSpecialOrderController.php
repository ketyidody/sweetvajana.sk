<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SpecialOrder;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminSpecialOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = SpecialOrder::with('product')->latest();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        return Inertia::render('Admin/SpecialOrders/Index', [
            'specialOrders' => $query->get(),
            'currentStatus' => $request->status ?? 'all',
        ]);
    }

    public function show(SpecialOrder $specialOrder)
    {
        $specialOrder->load('product');

        return Inertia::render('Admin/SpecialOrders/Show', [
            'specialOrder' => $specialOrder,
        ]);
    }

    public function update(Request $request, SpecialOrder $specialOrder)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,contacted,completed,cancelled',
        ]);

        $specialOrder->update($validated);

        return redirect()->back()->with('success', 'Special order updated.');
    }

    public function destroy(SpecialOrder $specialOrder)
    {
        $specialOrder->delete();

        return redirect()->route('admin.special-orders.index')
            ->with('success', 'Special order deleted.');
    }
}
