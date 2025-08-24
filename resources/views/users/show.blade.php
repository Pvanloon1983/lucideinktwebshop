<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Gebruiker #{{ $user->id }}</h2>
        @if(session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        <div class="order-info">
            <div class="order-info-grid">
                <div class="order-info-item">
                    <h3>Persoonlijke gegevens</h3>
                <form action="{{ route('userEdit', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <p><strong>Naam:</strong> {{ $user->first_name }} {{ $user->last_name }}</p>
                    <p><strong>Email:</strong> {{ $user->email }}</p>
                    <p><strong>Geregistreerd op:</strong> {{ $user->created_at->format('d-m-Y H:i') }}</p>
                    <p><strong>Rol:</strong> 
                        @php
                            $userRoles = [
                                'admin' => 'Admin',
                                'user' => 'Gebruiker',
                            ];
                        @endphp
                        <select style="width: fit-content;" name="user_role">
                            @if (!empty($userRoles))
                                @foreach ($userRoles as $key => $label)
                                    <option value="{{ $key }}" @if($user->role === $key) selected @endif>{{ $label }}</option>
                                @endforeach                                
                            @endif                          
                        </select>
                    </p>
                    <button style="margin-top: 10px" class="btn" type="submit">Gebruiker bijwerken</button>
                </form>
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
                            <tr style="cursor: pointer;" onclick="window.location='{{ route('orderShow', $order->id) }}'">
                                <td>#{{ $order->id }}</td>
                                <td>{{ $order->created_at->format('d-m-Y') }}</td>
                                <td>{{ $order->status_label }}</td>
                                <td>€ {{ number_format($order->total, 2) }}</td>
                                <td>
                                    <a href="{{ route('orderShow', $order->id) }}" class="action-btn show" onclick="event.stopPropagation()">
                                        <i class="fas fa-eye show"></i>
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
