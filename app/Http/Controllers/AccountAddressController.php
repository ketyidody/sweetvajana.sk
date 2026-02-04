<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class AccountAddressController extends Controller
{
    public function edit()
    {
        return Inertia::render('Account/Addresses', [
            'user' => auth()->user(),
        ]);
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'nullable|string|max:255',
            'shipping_address' => 'nullable|string',
            'billing_address' => 'nullable|string',
        ]);

        auth()->user()->update($validated);

        return redirect()->back()->with('success', 'Addresses updated.');
    }
}
