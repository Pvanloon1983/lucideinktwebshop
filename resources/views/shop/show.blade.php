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
                        <p class="price">&nbsp;</p>
                        <p class="short_description">&nbsp;</p>
                        <div class="product-stock">&nbsp;</div>
                    </div>

                    <form action="{{ route('addToCart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="quantity" value="1">
                        <div class="form-input">
                            <label for="product_copy_id">Exemplaar</label>
                            <select style="width: auto;display: block;" name="product_copy_id" id="product_copy_id" required>
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

            function updateExemplaarInfo(copyId) {
                let info = {price: '', short_description: '', stock: '', image_1: ''};
                let ex = exemplaren.find(e => e.product_copy_id == copyId);
                if (ex) {
                    info.price = '€ ' + ex.price;
                    info.short_description = ex.short_description || '';
                    if (ex.stock == 0) {
                        info.stock = '<p class="no-stock">Niet meer op voorraad</p>';
                        btn.disabled = true;
                    } else if (ex.stock > 0 && ex.stock <= 3) {
                        info.stock = '<p class="low-stock">Nog maar ' + ex.stock + ' op voorraad</p>';
                        btn.disabled = false;
                    } else {
                        info.stock = '';
                        btn.disabled = false;
                    }
                    // Dynamisch afbeelding
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
                exemplaarInfo.querySelector('.price').innerHTML = info.price;
                exemplaarInfo.querySelector('.short_description').innerHTML = info.short_description;
                exemplaarInfo.querySelector('.product-stock').innerHTML = info.stock;
            }

            // Initieel: selecteer eerste exemplaar
            if (select.options.length > 0) {
                select.selectedIndex = 0;
                updateExemplaarInfo(select.value);
            }

            select.addEventListener('change', function() {
                updateExemplaarInfo(select.value);
            });
        });
    </script>
</x-layout>
