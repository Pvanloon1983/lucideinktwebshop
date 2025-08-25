<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Factuur #{{ $order->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #b30000; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background: #f7f7f7; }
    </style>
</head>
<body>
    <h1>Factuur</h1>
    <p>
    Stichting Lucide Inkt<br>
    Kerspellaan 12<br>
    7824 JG<br>
    Emmen<br>    
    </p>
    <p>Ordernummer: {{ $order->id }}</p>
    <p>Datum: {{ $order->created_at->format('d-m-Y') }}</p>
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
    </table>
    <p><strong>Totaal:</strong> € {{ number_format($order->total, 2, ',', '.') }}</p>
</body>
</html>