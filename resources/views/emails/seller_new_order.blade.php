<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #111; margin: 0; padding: 0;">
  <div style="max-width: 600px; margin: 40px auto; padding: 0 20px;">
    <h2 style="margin: 0 0 4px;">Nová zaplatená objednávka</h2>
    <p style="margin: 0 0 24px; color: #555;">New paid order received.</p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold; width: 40%;">Číslo objednávky</td>
        <td style="padding: 10px 14px;">{{ $order->order_number }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">Dátum</td>
        <td style="padding: 10px 14px;">{{ $order->created_at->format('d.m.Y H:i') }}</td>
      </tr>
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">Zákazník</td>
        <td style="padding: 10px 14px;">{{ $order->customer_name }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">E-mail</td>
        <td style="padding: 10px 14px;">{{ $order->customer_email }}</td>
      </tr>
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">Telefón</td>
        <td style="padding: 10px 14px;">{{ $order->customer_phone }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">Dodacia adresa</td>
        <td style="padding: 10px 14px;">{{ $order->shipping_address }}</td>
      </tr>
      @if($order->notes)
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">Poznámky</td>
        <td style="padding: 10px 14px;">{{ $order->notes }}</td>
      </tr>
      @endif
    </table>

    <h3 style="margin: 0 0 8px; font-size: 14px;">Položky objednávky</h3>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
      <tr style="background: #f5f5f5;">
        <td style="padding: 8px 14px; font-weight: bold;">Produkt</td>
        <td style="padding: 8px 14px; font-weight: bold; text-align: right;">Mn.</td>
        <td style="padding: 8px 14px; font-weight: bold; text-align: right;">Suma</td>
      </tr>
      @foreach($order->items as $item)
      <tr>
        <td style="padding: 8px 14px;">{{ $item->product_name }}</td>
        <td style="padding: 8px 14px; text-align: right;">{{ $item->quantity }}</td>
        <td style="padding: 8px 14px; text-align: right;">€{{ number_format($item->subtotal, 2, '.', '') }}</td>
      </tr>
      @endforeach
      <tr style="background: #f5f5f5;">
        <td colspan="2" style="padding: 10px 14px; font-weight: bold; text-align: right;">Celkom:</td>
        <td style="padding: 10px 14px; font-weight: bold; text-align: right;">€{{ number_format($order->total, 2, '.', '') }}</td>
      </tr>
    </table>
  </div>
</body>
</html>
