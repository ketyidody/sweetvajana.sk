<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #111; margin: 0; padding: 0;">
  <div style="max-width: 600px; margin: 40px auto; padding: 0 20px;">
    <h2 style="margin: 0 0 8px;">{{ config('invoice.seller_name') }}</h2>
    <p style="margin: 0 0 24px; color: #555;">{{ __('Your order has been received. Please find your invoice attached.') }}</p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">{{ __('Order number') }}</td>
        <td style="padding: 10px 14px;">{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">{{ __('Date') }}</td>
        <td style="padding: 10px 14px;">{{ $order->created_at->format('d.m.Y') }}</td>
      </tr>
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">{{ __('Total') }}</td>
        <td style="padding: 10px 14px;">€{{ number_format($order->total, 2, '.', '') }}</td>
      </tr>
    </table>

    <p style="color: #555;">{{ __('Thank you for your order!') }}</p>
    <p style="color: #555; margin: 0;">{{ config('invoice.seller_name') }}</p>
  </div>
</body>
</html>
