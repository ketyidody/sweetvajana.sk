<?php

namespace App\Http\Requests;

use App\Delivery\DeliveryManager;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $delivery = app(DeliveryManager::class);

        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:50',
            'delivery_method' => ['required', 'string', Rule::in($delivery->available()->map(fn ($driver) => $driver::key())->all())],
            'shipping_address' => [
                Rule::requiredIf(fn () => $delivery->findAvailable($this->input('delivery_method'))?->requiresAddress() ?? false),
                'nullable',
                'string',
                'max:1000',
            ],
            'notes' => 'nullable|string|max:2000',
            'billing_address' => 'nullable|string|max:1000',
            'company_name' => 'nullable|string|max:255',
            'company_id' => 'nullable|string|max:50',
            'vat_number' => 'nullable|string|max:50',
            'payment_method' => 'required|in:gopay',
            'recaptcha_token' => ['required', 'string', new Recaptcha],
        ];
    }
}
