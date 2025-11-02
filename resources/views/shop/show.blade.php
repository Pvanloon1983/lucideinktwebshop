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

            <x-breadcrumbs :items="[
              ['label' => 'Home', 'url' => route('home')],
              ['label' => 'Winkel', 'url' => route('shop')],
              ['label' => $product->title, 'url' => ''],
            ]" />

        <div class="single-product">
            @if ($product)

                @php
                    // Build an array of available product images (image_1..image_4)
                    $productImages = [];
                    for ($si = 1; $si <= 4; $si++) {
                        $f = 'image_' . $si;
                        if (!empty($product->$f)) {
                            if (str_starts_with($product->$f, 'http://') || str_starts_with($product->$f, 'https://')) {
                                $productImages[] = $product->$f;
                            } elseif (str_starts_with($product->$f, 'image/books/') || str_starts_with($product->$f, 'images/books/')) {
                                $productImages[] = asset($product->$f);
                            } else {
                                $productImages[] = asset('storage/' . $product->$f);
                            }
                        }
                    }
                @endphp

                @if(count($productImages) > 0)
                    <div class="image gallery">
                        @if(count($productImages) > 0)

                            <div id="main-slider" class="splide">
                                <div class="splide__track">
                                    <ul class="splide__list">
                                        @foreach($productImages as $idx => $img)
                                            <li class="splide__slide" style="display: flex; justify-content: center; height: auto">
                                                <a data-lightbox="books" href="{{ $img }}" data-title="{{ $product->title }}">
                                                <img data-lightbox="books" src="{{ $img }}" alt="{{ $product->title }} {{ $idx + 1 }}" loading="lazy">
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>

                            <ul id="thumbnails" class="thumbnails">
                                @foreach($productImages as $idx => $img)
                                    <li class="thumbnail">
                                        <img src="{{ $img }}" alt="{{ $product->title }} {{ $idx + 1 }}" loading="lazy">
                                    </li>
                                @endforeach
                            </ul>

                        @else
                            <p>Hoofdafbeelding ontbreekt</p>
                        @endif
                    </div>
                @else
                    <p>Hoofdafbeelding ontbreekt</p>
                @endif

                <div class="meta-data">

                    <h1 class="title">{{ $product->title }}</h1>
                    @if (isset($product->category) && !empty($product->category->name))
                        <p class="category">{{ $product->category->name }}</p>
                    @endif
                    <div id="exemplaar-info">
                        <p class="price">€{{ number_format($product->price, 2) }}</p>
                        <p class="long_description">
                            {{ $product->long_description }}
                        </p>
                        <div class="product-stock"></div>
                    </div>

                    <form action="{{ route('addToCart') }}" method="POST" id="addToCartForm">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <div class="form-input">
                            {{-- No exemplaar selection: single product add-to-cart --}}
                            <label for="quantity">Aantal</label>
                            <select name="quantity" id="quantity" style="width:70px" class="sf-select">
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" {{ (int)old('quantity', 1) === $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                            @error('quantity')
                            <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                        <button type="submit" class="add-to-cart-button btn">
                            <span class="loader"></span>Aan winkelmand toevoegen
                        </button>
                     </form>

                </div>
            @else
                <p>Geen product gevonden</p>
            @endif
        </div>
    </main>
</x-layout>
