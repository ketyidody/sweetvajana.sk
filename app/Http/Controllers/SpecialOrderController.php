<?php

namespace App\Http\Controllers;

use App\Mail\SellerNewSpecialOrderMail;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\SpecialOrder;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SpecialOrderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'message' => 'nullable|string|max:2000',
            'recaptcha_token' => ['required', 'string', new Recaptcha],
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->where('is_orderable_online', false)
            ->firstOrFail();

        $specialOrder = SpecialOrder::create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'message' => $validated['message'],
        ]);

        $sellerEmail = SiteSetting::get('invoice_seller_email', config('invoice.seller_email'));
        if ($sellerEmail) {
            try {
                Mail::to($sellerEmail)->send(new SellerNewSpecialOrderMail($specialOrder));
            } catch (\Throwable $e) {
                Log::error('Seller special order notification failed', [
                    'special_order_id' => $specialOrder->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return back()->with('success', 'special_order.success');
    }
}
