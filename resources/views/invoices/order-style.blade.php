<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur #{{ $order['id'] }}</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #fafbfc; color: #222; margin: 0; padding: 0; }
        .invoice-container { max-width: 700px; margin: 30px auto; background: #fff; box-shadow: 0 2px 8px #eee; border-radius: 8px; padding: 32px; }
        .header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 32px; }
        .logo { height: 48px; }
        .company-info { font-size: 15px; color: #555; }
        h1 { color: #b30000; font-size: 2.1em; margin-bottom: 0; }
        .address-blocks { display: flex; flex-wrap: wrap; gap: 30px; margin-bottom: 18px; }
        .address { min-width: 220px; flex: 1; font-size: 15px; background: #f7f7f7; border-radius: 6px; padding: 16px; }
        .address strong { font-size: 1.08em; color: #b30000; }
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
    <div class="invoice-container">
        <div class="header">
            <div>
                <img src="{{ public_path('images/Logo_Lucide_Inkt.png') }}" alt="Lucide Inkt" class="logo" onerror="this.style.display='none'">
            </div>
            <div class="company-info">
                Stichting Lucide Inkt<br>
                Kerspellaan 12<br>
                7824 JG Emmen<br>
                info@lucideinkt.nl<br>
                KvK: 54486890<br>
            </div>
        </div>
        <h1>Factuur</h1>
        <div class="meta">
            <span><strong>Ordernummer:</strong> {{ $order['id'] }}</span><br>
            <span><strong>Datum:</strong> {{ $order['created_at'] ?? date('d-m-Y') }}</span>
        </div>
        <div class="address-blocks">
            <div class="address">
                <strong>{{ __('Factuuradres') }}</strong><br>
                {{ $order['customer']['billing_first_name'] }} {{ $order['customer']['billing_last_name'] }}<br>
                @if(!empty($order['customer']['billing_company']))
                    {{ $order['customer']['billing_company'] }}<br>
                @endif
                {{ $order['customer']['billing_street'] }} {{ $order['customer']['billing_house_number'] }}{{ !empty($order['customer']['billing_house_number_addition']) ? ' ' . $order['customer']['billing_house_number_addition'] : '' }}<br>
                {{ $order['customer']['billing_postal_code'] }} {{ $order['customer']['billing_city'] }}<br>
                {{ config('countries.' . $order['customer']['billing_country']) ?? $order['customer']['billing_country'] }}<br>
                @if(!empty($order['customer']['billing_phone']))
                    Tel: {{ $order['customer']['billing_phone'] }}<br>
                @endif
                Email: {{ $order['customer']['billing_email'] }}
            </div>
            <div class="address">
                <strong>{{ __('Verzendadres') }}</strong><br>
                @if(!empty($order['shipping_street']))
                    {{ $order['shipping_first_name'] }} {{ $order['shipping_last_name'] }}<br>
                    @if(!empty($order['shipping_company']))
                        {{ $order['shipping_company'] }}<br>
                    @endif
                    {{ $order['shipping_street'] }} {{ $order['shipping_house_number'] }}{{ !empty($order['shipping_house_number_addition']) ? ' ' . $order['shipping_house_number_addition'] : '' }}<br>
                    {{ $order['shipping_postal_code'] }} {{ $order['shipping_city'] }}<br>
                    {{ config('countries.' . $order['shipping_country']) ?? $order['shipping_country'] }}<br>
                    @if(!empty($order['shipping_phone']))
                        Tel: {{ $order['shipping_phone'] }}<br>
                    @endif
                @else
                    {{ $order['customer']['billing_first_name'] }} {{ $order['customer']['billing_last_name'] }}<br>
                    @if(!empty($order['customer']['billing_company']))
                        {{ $order['customer']['billing_company'] }}<br>
                    @endif
                    {{ $order['customer']['billing_street'] }} {{ $order['customer']['billing_house_number'] }}{{ !empty($order['customer']['billing_house_number_addition']) ? ' ' . $order['customer']['billing_house_number_addition'] : '' }}<br>
                    {{ $order['customer']['billing_postal_code'] }} {{ $order['customer']['billing_city'] }}<br>
                    {{ config('countries.' . $order['customer']['billing_country']) ?? $order['customer']['billing_country'] }}<br>
                    @if(!empty($order['customer']['billing_phone']))
                        Tel: {{ $order['customer']['billing_phone'] }}<br>
                    @endif
                @endif
            </div>
        </div>
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
                @foreach($order['items'] as $item)
                <tr>
                    <td>{{ $item['product_name'] }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>€ {{ number_format($item['unit_price'], 2, ',', '.') }}</td>
                    <td>€ {{ number_format($item['subtotal'], 2, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">Totaal</td>
                    <td>€ {{ number_format($order['total'], 2, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        <div class="summary">
            <strong>Te betalen:</strong> € {{ number_format($order['total'], 2, ',', '.') }}<br>
            <span>Gelieve het bedrag over te maken naar rekening NL00BANK0123456789 t.n.v. Stichting Lucide Inkt o.v.v. uw ordernummer.</span>
        </div>
        <div class="payment-info">
            Heeft u vragen over deze factuur? Neem gerust contact op via info@lucideinkt.nl.<br>
            Dank voor uw bestelling!
        </div>
    </div>
</body>
</html>