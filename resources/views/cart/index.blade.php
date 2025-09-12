<x-layout>
    <main class="container page cart">
        <h2>Winkelwagen</h2>

        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" style="position: relative;">
                {{ session('error') }}
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if ($errors->has('stock'))
            <div class="alert alert-error">
                <div>
                    {!! $errors->first('stock') !!}
                </div>
                <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if (count($cart))
            <div class="table-wrapper">
                <form action="{{ route('updateCart') }}" method="POST">
                    @csrf
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Afbeelding</th>
                            <th>Titel</th>
                            <th>Aantal</th>
                            <th>Exemplaar</th>
                            <th>Stukprijs</th>
                            <th>Totaal</th>
                            <th>Actie</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($cart as $index => $item)
                            <tr>
                                <td class="td-img" data-label="Afbeelding">
                                    @php
                                        $img = $item['image_1'] ?? '';
                                        if(!$img) {
                                            $prodModel = Product::find($item['product_id']);
                                            $img = $prodModel?->image_1;
                                        }
                                        $src = asset('images/placeholder.png');
                                        if ($img) {
                                            $clean = ltrim($img, '/');
                                            if (Str::startsWith($clean, ['http://','https://'])) {
                                                $src = $clean;
                                            } elseif (Str::startsWith($clean, ['images/','image/'])) {
                                                $src = asset($clean);
                                            } elseif (Str::startsWith($clean, 'storage/')) {
                                                $src = asset($clean);
                                            } else {
                                                $src = asset('storage/' . $clean);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $src }}" alt="{{ $item['name'] }}" style="max-width:60px;max-height:60px;object-fit:cover;">
                                </td>
                                <td data-label="Titel">{{ $item['name'] }}</td>
                                <td data-label="Aantal">
                                    <input type="number" name="products[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" min="1" max="1000" style="width:60px;">
                                    <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                                    <input type="hidden" name="products[{{ $index }}][product_copy_id]" value="{{ $item['product_copy_id'] }}">
                                </td>
                                <td data-label="Exemplaar">{{ $item['product_copy_name'] }}</td>
                                <td data-label="Stukprijs">€ {{ number_format($item['price'], 2, ',', '.') }}</td>
                                <td data-label="Totaal">€ {{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                                <td data-label="Actie">
                                    <button type="submit" class="btn small" form="delete-{{ $item['product_id'] }}-{{ $item['product_copy_id'] }}">Verwijderen</button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="total-cart-box">
                        <div>
                            @php
                                $total = 0;
                                foreach ($cart as $item) {
                                    $total += $item['price'] * $item['quantity'];
                                }
                            @endphp
                            <p><strong>Totaal: € {{ number_format($total, 2, ',', '.') }}</strong></p>
                        </div>

                        <div class="button-box">
                            <button class="btn update-qty" type="submit">
                                <span class="loader"></span>Winkelwagen bijwerken
                            </button>
                </form>

                <a href="{{ route('checkoutPage') }}">
                    <button type="button" class="btn checkout">Afrekenen</button>
                </a>

                <form action="{{ route('removeCart') }}" method="POST" class="needs-confirm"
                      data-confirm="Weet je zeker dat je de hele winkelwagen wilt legen?"
                      data-confirm-title="Bevestig legen">
                    @csrf
                    <button type="submit" class="btn delete">
                        <span class="loader"></span>Winkelwagen legen
                    </button>
                </form>
            </div>
            </div>
            </div>

            @foreach ($cart as $item)
                <form id="delete-{{ $item['product_id'] }}-{{ $item['product_copy_id'] }}"
                      action="{{ route('deleteItemFromCart') }}" method="POST" style="display:none;"
                      class="needs-confirm"
                      data-confirm="Weet je zeker dat je dit product uit je winkelwagen wilt verwijderen?"
                      data-confirm-title="Bevestig verwijderen">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                    <input type="hidden" name="product_copy_id" value="{{ $item['product_copy_id'] }}">
                </form>
            @endforeach
        @else
            <p>Winkelwagen is leeg</p>
        @endif
    </main>
</x-layout>
