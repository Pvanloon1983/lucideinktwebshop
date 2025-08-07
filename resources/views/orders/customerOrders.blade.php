<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Mijn Bestellingen</h2>
        @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close"
                onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        @endif
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>Bestelling</th>
                        <th>Naam</th>
                        <th>Datum</th>
                        <th>Totaal</th>
                        <th>Status</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                    <tr style="cursor: pointer;" onclick="window.location='{{ route('showMyOrder', $order->id) }}'">
                        <td># {{ $order->id }}</td>
                        <td>{{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}</td>
                        <td>{{ $order->created_at->format('d-m-Y') }}</td>
                        <td>€ {{ number_format($order->total, 2) }}</td>
                        <td>{{ $order->status_label }}</td>
                        <td class="table-action">
                            <a href="{{ route('showMyOrder', $order->id) }}" class="action-btn show"
                                onclick="event.stopPropagation()">
                                <i class="fas fa-eye show"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">
                            Geen bestellingen gevonden.
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
            {{ $orders->links('vendor.pagination.custom') }}
        </div>

    </main>
</x-dashboard-layout>