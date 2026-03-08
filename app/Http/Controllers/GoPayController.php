<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\GoPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GoPayController extends Controller
{
    public function __construct(private GoPayService $goPayService) {}

    public function return(Request $request)
    {
        if (! $request->hasValidSignatureWhileIgnoring(['id'])) {
            abort(403);
        }

        $order = Order::where('id', $request->query('order_id'))->firstOrFail();

        $request->session()->put('last_order_id', $order->id);

        if ($order->gopay_payment_id) {
            $response = $this->goPayService->getPaymentStatus($order->gopay_payment_id);

            if ($response->hasSucceed()) {
                $newStatus = $this->goPayService->mapGoPayState($response->json['state']);

                if ($order->payment_status !== $newStatus) {
                    $order->update(['payment_status' => $newStatus]);
                }
            }
        }

        return redirect()->route('orders.confirmation', $order);
    }

    public function notify(Request $request)
    {
        $gopayPaymentId = $request->input('id');

        if (! $gopayPaymentId) {
            return response('Missing payment ID', 400);
        }

        $order = Order::where('gopay_payment_id', $gopayPaymentId)->first();

        if (! $order) {
            Log::warning('GoPay notify: order not found', ['gopay_payment_id' => $gopayPaymentId]);

            return response('Order not found', 404);
        }

        $response = $this->goPayService->getPaymentStatus($gopayPaymentId);

        if ($response->hasSucceed()) {
            $newStatus = $this->goPayService->mapGoPayState($response->json['state']);

            if ($order->payment_status !== $newStatus) {
                $order->update(['payment_status' => $newStatus]);
            }
        } else {
            Log::error('GoPay notify: status check failed', [
                'gopay_payment_id' => $gopayPaymentId,
                'status_code' => $response->statusCode,
            ]);
        }

        return response('OK', 200);
    }
}
