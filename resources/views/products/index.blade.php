<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Producten</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif
    <a href="{{ route('productCreatePage') }}"><button class="btn">Nieuwe toevoegen</button></a>
    <div class="table-wrapper">
        <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Afbeelding</th>
                <th>Titel</th>
                <th>Slug</th>
                <th>Prijs</th>
                <th>Voorraad</th>
                <th>Categorieën</th>
                <th>Hoofdproduct</th>
                <th>Gepubliceerd</th>
                <th>Datum</th>
                <th>Actie</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
                <tr>
                    <td style="min-width:40px;">{{ $product->id }}</td>
                    <td class="td-img">
                        <img src="{{ e(
                            Str::startsWith($product->image_1, 'https://')
                                ? $product->image_1
                                : (Str::startsWith($product->image_1, 'image/books/')
                                    ? asset($product->image_1)
                                    : (Str::startsWith($product->image_1, 'images/books/')
                                        ? asset($product->image_1)
                                        : asset('storage/' . $product->image_1)
                                    )
                                )
                        ) }}" alt="">
                    </td>
                    <td style="min-width:180px;">{{ $product->title }}</td>
                    <td style="min-width:160px;">{{ $product->slug }}</td>
                    <td style="min-width:70px;">{{ $product->price }}</td>
                    <td style="min-width:70px;">{{ $product->stock }}</td>
                    <td style="min-width:180px;">
                        {{ $product->category?->name ?? '-' }}
                    </td>
                    <td style="min-width:120px;">
                        @if($product->parent)
                            {{ $product->parent->title }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="min-width:90px;">
                        @if ($product->is_published == 1)
                            ja
                        @else
                            nee
                        @endif
                    </td>
                    <td style="min-width:110px;">{{ $product->created_at->format('d-m-Y H:i') }}</td>
                    <td class="table-action" style="min-width:100px;">
                        <a href="{{ route('productEditPage', $product->id) }}"><i class="fa-regular fa-pen-to-square edit action-btn"></i></a>
                        <form action="{{ route('productDelete', $product->id) }}" method="POST" class="needs-confirm" data-confirm="Weet je zeker dat je dit product wilt verwijderen?" data-confirm-title="Product verwijderen">
                            @csrf
                            @method('DELETE')
                            <button style="background-color: transparent; border: none;padding: 0;" type="submit"><i class="fa-regular fa-trash-can delete action-btn"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                
            @endforelse

        </tbody>
        </table>
        {{ $products->links('vendor.pagination.custom') }}
    </div>

</main>
</x-dashboard-layout>