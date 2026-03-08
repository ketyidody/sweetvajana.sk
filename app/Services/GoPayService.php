<?php

namespace App\Services;

use App\Models\Order;
use GoPay\Api;
use GoPay\Definition\Language;
use GoPay\Definition\Payment\Currency;
use GoPay\Definition\Payment\PaymentInstrument;
use GoPay\Http\Response;

class GoPayService
{
    private $gopay;

    public function __construct()
    {
        $this->gopay = Api::payments([
            'goid' => config('services.gopay.goid'),
            'clientId' => config('services.gopay.client_id'),
            'clientSecret' => config('services.gopay.client_secret'),
            'gatewayUrl' => config('services.gopay.is_production')
                ? 'https://gate.gopay.cz'
                : 'https://gw.sandbox.gopay.com',
            'language' => Language::CZECH,
        ]);
    }

    public function createPayment(Order $order, string $returnUrl, string $notifyUrl): Response
    {
        $items = $order->items->map(fn ($item) => [
            'name' => $item->product_name,
            'amount' => (int) round($item->subtotal * 100),
            'count' => $item->quantity,
        ])->toArray();

        $locale = app()->getLocale();
        $language = match ($locale) {
            'sk' => Language::SLOVAK,
            default => Language::ENGLISH,
        };

        return $this->gopay->createPayment([
            'amount' => (int) round($order->total * 100),
            'currency' => Currency::EUROS,
            'order_number' => $order->order_number,
            'order_description' => 'Order '.$order->order_number,
            'payer' => [
                'contact' => [
                    'first_name' => $order->customer_name,
                    'email' => $order->customer_email,
                    'phone_number' => $order->customer_phone,
                ],
                'allowed_payment_instruments' => [
                    PaymentInstrument::PAYMENT_CARD,
                    PaymentInstrument::BANK_ACCOUNT,
                    PaymentInstrument::GPAY,
                    PaymentInstrument::APPLE_PAY,
                ],
            ],
            'items' => $items,
            'callback' => [
                'return_url' => $returnUrl,
                'notification_url' => $notifyUrl,
            ],
            'lang' => $language,
        ]);
    }

    public function getPaymentStatus(string $gopayPaymentId): Response
    {
        return $this->gopay->getStatus($gopayPaymentId);
    }

    public function mapGoPayState(string $state): string
    {
        return match ($state) {
            'PAID' => 'paid',
            'CANCELED', 'TIMEOUTED' => 'failed',
            'REFUNDED', 'PARTIALLY_REFUNDED' => 'refunded',
            default => 'pending',
        };
    }
}
