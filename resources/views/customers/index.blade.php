<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Klanten</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif
    <div class="table-wrapper">
        <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Naam</th>
                <th>Datum geregistreerd</th>
                <th>E-mail</th>
								<th>Bestellingen</th>
								<th>Totaal uitgegeven</th>
								<th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($customers as $customer)
                <tr style="cursor: pointer;" onclick="window.location='{{ route('customerShow', $customer->id) }}'">
                    <td>{{ $customer->id }}</td>
                    <td>{{ $customer->billing_first_name }} {{ $customer->billing_last_name }}</td>
                    <td>{{ $customer->created_at->format('d-m-Y H:i') }}</td>
                    <td>{{ $customer->billing_email }}</td>
                    <td>{{ count($customer->orders) }}</td>
                    <td>€ {{ number_format($customer->orders->sum('total'), 2) }}</td>
                    <td class="table-action">
                        <a href="{{ route('customerShow', $customer->id) }}">
                            <i class="fas fa-eye show"></i>
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; padding: 20px;">
                        Geen klanten gevonden.
                    </td>
                </tr>
            @endforelse

        </tbody>
        </table>
        {{ $customers->links('vendor.pagination.custom') }}
    </div>

</main>
</x-dashboard-layout>