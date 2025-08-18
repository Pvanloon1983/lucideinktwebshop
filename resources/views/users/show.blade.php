<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Gebruiker #{{ $user->id }}</h2>

        <div class="order-info">
            <div class="order-info-grid">
                <div class="order-info-item">
                    <h3>Persoonlijke gegevens</h3>
                    <p><strong>Naam:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Geregistreerd op:</strong> {{ $user->created_at->format('d-m-Y H:i') }}</p>
                    <p><strong>Rol:</strong> {{ $user->role }}</p>
                </div>
                @if (!empty($customer->billing_street))
                    <div class="order-info-item">
                        <h3>Factuuradres</h3>
                        <p><strong>Straatnaam:</strong> {{ $customer->billing_street }}</p>
                        <p><strong>Huisnummer:</strong> {{ $customer->billing_house_number }}</p>
                        <p><strong>Toevoeging:</strong> {{ $customer->billing_house_number_addition ?? '-' }}</p>
                        <p><strong>Postcode:</strong> {{ $customer->billing_postal_code }}</p>
                        <p><strong>Plaats:</strong> {{ $customer->billing_city }}</p>
                        <p><strong>Land:</strong> {{ $customer->billing_country }}</p>
                    </div>
                    <div style="width: 100%;" class="order-info-item">
                        <h3>Bestellingen</h3>
                        <p><strong>Aantal bestellingen:</strong> {{ $customer->orders->count() }}</p>
                        <p><strong>Totaal uitgegeven:</strong> €
                            {{ number_format($customer->orders->sum('total'), 2) }}</p>
                    </div>
                @endif
            </div>
        </div>

        <div class="table-wrapper" style="margin-top: 30px;">
            <h3>Ordergeschiedenis</h3>
            <table class="table">
                <thead>
                    <tr>
                        <th>Bestelling</th>
                        <th>Datum</th>
                        <th>Status</th>
                        <th>Totaal</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @if (!empty($customer->orders))
                        @foreach ($customer->orders as $order)
                            <tr>
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->status_label }}</td>
                                <td>€ {{ number_format($order->total, 2) }}</td>
                                <td>
                                    <a href="{{ route('orderShow', $order->id) }}" class="action-btn show">
                                        <i class="fas fa-eye"></i> Bekijken
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="5" class="table-empty-state">Geen bestellingen gevonden.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </main>
</x-dashboard-layout>
