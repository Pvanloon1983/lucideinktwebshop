<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Bestelling #{{ $order->id }}</h2>
        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
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

                    @if (empty($order->invoice_pdf_path))
                        <div>
                            <form action="{{ route('generateInvoice', $order->id) }}" method="POST">
                                @csrf
                                <button class="btn small" type="submit"><span class="loader"></span>Genereer Factuur</button>
                            </form>
                        </div>
                    @endif

                    @if (!empty($order->invoice_pdf_path))
                        <div>
                            <form action="{{ route('sendOrderEmailWithInvoice', $order->id) }}" method="POST">
                                @csrf
                                <button class="btn small" type="submit"><span class="loader"></span>Verstuur E-mail met factuur</button>
                            </form>
                        </div>
                    @endif
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
                                    'pending' => 'In afwachting',
                                    'shipped' => 'Verzonden',
                                    'cancelled' => 'Geannuleerd',
                                    'completed' => 'Afgerond',
                                ];
                            @endphp
                            <select style="width: fit-content" name="order-status">
                                @foreach ($statuses as $key => $label)
                                    <option value="{{ $key }}"
                                        @if ($order->status === $key) selected @endif>
                                        {{ $label }}
                                    </option>
                                    @error('order-status')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                @endforeach
                            </select>
                        </p>

                        <p><strong>Betaalstatus:</strong> {{ $order->payment_status_label ?? 'Onbekend' }}</p>

                        @if (!empty($order->invoice_pdf_path))
                            <p><strong>Factuur:</strong>
                                <a style="text-decoration: underline" href="{{ route('orders.invoice', $order->id) }}"
                                    target="_blank">Download factuur</a>
                            </p>
                        @endif

                        @if ($order->payment_status !== 'paid' && $order->payment_link)
                            <strong>Betaallink:</strong>
                            <div class="payment-link-box">

                                <button class="btn small" id="payment-link">
                                    <a style="color: #fff;" href="{{ $order->payment_link }}" target="_blank">Ga naar
                                        betaallink</a>
                                </button>
                                <button class="btn small" id="copy-payment-link"
                                    data-payment-link="{{ $order->payment_link }}">
                                    Kopieer betaallink
                                </button>
                            </div>
                        @endif
                        <button class="btn small" type="submit"><span class="loader"></span>Bestelling bijwerken</button>
                    </form>
                </div>

                <div class="order-info-item">
                    <h3>Verzendinformatie (MyParcel)</h3>
                    @if ($order->myparcel_consignment_id)
                        <div class="shipment-info-block">
                            <p><strong>Consignment ID:</strong> {{ $order->myparcel_consignment_id }}</p>
                            <p><strong>Carrier:</strong> {{ ucfirst($order->myparcel_carrier ?? 'PostNL') }}</p>

                            {{-- Track & Trace met barcode --}}
                            <p><strong>Track & Trace:</strong>
                                @if ($order->myparcel_track_trace_url)
                                    <a style="text-decoration: underline" href="{{ $order->myparcel_track_trace_url }}" target="_blank">
                                        {{ $order->myparcel_barcode ?? 'Bekijk zending' }}
                                    </a>
                                @else
                                    -
                                @endif
                            </p>

                            {{-- Pakket type --}}
                            @php
                                $types = [
                                    1 => 'Pakket',
                                    2 => 'Brievenbuspakje',
                                    3 => 'Brief',
                                    4 => 'Digitale postzegel',
                                ];
                            @endphp
                            <p><strong>Pakket type:</strong>
                                {{ $types[$order->myparcel_package_type_id] ?? $order->myparcel_package_type_id }}
                            </p>


                            @if ($order->myparcel_delivery_type)
                                {{-- Bezorgtype --}}
                                @php
                                    $deliveryTypesShort = [
                                        'standard' => 'Thuisbezorging',
                                        'standard' => 'Thuisbezorging',
                                        'standard' => 'Thuisbezorging',
                                        'pickup' => 'Afhaalpunt',
                                        'pickup' => 'Afhaalpunt (express)',
                                    ];
                                @endphp
                                <p>
                                    <strong>Gekozen bezorgtype:</strong>
                                    {{ $deliveryTypesShort[$order->myparcel_delivery_type] ?? ucfirst($order->myparcel_delivery_type ?? '-') }}
                                </p>
                            @endif

                            {{-- Label link of knop om label aan te maken --}}
                            @if ($order->myparcel_label_link)
                                <p><strong>Label:</strong>
                                    <a href="{{ $order->myparcel_label_link }}" target="_blank">Download label
                                        (PDF)</a>
                                </p>
                            @else
                            {{-- Form om pakket type aan te passen --}}
                            <form action="{{ route('orderUpdatePackageType', $order->id) }}" method="POST"
                                style="margin-top: 10px;">
                                @csrf
                                <select name="package_type" style="width: fit-content">
                                    @foreach ($types as $key => $label)
                                        <option value="{{ $key }}"
                                            @if ($order->myparcel_package_type_id == $key) selected @endif>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('package_type')
                                    <div class="error">{{ $message }}</div>
                                @enderror
                                <button class="btn small" type="submit" style="margin-left: 10px;"><span class="loader"></span>Update pakket type</button>
                            </form>

                                <form action="{{ route('orderGenerateLabel', $order->id) }}" method="POST"
                                    style="display:inline-block; margin-bottom: 10px;margin-top: 20px;">
                                    @csrf
                                    <button class="btn small btn-primary" type="submit"><span class="loader"></span>Label aanmaken bij MyParcel</button>
                                </form>
                            @endif
                        </div>
                    @else
                        <p>Geen MyParcel zending gekoppeld aan deze bestelling.</p>
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
                </tbody>
            </table>

            @if ($order->items instanceof \Illuminate\Pagination\LengthAwarePaginator)
                {{ $order->items->links('vendor.pagination.custom') }}
            @endif
        </div>

    </main>

</x-dashboard-layout>
