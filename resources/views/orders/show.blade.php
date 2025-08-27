<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Bestelling #{{ $order->id }}</h2>
        @if(session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

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
                    <form action="{{ route('orderUpdate', $order->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <p>
                        <strong>Order Status:</strong> 
                        @php
                            $statuses = [
                                'pending'   => 'In afwachting',
                                'shipped'   => 'Verzonden',
                                'cancelled' => 'Geannuleerd',
                                'completed' => 'Afgerond',
                            ];
                        @endphp
                        <select style="width: fit-content" name="order-status">
                            @foreach ($statuses as $key => $label)
                                <option value="{{ $key }}" @if($order->status === $key) selected @endif>
                                    {{ $label }}
                                </option>
                                @error('order-status') <div class="error">{{ $message }}</div> @enderror
                            @endforeach
                        </select>
                    </p>
                    <p><strong>Totaal:</strong> € {{ number_format($order->total, 2) }}</p>
                    <p><strong>Betaalstatus:</strong> {{ $order->payment_status_label ?? 'Onbekend' }}</p>

                    @if ($order->payment_status !== 'paid' && $order->payment_link)
                        <p><strong>Betaallink:</strong> 
                            <div class="payment-link-box">

                            <button class="btn" id="payment-link">
                                <a style="color: #fff;" href="{{ $order->payment_link }}" target="_blank">Ga naar betaallink</a>
                            </button>
                            <button class="btn" id="copy-payment-link" data-payment-link="{{ $order->payment_link }}">
                                Kopieer betaallink
                            </button>
                            </div>
                        </p>
                    @endif
                    @if (!empty($order->invoice_pdf_path))
                        <p><strong>Factuur:</strong> 
                                <a style="text-decoration: underline" href="{{ route('orders.invoice', $order) }}" target="_blank">Download factuur</a>
                        </p>
                    @endif
                    <button class="btn" type="submit">Bestelling bijwerken</button>
                </form>
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

            @if ($order->items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $order->items->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>   

</x-dashboard-layout>
