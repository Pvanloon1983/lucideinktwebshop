<x-layout>
<main class="container page shop">
    <h1>Winkel</h1>
		<div class="book-box">
		@foreach ($products as $product)
			<a href="{{ route('productShow', $product->id) }}">
			<div class="card">
				<div class="image-container">
					<img src="{{ asset('/storage/' . $product->image_1) }}" alt="">
				</div>
				<h6 class="title">{{ $product->title }}</h6>
				<p class="category">{{ $product->category->name }}</p>
				<p class="price">€ {{ $product->price }}</p>
			</div>
			</a>
		@endforeach
		</div>
</main>
</x-layout>