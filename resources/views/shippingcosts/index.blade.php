<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Verzendkosten</h2>
        @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close"
                onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        @endif
        <a href="{{ route('shippingCostCreatePage') }}"><button class="btn">Nieuwe toevoegen</button></a>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Bedrag</th>
                        <th>Land</th>
                        <th>Gepubliceerd</th>
                        <th>Datum</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($shippingCosts as $shippingCost)
                    <tr>
                        <td>{{ $shippingCost->id }}</td>
                        <td style="min-width:120px;">&euro; {{ number_format($shippingCost->amount, 2, ',', '.') }}</td>
                        <td style="min-width:120px;">{{ $shippingCost->country }}</td>
                        <td style="min-width:90px;">
                            @if ($shippingCost->is_published == 1)
                            ja
                            @else
                            nee
                            @endif
                        </td>
                        <td style="min-width:110px;">{{ $shippingCost->created_at->format('d-m-Y') }}</td>
                        <td class="table-action" style="min-width:80px;">
                            <a href="{{ route('shippingCostEditPage', $shippingCost->id) }}"><i
                                    class="fa-regular fa-pen-to-square edit action-btn"></i></a>
                            <form action="{{ route('shippingCostDelete', $shippingCost->id) }}" method="POST" class="needs-confirm" data-confirm="Weet je zeker dat je deze verzendkosten wilt verwijderen?" data-confirm-title="Verzendkosten verwijderen">
                                @csrf
                                @method('DELETE')
                                <button style="background-color: transparent; border: none;padding: 0;" type="submit"><i
                                        class="fa-regular fa-trash-can delete action-btn"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 20px;">
                                Geen verzendkosten gevonden.
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
            {{ $shippingCosts->links('vendor.pagination.custom') }}
        </div>
    </main>
</x-dashboard-layout>
