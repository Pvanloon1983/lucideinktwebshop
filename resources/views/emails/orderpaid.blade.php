<!DOCTYPE html>
<html lang="nl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ __('Wachtwoord resetten') }}</title>
  <style>
    body {
      font-family: 'DelimaMTProRegular', Arial, sans-serif;
      background: #ecd6af;
      color: #620505;
      margin: 0;
      padding: 0 15px;
    }
    .email-container {
      max-width: 500px;
      margin: 40px auto;
      background: #fff;
      border-radius: 4px;
      padding: 32px 12px;
      overflow-x: auto;
      box-sizing: border-box;
    }
    .table-responsive {
      width: 100%;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;
      margin-bottom: 18px;
      box-sizing: border-box;
      max-width: 100%;
    }
    table {
      min-width: 400px;
    }
    h1 {
      color: #ab0f14;
      font-size: 26px;
      margin-bottom: 18px;
      font-family: 'DelimaMTProRegular', Arial, sans-serif;
    }
    p {
      font-size: 17px;
      margin-bottom: 18px;
    }
    .btn {
      background: #ab0f14;
      color: #fff !important;
      border-radius: 4px;
      padding: 12px 28px;
      text-decoration: none;
      font-size: 16px;
      display: inline-block;
      font-weight: 400;
    }
    .footer {
      font-size: 15px;
      color: #888;
      margin-top: 30px;
      text-align: left;
    }
  </style>
</head>
<body>
  <div class="email-container">
<h1>{{ __('Bedankt voor uw bestelling!', [], 'nl') }}</h1>

<p>{{ __('Beste', [], 'nl') }} {{ $order->customer->billing_first_name }},</p>

<p>{{ __('We hebben uw betaling ontvangen en gaan direct aan de slag met uw bestelling.', [], 'nl') }}</p>

<hr>

<p><strong>{{ __('Ordernummer', [], 'nl') }}:</strong> {{ $order->id }}<br>
<strong>{{ __('Besteldatum', [], 'nl') }}:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>

<div class="table-responsive">
<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;">
  <thead>
    <tr style="background:#f7f7f7;">
      <th align="left">{{ __('Product', [], 'nl') }}</th>
      <th align="center">{{ __('Aantal', [], 'nl') }}</th>
      <th align="right">{{ __('Stukprijs', [], 'nl') }}</th>
      <th align="right">{{ __('Subtotaal', [], 'nl') }}</th>
    </tr>
  </thead>
  <tbody>
    @foreach($order->items as $item)
    <tr>
      <td>{{ $item->product_name }}</td>
      <td align="center">{{ $item->quantity }}</td>
      <td align="right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
      <td align="right">€ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
    </tr>
    @endforeach
  </tbody>
  </table>
</div>

<p><strong>{{ __('Totaal', [], 'nl') }}:</strong> € {{ number_format($order->total, 2, ',', '.') }}</p>

<hr>

<p>{{ __('Uw bestelling wordt zo snel mogelijk verzonden. U ontvangt een e-mail zodra uw pakket onderweg is.', [], 'nl') }}</p>

<p>{{ __('Heeft u vragen? Neem gerust contact met ons op.', [], 'nl') }}</p>

<p>{{ __('Met vriendelijke groet,', [], 'nl') }}<br>
{{ config('app.name') }}</p>
  </div>
</body>
</html>
