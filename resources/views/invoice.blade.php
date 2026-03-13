<!DOCTYPE html>
<html lang="sk">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, Arial, sans-serif; font-size: 12px; color: #111; background: #fff; }
    .page { padding: 40px 48px; }

    /* Header */
    .header { display: flex; justify-content: space-between; margin-bottom: 40px; }
    .seller-name { font-size: 22px; font-weight: bold; color: #222; margin-bottom: 6px; }
    .seller-detail { color: #555; font-size: 11px; line-height: 1.6; }
    .invoice-meta { text-align: right; }
    .invoice-title { font-size: 26px; font-weight: bold; color: #333; margin-bottom: 8px; }
    .invoice-meta table { margin-left: auto; }
    .invoice-meta td { padding: 2px 0 2px 16px; color: #555; font-size: 11px; }
    .invoice-meta td:first-child { color: #888; text-align: right; }

    /* Divider */
    .divider { border: none; border-top: 1px solid #ddd; margin: 24px 0; }

    /* Parties */
    .parties { display: flex; gap: 40px; margin-bottom: 32px; }
    .party { flex: 1; }
    .party-label { font-size: 10px; text-transform: uppercase; letter-spacing: 0.08em; color: #888; margin-bottom: 8px; font-weight: bold; }
    .party-name { font-size: 13px; font-weight: bold; margin-bottom: 4px; }
    .party-detail { font-size: 11px; color: #555; line-height: 1.6; }

    /* Items table */
    .items-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
    .items-table thead tr { background: #f4f4f4; }
    .items-table th { padding: 8px 12px; text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.05em; color: #666; font-weight: bold; border-bottom: 2px solid #ddd; }
    .items-table th.right { text-align: right; }
    .items-table td { padding: 10px 12px; border-bottom: 1px solid #eee; font-size: 12px; vertical-align: top; }
    .items-table td.right { text-align: right; }
    .items-table tbody tr:last-child td { border-bottom: none; }

    /* Totals */
    .totals { float: right; width: 240px; margin-bottom: 40px; }
    .totals table { width: 100%; border-collapse: collapse; }
    .totals td { padding: 5px 0; font-size: 12px; }
    .totals td:last-child { text-align: right; }
    .totals .total-row td { font-size: 14px; font-weight: bold; border-top: 2px solid #222; padding-top: 8px; }
    .clearfix { clear: both; }

    /* Footer */
    .footer { margin-top: 40px; padding-top: 16px; border-top: 1px solid #ddd; font-size: 10px; color: #aaa; text-align: center; }

    /* Status badge */
    .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.06em; }
    .badge-paid { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #fef3c7; color: #92400e; }
  </style>
</head>
<body>
<div class="page">

  <!-- Header -->
  <div class="header">
    <div>
      <div class="seller-name">{{ $seller['name'] }}</div>
      <div class="seller-detail">
        @if($seller['address'])
          {{ $seller['address'] }}<br>
        @endif
        @if($seller['company_id'])
          IČO: {{ $seller['company_id'] }}<br>
        @endif
        @if($seller['vat_number'])
          DIČ/IČ DPH: {{ $seller['vat_number'] }}<br>
        @endif
        @if($seller['email'])
          {{ $seller['email'] }}<br>
        @endif
        @if($seller['phone'])
          {{ $seller['phone'] }}
        @endif
      </div>
    </div>
    <div class="invoice-meta">
      <div class="invoice-title">FAKTÚRA</div>
      <table>
        <tr>
          <td>Číslo faktúry:</td>
          <td><strong>{{ $order->order_number }}</strong></td>
        </tr>
        <tr>
          <td>Dátum vystavenia:</td>
          <td>{{ $order->created_at->format('d.m.Y') }}</td>
        </tr>
        <tr>
          <td>Dátum splatnosti:</td>
          <td>{{ $order->created_at->addDays(14)->format('d.m.Y') }}</td>
        </tr>
        <tr>
          <td>Stav platby:</td>
          <td>
            @if($order->payment_status === 'paid')
              <span class="badge badge-paid">Zaplatená</span>
            @else
              <span class="badge badge-pending">Čaká na platbu</span>
            @endif
          </td>
        </tr>
      </table>
    </div>
  </div>

  <hr class="divider">

  <!-- Buyer & Billing -->
  <div class="parties">
    <div class="party">
      <div class="party-label">Odberateľ</div>
      <div class="party-name">{{ $order->company_name ?: $order->customer_name }}</div>
      <div class="party-detail">
        @if($order->company_name)
          Kontakt: {{ $order->customer_name }}<br>
        @endif
        @if($order->company_id)
          IČO: {{ $order->company_id }}<br>
        @endif
        @if($order->vat_number)
          DIČ/IČ DPH: {{ $order->vat_number }}<br>
        @endif
        {{ $order->billing_address }}<br>
        {{ $order->customer_email }}<br>
        {{ $order->customer_phone }}
      </div>
    </div>
    @if($order->shipping_address && $order->shipping_address !== $order->billing_address)
    <div class="party">
      <div class="party-label">Dodacia adresa</div>
      <div class="party-name">{{ $order->customer_name }}</div>
      <div class="party-detail">{{ $order->shipping_address }}</div>
    </div>
    @endif
    @if($seller['bank_account'])
    <div class="party" style="text-align: right;">
      <div class="party-label">Platobné údaje</div>
      <div class="party-detail">
        IBAN / Účet:<br>
        <strong>{{ $seller['bank_account'] }}</strong>
      </div>
    </div>
    @endif
  </div>

  <!-- Items -->
  <table class="items-table">
    <thead>
      <tr>
        <th style="width: 50%">Popis</th>
        <th class="right" style="width: 15%">Jedn. cena</th>
        <th class="right" style="width: 15%">Množstvo</th>
        <th class="right" style="width: 20%">Suma</th>
      </tr>
    </thead>
    <tbody>
      @foreach($items as $item)
      <tr>
        <td>{{ $item['product_name'] }}</td>
        <td class="right">€{{ number_format($item['price'], 2, '.', '') }}</td>
        <td class="right">{{ $item['quantity'] }}</td>
        <td class="right">€{{ number_format($item['subtotal'], 2, '.', '') }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>

  <!-- Totals -->
  <div class="totals">
    <table>
      <tr>
        <td style="color: #666;">Medzisúčet:</td>
        <td>€{{ number_format($order->subtotal, 2, '.', '') }}</td>
      </tr>
      @if($order->shipping > 0)
      <tr>
        <td style="color: #666;">Doprava:</td>
        <td>€{{ number_format($order->shipping, 2, '.', '') }}</td>
      </tr>
      @endif
      <tr class="total-row">
        <td>Celkom:</td>
        <td>€{{ number_format($order->total, 2, '.', '') }}</td>
      </tr>
    </table>
  </div>
  <div class="clearfix"></div>

  @if($order->notes)
  <div style="margin-top: 24px; padding: 12px 16px; background: #f9f9f9; border-radius: 4px; font-size: 11px; color: #555;">
    <strong>Poznámka:</strong> {{ $order->notes }}
  </div>
  @endif

  <!-- Footer -->
  <div class="footer">
    {{ $seller['name'] }}
    @if($seller['email']) · {{ $seller['email'] }} @endif
    @if($seller['phone']) · {{ $seller['phone'] }} @endif
  </div>

</div>
</body>
</html>
