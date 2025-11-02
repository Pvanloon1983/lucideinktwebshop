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
                                            $prodModel = \App\Models\Product::find($item['product_id']);
                                            $img = $prodModel?->image_1;
                                        }
                                        $src = asset('images/placeholder.png');
                                        if ($img) {
                                            $clean = ltrim($img, '/');
                                            if (str_starts_with($clean, 'http://') || str_starts_with($clean, 'https://')) {
                                                $src = $clean;
                                            } elseif (str_starts_with($clean, 'images/') || str_starts_with($clean, 'image/')) {
                                                $src = asset($clean);
                                            } elseif (str_starts_with($clean, 'storage/')) {
                                                $src = asset($clean);
                                            } else {
                                                $src = asset('storage/' . $clean);
                                            }
                                        }
                                    @endphp
                                    <img src="{{ $src }}" alt="{{ $item['name'] }}" style="max-width:60px;max-height:60px;object-fit:cover;">
                                </td>

                                @php
                                    $slug = $item['slug'] ?? null;
                                    if (!$slug) {
                                        $prod = \App\Models\Product::find($item['product_id']);
                                        $slug = $prod?->slug;
                                    }
                                    $productUrl = $slug ? url('/winkel/product/' . $slug) : url('/product/' . $item['product_id']);
                                @endphp
                                <td data-label="Titel"><a href="{{ $productUrl }}">{{ $item['name'] }}</a></td>

                                <td data-label="Aantal">
                                    <div class="qty-control" style="display:flex;align-items:center;gap:6px;">
                                        <button type="button" class="btn qty-decrease small" aria-label="Decrease quantity"
                                                onclick="(function(btn){const inp=btn.parentElement.querySelector('.qty-input'); const min=Number(inp.min)||0; if(Number(inp.value)>min){inp.stepDown(); inp.dispatchEvent(new Event('change'));}})(this)">
                                            &minus;
                                        </button>
                                        <input type="number" name="products[{{ $index }}][quantity]" value="{{ $item['quantity'] }}" min="0" max="1000" class="qty-input" style="width:60px;text-align:center;">
                                        <button type="button" class="btn qty-increase small" aria-label="Increase quantity"
                                                onclick="(function(btn){const inp=btn.parentElement.querySelector('.qty-input'); const max=Number(inp.max)||Infinity; if(Number(inp.value)<max){inp.stepUp(); inp.dispatchEvent(new Event('change'));}})(this)">
                                            +
                                        </button>
                                        <input type="hidden" name="products[{{ $index }}][product_id]" value="{{ $item['product_id'] }}">
                                    </div>
                                </td>
                                <td data-label="Stukprijs">€{{ number_format($item['price'], 2, ',', '.') }}</td>
                                <td data-label="Totaal">€{{ number_format($item['price'] * $item['quantity'], 2, ',', '.') }}</td>
                                <td data-label="Actie">
                                    <button style="background-color: #ab0f14" type="submit" class="btn small" form="delete-{{ $item['product_id'] }}">Verwijderen</button>
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
                            <p><strong>Totaal: €{{ number_format($total, 2, ',', '.') }}</strong></p>
                        </div>

                        <div class="button-box">
                            <button class="btn update-qty small" type="submit">
                                <span class="loader"></span>Winkelwagen bijwerken
                            </button>
                        </div>
                    </div>
                </form>

                <a href="{{ route('checkoutPage') }}">
                    <button type="button" class="btn checkout small">Afrekenen</button>
                </a>

                <form action="{{ route('removeCart') }}" style="margin-top: 10px" method="POST" class="needs-confirm"
                      data-confirm="Weet je zeker dat je de hele winkelwagen wilt legen?"
                      data-confirm-title="Bevestig legen">
                    @csrf
                    <button style="background-color: #ab0f14" type="submit" class="btn delete small">
                        <span class="loader"></span>Winkelwagen legen
                    </button>
                </form>
            </div>

            @foreach ($cart as $item)
                <form id="delete-{{ $item['product_id'] }}"
                      action="{{ route('deleteItemFromCart') }}" method="POST" style="display:none;"
                      class="needs-confirm"
                      data-confirm="Weet je zeker dat je dit product uit je winkelwagen wilt verwijderen?"
                      data-confirm-title="Bevestig verwijderen">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                </form>
            @endforeach
        @else
            <p>Winkelwagen is leeg</p>
        @endif
    </main>
</x-layout>
