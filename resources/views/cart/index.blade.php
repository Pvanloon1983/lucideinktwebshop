<x-layout>
    <main class="container page cart">
        <h2>Winkelwagen</h2>
        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" style="position: relative;">
                {{ session('error') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if ($errors->has('stock'))
            <div class="alert alert-error">
                <div>
                    {!! $errors->first('stock') !!}
                </div>
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
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

                            @foreach ($cart as $item)
                                <tr>
                                    <td class="td-img" data-label="Afbeelding">
                                        <img src="{{ e(
                                            Str::startsWith($item['image_1'], 'https://')
                                                ? $item['image_1']
                                                : (Str::startsWith($item['image_1'], 'image/books/')
                                                    ? asset($item['image_1'])
                                                    : (Str::startsWith($item['image_1'], 'images/books/')
                                                        ? asset($item['image_1'])
                                                        : asset('storage/' . $item['image_1']))),
                                        ) }}"
                                            alt="">
                                    </td>
                                    <td class="td-title" data-label="Titel">{{ $item['name'] }}</td>
                                    <td class="td-quantity" style="text-align: left;" data-label="Aantal">
                                        <input type="hidden" name="products[{{ $loop->index }}][product_id]"
                                            value="{{ $item['product_id'] }}">
                                        <div class="update-cart"
                                            style="display: flex; align-items: center; justify-content: flex-start;">
                                            <input type="number" name="products[{{ $loop->index }}][quantity]"
                                                value="{{ $item['quantity'] }}" min="1" style="width: 70px;">
                                        </div>
                                    </td>
                                    <td style="min-width:80px;" data-label="Stukprijs">€ {{ $item['price'] }}</td>
                                    <td style="min-width:80px;" data-label="Totaal">€
                                        {{ $item['quantity'] * $item['price'] }}</td>
                                    <td style="min-width:80px;" data-label="Actie">
                                        <button type="submit" class="btn small"
                                            form="delete-{{ $item['product_id'] }}">
                                            Verwijderen
                                        </button>
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
                         <button class="btn update-qty" type="submit"><span class="loader"></span>Winkelwagen bijwerken</button>
                </form>

                <a href="{{ route('checkoutPage') }}">
                    <button type="button" class="btn checkout">Afrekenen</button>
                </a>
                <form action="{{ route('removeCart') }}" method="POST" class="empty-cart-btn needs-confirm"
      data-confirm="Weet je zeker dat je de hele winkelwagen wilt legen?" data-confirm-title="Bevestig legen">
                    @csrf
                    <button type="submit" class="btn delete"><span class="loader"></span>Winkelwagen legen</button>
                </form>
            </div>

            @foreach ($cart as $item)
                <form id="delete-{{ $item['product_id'] }}" action="{{ route('deleteItemFromCart') }}" method="POST"
                    style="display:none;" class="delete-cart-item needs-confirm"
        data-confirm="Weet je zeker dat je dit product uit je winkelwagen wilt verwijderen?" data-confirm-title="Bevestig verwijderen">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
                </form>
            @endforeach

            </div>
            </div>
        @else
            <p>Winkelwagen is leeg</p>
        @endif
    </main>
</x-layout>
