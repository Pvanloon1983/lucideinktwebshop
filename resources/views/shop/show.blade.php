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
                    <h1 class="title">{{ $product->title }}</h1>
                    @if (isset($product->category) && !empty($product->category->name))
                        <p class="category">{{ $product->category->name }}</p>
                    @endif

                    @if (isset($product->price) && !empty($product->price))
                        <p class="price">€ {{ $product->price }}</p>
                    @endif

                    @if (isset($product->short_description) && !empty($product->short_description))
                        <p class="short_description">{{ $product->short_description }}</p>
                    @endif

                    <div class="product-stock">
                        @if ($product->stock > 0 && $product->stock <= 3)
                            <p class="low-stock">Nog maar {{ $product->stock }} op voorraad</p>
                        @elseif ($product->stock == 0)
                            <p class="no-stock">Niet meer op voorraad</p>
                        @endif
                    </div>

                    @if ($product->stock > 0)
                        <form action="{{ route('addToCart') }}" method="POST">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="add-to-cart-button btn"><span class="loader"></span>Aan
                                winkelmand toevoegen</button>
                        </form>
                    @endif

                </div>
            @else
                <p>Geen product gevonden</p>
            @endif
        </div>
    </main>
</x-layout>
