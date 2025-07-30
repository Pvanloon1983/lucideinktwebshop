<x-layout>
<main class="container page product">
			@if(session('success'))
			<div class="alert alert-success" style="position: relative;">
					{{ session('success') }}
					<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
			</div>
			@endif
		<div class="single-product">
			@if ($product)
				<div class="image">
					<a href="{{ route('shop') }}" class="back-link">&larr; Terug naar winkel</a>
					<img src="{{ asset('/storage/' . $product->image_1) }}" alt="">
				</div>
				<div class="meta-data">
					<h1 class="title">{{ $product->title }}</h1>
					<p class="category">{{ $product->category->name }}</p>
					<p class="price">{{ $product->price }}</p>
					<p class="short_description">{{ $product->short_description }}</p>
					<form action="{{ route('addToCart') }}" method="POST">
						@csrf
						<input type="hidden" name="product_id" value="{{ $product->id }}">
						<input type="hidden" name="quantity">
						<button class="add-to-cart-button">Aan winkelmand toevoegen</button>	
					</form>	
				</div>
			@else
				<p>Geen product gevonden</p>
			@endif
		</div>
</main>
</x-layout>