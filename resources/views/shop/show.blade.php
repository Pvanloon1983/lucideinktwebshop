<x-layout>
    <main class="container page product">
        @if (session('success_add_to_cart'))
            <div class="alert alert-success" style="position: relative;">
                <div>
                    {{ session('success_add_to_cart') }} <a style="" href="{{ route('cartPage') }}"> Bekijk
                        winkelwagen</a>
                </div>
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

		@if($errors->has('stock'))
		<div class="alert alert-error">
			<div>
				{!! $errors->first('stock') !!}
			</div>
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
		</div>
		@endif

        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif
        <a href="{{ route('shop') }}" class="back-link">&larr; Terug naar winkel</a>
        <div class="single-product">
            @if ($product)
                <div class="image">
                    <img src="{{ e(
                        Str::startsWith($product->image_1, 'https://')
                            ? $product->image_1
                            : (Str::startsWith($product->image_1, 'image/books/')
                                ? asset($product->image_1)
                                : (Str::startsWith($product->image_1, 'images/books/')
                                    ? asset($product->image_1)
                                    : asset('storage/' . $product->image_1))),
                    ) }}"
                        alt="">
                </div>
                <div class="meta-data">

                    <h1 class="title">{{ $product->base_title ?? $product->title }}</h1>
                    @if (isset($product->category) && !empty($product->category->name))
                        <p class="category">{{ $product->category->name }}</p>
                    @endif
                    <div id="exemplaar-info">
                        @php
                            $baseTitleProducts = \App\Models\Product::where('base_title', $product->base_title)->get();
                        @endphp
                        <p class="price">
                            @if(isset($baseTitleProducts) && $baseTitleProducts->count() > 1)
                                €{{ number_format($baseTitleProducts->min('price'), 2) }} - €{{ number_format($baseTitleProducts->max('price'), 2) }}
                            @else
                                {{ number_format($product->price, 2) }}
                            @endif
                        </p>
                        <p class="long_description">
                            {{ $product->long_description }}
                        </p>
                        <div class="product-stock"></div>
                    </div>

                    <form action="{{ route('addToCart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <div class="form-input">
                            <select style="width: auto;display: block;" name="product_copy_id" id="product_copy_id" required>
                                <option value="">Kies een exemplaar</option>
                                @foreach ($productCopies as $i => $productCopy)
                                    <option value="{{ $productCopy->id }}" {{ $i === 0 ? 'selected' : '' }}>{{ $productCopy->name }}</option>
                                @endforeach
                            </select>
                            @error('product_copy_id')
                            <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="add-to-cart-button btn" id="addToCartBtn" disabled>
                            <span class="loader"></span>Aan winkelmand toevoegen
                        </button>
                    </form>

                </div>
            @else
                <p>Geen product gevonden</p>
            @endif
        </div>
    </main>
    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const select = document.getElementById('product_copy_id');
            const btn = document.getElementById('addToCartBtn');
            const exemplaarInfo = document.getElementById('exemplaar-info');
            const exemplaren = @json($exemplaren);
            const imageEl = document.querySelector('.single-product .image img');

            // Store original product info
            const original = {
                price: exemplaarInfo.querySelector('.price').innerHTML,
                long_description: exemplaarInfo.querySelector('.long_description').innerHTML,
                product_stock: exemplaarInfo.querySelector('.product-stock').innerHTML,
                image: imageEl.src
            };

            function updateExemplaarInfo(copyId) {
                if (!copyId) {
                    // Restore original info
                    exemplaarInfo.querySelector('.price').innerHTML = original.price;
                    exemplaarInfo.querySelector('.long_description').innerHTML = original.long_description;
                    exemplaarInfo.querySelector('.product-stock').innerHTML = original.product_stock;
                    imageEl.src = original.image;
                    btn.disabled = true;
                    return;
                }
                let ex = exemplaren.find(e => e.product_copy_id == copyId);
                if (ex) {
                    exemplaarInfo.querySelector('.price').innerHTML = '€' + ex.price;
                    exemplaarInfo.querySelector('.long_description').innerHTML = ex.long_description || '';
                    if (ex.stock == 0) {
                        exemplaarInfo.querySelector('.product-stock').innerHTML = '<p class="no-stock">Niet meer op voorraad</p>';
                        btn.disabled = true;
                    } else if (ex.stock > 0 && ex.stock <= 3) {
                        exemplaarInfo.querySelector('.product-stock').innerHTML = '<p class="low-stock">Nog maar ' + ex.stock + ' op voorraad</p>';
                        btn.disabled = false;
                    } else {
                        exemplaarInfo.querySelector('.product-stock').innerHTML = '';
                        btn.disabled = false;
                    }
                    // Dynamically update image
                    if (ex.image_1) {
                        let imgSrc = ex.image_1;
                        if (!imgSrc.startsWith('https://')) {
                            if (imgSrc.startsWith('image/books/')) {
                                imgSrc = '{{ asset('') }}' + imgSrc;
                            } else if (imgSrc.startsWith('images/books/')) {
                                imgSrc = '{{ asset('') }}' + imgSrc;
                            } else {
                                imgSrc = '{{ asset('storage/') }}/' + imgSrc;
                            }
                        }
                        imageEl.src = imgSrc;
                    }
                }
            }

            // On page load, show original info and disable button
            select.selectedIndex = 0;
            updateExemplaarInfo(select.value);

            select.addEventListener('change', function() {
                updateExemplaarInfo(this.value);
            });
        });


    </script>
</x-layout>
