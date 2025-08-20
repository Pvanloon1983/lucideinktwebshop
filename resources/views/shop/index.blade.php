<x-layout>
    <main class="container page shop">
        {{-- <h2>Winkel</h2> --}}
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
        <div class="book-box">
            @foreach ($products as $product)
                <a href="{{ route('productShow', $product->slug) }}">
                    <div class="card">

                        <div class="product-stock">
                            @if ($product->stock > 0 && $product->stock <= 3)
                                <p class="low-stock">Lage voorraad</p>
                            @elseif ($product->stock == 0)
                                <p class="no-stock">Geen voorraad</p>
                            @endif
                        </div>

                        <div class="image-container">
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

                        <div class="title-price">
                            <h6 class="title">{{ $product->title }}</h6>
                            @if (isset($product->price) && !empty($product->category->name))
                                <p class="price">€ {{ $product->price }}</p>
                            @endif
                        </div>

                        {{-- <div class="product-stock">
					@if ($product->stock > 0 && $product->stock <= 3)
						<p class="low-stock">Nog maar {{$product->stock}} op voorraad</p>
					@elseif ($product->stock == 0)
						<p class="no-stock">Niet meer op voorraad</p>
					@endif
				</div> --}}

                        {{-- @if (isset($product->category) && !empty($product->category->name))
					<p class="category">{{ $product->category->name }}</p>
				@endif --}}
                    </div>
                </a>
            @endforeach
        </div>
    </main>
</x-layout>
