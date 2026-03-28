<?php

namespace App\Http\Controllers;

use App\Mail\SellerNewSpecialOrderMail;
use App\Models\Addition;
use App\Models\Corpus;
use App\Models\CreamFlavor;
use App\Models\Product;
use App\Models\SiteSetting;
use App\Models\SpecialOrder;
use App\Rules\Recaptcha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
            'size_id' => 'nullable|integer|exists:sizes,id',
            'corpus_id' => [Corpus::exists() ? 'required' : 'nullable', 'integer', 'exists:corpuses,id'],
            'cream_flavor_id' => [CreamFlavor::exists() ? 'required' : 'nullable', 'integer', 'exists:cream_flavors,id'],
            'addition_ids' => 'nullable|array',
            'addition_ids.*' => 'integer|exists:additions,id',
            'recaptcha_token' => ['required', 'string', new Recaptcha],
        ]);

        $product = Product::where('id', $validated['product_id'])
            ->where('is_active', true)
            ->where('is_orderable_online', false)
            ->firstOrFail();

        if ($product->sizes()->exists() && empty($validated['size_id'])) {
            throw ValidationException::withMessages(['size_id' => __('validation.required')]);
        }

        $choices = [];

        if (! empty($validated['size_id'])) {
            $size = $product->sizes()->where('size_id', $validated['size_id'])->first();
            if ($size) {
                $choices['size'] = ['id' => $size->id, 'name' => $size->name, 'price' => $size->pivot->price];
            }
        }

        if (! empty($validated['corpus_id'])) {
            $corpus = Corpus::find($validated['corpus_id']);
            if ($corpus) {
                $choices['corpus'] = ['id' => $corpus->id, 'name' => $corpus->name];
            }
        }

        if (! empty($validated['cream_flavor_id'])) {
            $creamFlavor = CreamFlavor::find($validated['cream_flavor_id']);
            if ($creamFlavor) {
                $choices['cream_flavor'] = ['id' => $creamFlavor->id, 'name' => $creamFlavor->name];
            }
        }

        if (! empty($validated['addition_ids'])) {
            $additions = Addition::whereIn('id', $validated['addition_ids'])->get();
            $choices['additions'] = $additions->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'price' => $a->price,
            ])->toArray();
        }

        $specialOrder = SpecialOrder::create([
            'product_id' => $product->id,
            'product_name' => $product->name,
            'customer_name' => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'customer_phone' => $validated['customer_phone'],
            'message' => $validated['message'],
            'choices' => ! empty($choices) ? $choices : null,
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
