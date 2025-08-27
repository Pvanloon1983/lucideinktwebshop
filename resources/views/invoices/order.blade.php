<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; color: #222; margin: 0; padding: 0; }
    .invoice-container { width: 600px; margin: 40px auto; background: #fff; padding: 24px; border: 1px solid #e3e3e3; }
        h1 { color: #b30000; font-size: 2.1em; margin-bottom: 0; }
        .meta { margin-bottom: 18px; font-size: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; font-size: 15px; }
        th, td { border: 1px solid #e3e3e3; padding: 10px; }
        th { background: #f7f7f7; color: #222; font-weight: 600; }
        tfoot td { font-weight: bold; background: #f7f7f7; }
        .summary { margin-top: 24px; font-size: 16px; }
        .payment-info { margin-top: 32px; font-size: 15px; color: #555; }
    </style>
</head>
<body>
    <div class="invoice-container" style="margin: 0 auto;">
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
            <tr>
                <td style="text-align:right; font-size:15px; color:#555; vertical-align:top;">
                    Stichting Lucide Inkt<br>
                    Kerspellaan 12<br>
                    7824 JG Emmen<br>
                    info@lucideinkt.nl<br>
                    KvK: 12345678<br>
                    BTW: NL123456789B01
                </td>
            </tr>
        </table>
        <h1>Factuur</h1>
        <div class="meta">
            <span><strong>Ordernummer:</strong> {{ $order->id }}</span><br>
            <span><strong>Datum:</strong> {{ $order->created_at->format('d-m-Y') }}</span>
        </div>
        <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:18px;">
            <tr>
                <td style="font-size:15px; background:#f7f7f7; padding:16px; width:50%; vertical-align:top;">
                    <strong style="color:#b30000;">{{ __('Factuuradres') }}</strong><br>
                    {{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
                    @if(!empty($order->customer->billing_company))
                        {{ $order->customer->billing_company }}<br>
                    @endif
                    {{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ !empty($order->customer->billing_house_number_addition) ? ' ' . $order->customer->billing_house_number_addition : '' }}<br>
                    {{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
                    {{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
                    @if(!empty($order->customer->billing_phone))
                        Tel: {{ $order->customer->billing_phone }}<br>
                    @endif
                    Email: {{ $order->customer->billing_email }}
                </td>
                <td style="font-size:15px; background:#f7f7f7; padding:16px; width:50%; vertical-align:top;">
                    <strong style="color:#b30000;">{{ __('Verzendadres') }}</strong><br>
                    @if(!empty($order->shipping_street))
                        {{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
                        @if(!empty($order->shipping_company))
                            {{ $order->shipping_company }}<br>
                        @endif
                        {{ $order->shipping_street }} {{ $order->shipping_house_number }}{{ !empty($order->shipping_house_number_addition) ? ' ' . $order->shipping_house_number_addition : '' }}<br>
                        {{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
                        {{ config('countries.' . $order->shipping_country) ?? $order->shipping_country }}<br>
                        @if(!empty($order->shipping_phone))
                            Tel: {{ $order->shipping_phone }}<br>
                        @endif
                    @else
                        {{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
                        @if(!empty($order->customer->billing_company))
                            {{ $order->customer->billing_company }}<br>
                        @endif
                        {{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ !empty($order->customer->billing_house_number_addition) ? ' ' . $order->customer->billing_house_number_addition : '' }}<br>
                        {{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
                        {{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
                        @if(!empty($order->customer->billing_phone))
                            Tel: {{ $order->customer->billing_phone }}<br>
                        @endif
                    @endif
                </td>
            </tr>
        </table>
        <table>
    <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Aantal</th>
                    <th>Stukprijs</th>
                    <th>Subtotaal</th>
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
                    <td colspan="3" style="text-align:right;">Totaal</td>
                    <td>€ {{ number_format($order->total, 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="summary">
                @if ($order->payment_status == 'paid' && !empty($order->paid_at))
                    <strong>Factuur is betaald</strong>
                @else
                    <strong>Te betalen:</strong> € {{ number_format($order->total, 2, ',', '.') }}<br>
            <span>Gelieve het bedrag over te maken naar rekening NL00BANK0123456789 t.n.v. Stichting Lucide Inkt o.v.v. uw ordernummer.</span>
                @endif
        </div>
        <div class="payment-info">
            Heeft u vragen over deze factuur? Neem gerust contact op via info@lucideinkt.nl.<br>
            Dank voor uw bestelling!
        </div>
    </div>
</body>
</html>