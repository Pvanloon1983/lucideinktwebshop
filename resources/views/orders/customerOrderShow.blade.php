<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Bestelling #{{ $order->id }}</h2>

        <div class="order-info">
            <div class="order-info-grid">
                <div class="order-info-item">
                    <h3>Klantgegevens</h3>
                    <p><strong>Naam:</strong> {{ $order->customer->billing_first_name }}
                        {{ $order->customer->billing_last_name }}</p>
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
                            <a style="text-decoration: underline" href="{{ route('orders.invoice', $order->id) }}"
                                target="_blank">Download factuur</a>
                        </p>
                    @endif
                    <p><strong>Track & Trace:</strong>
                        @if ($order->myparcel_track_trace_url)
                            <a style="text-decoration: underline" href="{{ $order->myparcel_track_trace_url }}" target="_blank">
                                {{ $order->myparcel_barcode ?? 'Bekijk zending' }}
                            </a>
                        @else
                            -
                        @endif
                    </p>

                    @if ($order->myparcel_delivery_type)
                        {{-- Bezorgtype --}}
                        @php
                            $deliveryTypesShort = [
                                'standard' => 'Thuisbezorging',
                                'standard' => 'Thuisbezorging',
                                'standard' => 'Thuisbezorging',
                                'pickup' => 'Afhaalpunt',
                                'pickup' => 'Afhaalpunt',
                            ];
                        @endphp
                        <p>
                            <strong>Gekozen bezorgtype:</strong>
                            {{ $deliveryTypesShort[$order->myparcel_delivery_type] ?? ucfirst($order->myparcel_delivery_type ?? '-') }}
                        </p>
                        @if ($order->myparcel_delivery_type == 'pickup')
                            <p><strong>Adres afhaalpunt:</strong></p>
                            <span>{{ $pickupLocation['locationName'] ?? '-' }}</span>,
                            <span>{{ $pickupLocation['street'] ?? '' }} {{ $pickupLocation['number'] ?? '' }}</span>,
                            <span>{{ $pickupLocation['postalCode'] ?? '' }}</span>,
                            <span>{{ $pickupLocation['city'] ?? '' }}</span>
                        @endif
                    @endif
                </div>
                <div class="order-info-item">
                    <h3>Factuuradres</h3>
                    <p><strong>Straatnaam:</strong> {{ $order->customer->billing_street }}</p>
                    <p><strong>Huisnummer:</strong> {{ $order->customer->billing_house_number }}</p>
                    <p><strong>Huisnummer toevoeging:</strong>
                        {{ $order->customer->billing_house_number_addition ?? '-' }}</p>
                    <p><strong>Postcode:</strong> {{ $order->customer->billing_postal_code }}</p>
                    <p><strong>Plaats:</strong> {{ $order->customer->billing_city }}</p>
                    <p><strong>Land:</strong> {{ $order->customer->billing_country }}</p>

                </div>
                <div class="order-info-item">
                    <h3>Verzendadres</h3>
                    @if (!empty($order->shipping_street))
                        <p><strong>Straatnaam:</strong> {{ $order->shipping_street }}</p>
                        <p><strong>Huisnummer:</strong> {{ $order->shipping_house_number }}</p>
                        <p><strong>Huisnummer toevoeging:</strong> {{ $order->shipping_house_number_addition ?? '-' }}
                        </p>
                        <p><strong>Postcode:</strong> {{ $order->shipping_postal_code }}</p>
                        <p><strong>Plaats:</strong> {{ $order->shipping_city }}</p>
                        <p><strong>Land:</strong> {{ $order->shipping_country }}</p>
                    @else
                        <p><strong>Straatnaam:</strong> {{ $order->customer->billing_street }}</p>
                        <p><strong>Huisnummer:</strong> {{ $order->customer->billing_house_number }}</p>
                        <p><strong>Huisnummer toevoeging:</strong>
                            {{ $order->customer->billing_house_number_addition ?? '-' }}</p>
                        <p><strong>Postcode:</strong> {{ $order->customer->billing_postal_code }}</p>
                        <p><strong>Plaats:</strong> {{ $order->customer->billing_city }}</p>
                        <p><strong>Land:</strong> {{ $order->customer->billing_country }}</p>
                    @endif
                </div>
            </div>
        </div>
        @if (session('success'))
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
                        <td style="font-weight: bold;">€ {{ number_format($order->total_before, 2) }}</td>
                    </tr>
                                        @if ($order->discount_value > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Korting
                                ({{ $order->discount_type == 'percent' ? (int) $order->discount_value . '%' : '€ ' . number_format($order->discount_value, 2) }})
                            </td>
                            <td style="font-weight: bold;">-€ {{ number_format($order->discount_price_total, 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Totaal na korting</td>
                            <td style="font-weight: bold;">€ {{ number_format($order->total_after_discount, 2) }}</td>
                        </tr>
                    @endif
                    @if (!empty($order->shipping_cost_amount) && $order->shipping_cost_amount > 0)
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Verzendkosten</td>
                            <td style="font-weight: bold;">€ {{ is_numeric($order->shipping_cost_amount) ? number_format($order->shipping_cost_amount, 2) : number_format((float)($order->shipping_cost_amount->amount ?? 0), 2) }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" style="text-align: right; font-weight: bold;">Totaal incl. verzendkosten</td>
                            <td style="font-weight: bold;">€ {{ number_format($order->total, 2) }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            @if ($order->items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $order->items->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>
</x-dashboard-layout>
