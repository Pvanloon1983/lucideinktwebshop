<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Alle producten</h2>
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
                <th>Afbeelding</th>
                <th>Titel</th>
                <th>Prijs</th>
                <th>Categorie</th>
                <th>Hoofdproduct</th>
                <th>Gepubliceerd</th>
                <th>Datum</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $product)
            <tr>
                <td>{{ $product->id }}</td>
                <td><img width="50px" src="{{ $product->image_1 }}" alt=""></td>
                <td>{{ $product->title }}</td>
                <td>{{ $product->price }}</td>
                <td>
                    @if(isset($product->categories) && count($product->categories))
                        {{ implode(', ', $product->categories->pluck('name')->toArray()) }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if($product->parent)
                        {{ $product->parent->title }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @if ($product->is_published == 1)
                        ja
                    @else
                        nee
                    @endif
                </td>
                <td>{{ $product->created_at->format('d-m-Y') }}</td>
            </tr>
            @empty
                
            @endforelse

        </tbody>
        </table>
        {{ $products->links('vendor.pagination.custom') }}
    </div>

</main>
</x-dashboard-layout>