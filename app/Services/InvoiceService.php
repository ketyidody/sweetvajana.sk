<?php

namespace App\Services;

use App\Mail\InvoiceMail;
use App\Mail\SellerNewOrderMail;
use App\Models\Order;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function generateAndSend(Order $order): void
    {
        if ($order->invoice_path) {
            return;
        }

        try {
            $order->loadMissing('items');

            $items = $order->items->map(fn ($item) => [
                'product_name' => $item->product_name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'subtotal' => $item->subtotal,
            ])->all();

            $seller = [
                'name' => SiteSetting::get('invoice_seller_name', config('invoice.seller_name')),
                'address' => SiteSetting::get('invoice_seller_address', config('invoice.seller_address')),
                'company_id' => SiteSetting::get('invoice_seller_company_id', config('invoice.seller_company_id')),
                'vat_number' => SiteSetting::get('invoice_seller_vat_number', config('invoice.seller_vat_number')),
                'bank_account' => SiteSetting::get('invoice_seller_bank_account', config('invoice.seller_bank_account')),
                'email' => SiteSetting::get('invoice_seller_email', config('invoice.seller_email')),
                'phone' => SiteSetting::get('invoice_seller_phone', config('invoice.seller_phone')),
            ];

            $pdf = Pdf::loadView('invoice', ['order' => $order, 'items' => $items, 'seller' => $seller])
                ->setPaper('a4', 'portrait');

            $invoicePath = 'invoices/'.$order->order_number.'.pdf';
            Storage::put($invoicePath, $pdf->output());

            $order->update(['invoice_path' => $invoicePath]);

            Mail::to($order->customer_email)->send(new InvoiceMail($order, $invoicePath));

            $sellerEmail = SiteSetting::get('invoice_seller_email', config('invoice.seller_email'));
            if ($sellerEmail) {
                Mail::to($sellerEmail)->send(new SellerNewOrderMail($order));
            }
        } catch (\Throwable $e) {
            Log::error('Invoice generation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
