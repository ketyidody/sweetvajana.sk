<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
</head>
<body style="font-family: Arial, sans-serif; font-size: 14px; color: #111; margin: 0; padding: 0;">
  <div style="max-width: 600px; margin: 40px auto; padding: 0 20px;">
    <h2 style="margin: 0 0 4px;">Nová špeciálna objednávka</h2>
    <p style="margin: 0 0 24px; color: #555;">New special order request received.</p>

    <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold; width: 40%;">Produkt</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->product_name }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">Zákazník</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->customer_name }}</td>
      </tr>
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">E-mail</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->customer_email }}</td>
      </tr>
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">Telefón</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->customer_phone }}</td>
      </tr>
      @if($specialOrder->message)
      <tr style="background: #f5f5f5;">
        <td style="padding: 10px 14px; font-weight: bold;">Správa / Požiadavky</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->message }}</td>
      </tr>
      @endif
      <tr>
        <td style="padding: 10px 14px; font-weight: bold;">Dátum</td>
        <td style="padding: 10px 14px;">{{ $specialOrder->created_at->format('d.m.Y H:i') }}</td>
      </tr>
    </table>
  </div>
</body>
</html>
