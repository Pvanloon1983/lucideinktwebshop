<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Bestelling #{{ $order->id }}</h2>

        <div class="order-info">
            <div class="order-info-grid">
                <div class="order-info-item">
                    <h3>Klantgegevens</h3>
                    <p><strong>Naam:</strong> {{ $order->customer->billing_first_name }} {{
                        $order->customer->billing_last_name }}</p>
                    <p><strong>Email:</strong> {{ $order->customer->billing_email }}</p>
                    <p><strong>Telefoonnummer:</strong> {{ $order->customer->billing_phone ?? '-' }}</p>
                    <p><strong>Datum:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
                </div>
                <div class="order-info-item">
                    <h3>Bestelling</h3>
                    <p><strong>Status:</strong> {{ $order->status_label }}</p>
                    <p><strong>Totaal:</strong> € {{ number_format($order->total, 2) }}</p>
                    <p><strong>Betaalstatus:</strong> {{ $order->payment_status_label ?? 'Onbekend' }}</p>
                    @if (!empty($order->invoice_pdf_path))
                        <p><strong>Factuur:</strong> 
                                <a style="text-decoration: underline" href="{{ route('my_orders.invoice', $order->id) }}" target="_blank">Download factuur</a>
                        </p>
                    @endif
                </div>
                <div class="order-info-item">
                    <h3>Factuuradres</h3>
                    <p><strong>Straatnaam:</strong> {{ $order->customer->billing_street }}</p>
                    <p><strong>Huisnummer:</strong> {{ $order->customer->billing_house_number }}</p>
                    <p><strong>Huisnummer toevoeging:</strong> {{ $order->customer->billing_house_number_addition ?? '-'
                        }}</p>
                    <p><strong>Postcode:</strong> {{ $order->customer->billing_postal_code }}</p>
                    <p><strong>Plaats:</strong> {{ $order->customer->billing_city }}</p>
                    <p><strong>Land:</strong> {{ $order->customer->billing_country }}</p>

                </div>
                <div class="order-info-item">
                    <h3>Verzendadres</h3>
                    @if (!empty($order->shipping_street))
                    <p><strong>Straatnaam:</strong> {{ $order->shipping_street }}</p>
                    <p><strong>Huisnummer:</strong> {{ $order->shipping_house_number }}</p>
                    <p><strong>Huisnummer toevoeging:</strong> {{ $order->shipping_house_number_addition ?? '-' }}</p>
                    <p><strong>Postcode:</strong> {{ $order->shipping_postal_code }}</p>
                    <p><strong>Plaats:</strong> {{ $order->shipping_city }}</p>
                    <p><strong>Land:</strong> {{ $order->shipping_country }}</p>
                    @else
                    <p><strong>Straatnaam:</strong> {{ $order->customer->billing_street }}</p>
                    <p><strong>Huisnummer:</strong> {{ $order->customer->billing_house_number }}</p>
                    <p><strong>Huisnummer toevoeging:</strong> {{ $order->customer->billing_house_number_addition ?? '-' }}</p>
                    <p><strong>Postcode:</strong> {{ $order->customer->billing_postal_code }}</p>
                    <p><strong>Plaats:</strong> {{ $order->customer->billing_city }}</p>
                    <p><strong>Land:</strong> {{ $order->customer->billing_country }}</p>
                    @endif
                </div>
            </div>
        </div>
        @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
            <button type="button" class="alert-close"
                onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        @endif

        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Aantal</th>
                        <th>Stukprijs</th>
                        <th>Subtotaal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($order->items as $item)
                    <tr>
                        <td>{{ $item->product_name }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>€ {{ number_format($item->unit_price, 2) }}</td>
                        <td>€ {{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="table-empty-state">
                            Geen items gevonden in deze bestelling.
                        </td>
                    </tr>
                    @endforelse
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: bold;">Totaal</td>
                        <td style="font-weight: bold;">€ {{ number_format($order->total, 2) }}</td>
                    </tr>

                </tbody>
            </table>

            @if($order->items instanceof \Illuminate\Pagination\LengthAwarePaginator)
            {{ $order->items->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>
</x-dashboard-layout>