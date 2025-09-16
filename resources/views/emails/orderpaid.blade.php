<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('Wachtwoord resetten') }}</title>
  <style>
    body {
      font-family: 'Segoe UI', Arial, sans-serif;
      background: #f7f7f7;
      color: #222;
      margin: 0;
      padding: 0;
    }
    .email-container {
      max-width: 600px;
      margin: 40px auto;
      background: #fff;
      border-radius: 8px;
      padding: 32px 24px;
      box-shadow: 0 2px 8px #eee;
      box-sizing: border-box;
    }
    .header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin-bottom: 32px;
    }
    .logo {
      height: 40px;
    }
    .company-info {
      font-size: 15px;
      color: #555;
      text-align: right;
    }
    h1 {
      color: #ab0f14;
      font-size: 2em;
      margin-bottom: 0;
      font-family: 'Segoe UI', Arial, sans-serif;
    }
    .meta {
      margin-bottom: 18px;
      font-size: 15px;
    }
    .address-blocks {
      display: flex;
      flex-wrap: wrap;
      gap: 30px;
      margin-bottom: 18px;
    }
    .address {
      min-width: 220px;
      flex: 1;
      font-size: 15px;
      background: #f7f7f7;
      border-radius: 6px;
      padding: 16px;
    }
    .address strong {
      font-size: 1.08em;
      color: #ab0f14;
    }
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      margin-bottom: 18px;
      box-sizing: border-box;
      max-width: 100%;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      font-size: 15px;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #e3e3e3;
      padding: 10px;
    }
    th {
      background: #f7f7f7;
      color: #222;
      font-weight: 600;
    }
    tfoot td {
      font-weight: bold;
      background: #f7f7f7;
    }
    .summary {
      margin-top: 24px;
      font-size: 16px;
    }
    .footer {
      font-size: 15px;
      color: #888;
      margin-top: 30px;
      text-align: left;
    }
  </style>
  </style>
</head>
<body>
  <div class="email-container">
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:32px;">
      <tr>
        <td style="text-align:left; font-size:15px; color:#555; vertical-align:top;">
          Stichting Lucide Inkt<br>
          Kerspellaan 12<br>
          7824 JG Emmen<br>
          info@lucideinkt.nl<br>
        </td>
      </tr>
    </table>
    <h3>{{ __('Bedankt voor je bestelling!') }}</h3>
    <div class="meta">
      <span><strong>{{ __('Ordernummer:') }}</strong> {{ $order->id }}</span><br>
      <span><strong>{{ __('Besteldatum:') }}</strong> {{ $order->created_at->format('d-m-Y H:i') }}</span>
    </div>
    <div class="address-blocks">
        {{-- Bezorgtype en afleveradres --}}
        @if(isset($delivery) && !empty($delivery['deliveryType']) && strtolower($delivery['deliveryType']) === 'pickup')
          <div class="address" style="background:#f7f7f7;">
            <strong>Bezorging</strong><br>
            Afhalen bij afhaalpunt<br>
            {{ $pickupLocation['locationName'] ?? '-' }}<br>
            {{ $pickupLocation['street'] ?? '' }} {{ $pickupLocation['number'] ?? '' }}<br>
            {{ $pickupLocation['postalCode'] ?? '' }} {{ $pickupLocation['city'] ?? '' }}<br>
          </div>
        @else
          <div class="address" style="background:#f7f7f7;">
            <strong>Bezorging</strong><br>
            Thuisbezorging
          </div>
        @endif
      <div class="address">
        <strong>{{ __('Factuuradres') }}</strong><br>
        {{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
        @if($order->customer->billing_company)
          {{ $order->customer->billing_company }}<br>
        @endif
        {{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ $order->customer->billing_house_number_addition ? ' '.$order->customer->billing_house_number_addition : '' }}<br>
        {{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
        {{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
        @if($order->customer->billing_phone)
          {{ __('Tel:') }} {{ $order->customer->billing_phone }}<br>
        @endif
        {{ __('Email:') }} {{ $order->customer->billing_email }}
      </div>
      <div class="address">
        <strong>{{ __('Verzendadres') }}</strong><br>
        @if($order->shipping_street)
          {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
          @if($order->shipping_company)
            {{ $order->shipping_company }}<br>
          @endif
          {{ $order->shipping_street }} {{ $order->shipping_house_number }}{{ $order->shipping_house_number_addition ? ' '.$order->shipping_house_number_addition : '' }}<br>
          {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
          {{ config('countries.' . $order->shipping_country) ?? $order->shipping_country }}<br>
          @if($order->shipping_phone)
            {{ __('Tel:') }} {{ $order->shipping_phone }}<br>
          @endif
        @else
          {{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
          @if($order->customer->billing_company)
            {{ $order->customer->billing_company }}<br>
          @endif
          {{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ $order->customer->billing_house_number_addition ? ' '.$order->customer->billing_house_number_addition : '' }}<br>
          {{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
          {{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
          @if($order->customer->billing_phone)
            {{ __('Tel:') }} {{ $order->customer->billing_phone }}<br>
          @endif
        @endif
      </div>
    </div>
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>{{ __('Product') }}</th>
            <th>{{ __('Aantal') }}</th>
            <th>{{ __('Stukprijs') }}</th>
            <th>{{ __('Subtotaal') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->items as $item)
          <tr>
            <td>{{ $item->product_name }}</td>
            <td>{{ $item->quantity }}</td>
            <td>€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
            <td>€ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
          </tr>
          @endforeach
        </tbody>
        <tfoot>
          <tr>
            <td colspan="3" style="text-align:right;">{{ __('Totaal') }}</td>
            <td colspan="2">€ {{ number_format($order->total_before, 2, ',', '.') }}</td>
          </tr>
          @if(isset($order->discount_type) && $order->discount_price_total > 0)
          <tr>
            <td colspan="3" style="text-align:right;">{{ __('Korting') }} ({{ $order->discount_type === 'percent' ? intval($order->discount_value) . '%' : '€ ' . number_format($order->discount_value, 2, ',', '.') }})</td>
            <td colspan="2">-€ {{ number_format($order->discount_price_total, 2, ',', '.') }}</td>
          </tr>
          <tr>
            <td colspan="3" style="text-align:right; font-weight:bold;">{{ __('Totaal na korting') }}</td>
            <td colspan="2" style="font-weight:bold;">€ {{ number_format($order->total - $order->discount_price_total, 2, ',', '.') }}</td>
          </tr>
          @endif

           @if(!empty($order->shipping_cost_amount) && $order->shipping_cost_amount > 0)
            <tr>
              <td colspan="3" style="text-align:right;">{{ __('Verzendkosten') }}</td>
              <td colspan="2">€ {{ is_numeric($order->shipping_cost_amount) ? number_format($order->shipping_cost_amount, 2, ',', '.') : number_format((float)($order->shipping_cost_amount->amount ?? 0), 2, ',', '.') }}</td>
            </tr>
            <tr>
              <td colspan="3" style="text-align:right; font-weight:bold;">{{ __('Totaal incl. verzendkosten') }}</td>
              <td colspan="2" style="font-weight:bold;">€ {{ number_format($order->total, 2, ',', '.') }}</td>
            </tr>
            @endif

        </tfoot>
      </table>
    </div>
    <div class="summary">
      <strong>{{ __('Je bestelling wordt zo snel mogelijk verzonden. Je ontvangt een e-mail zodra je pakket onderweg is.') }}</strong>
    </div>
    <div class="footer">
      {{ __('Heb je vragen? Neem gerust contact met ons op.') }}<br>
      {{ __('Met vriendelijke groet,') }}<br>
      {{ config('app.name') }}
    </div>
  </div>
  </div>
</body>
</html>
