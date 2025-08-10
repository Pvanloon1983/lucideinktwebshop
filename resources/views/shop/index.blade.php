<x-layout>
<main class="container page shop">
    <h2>Winkel</h2>
		@if(session('success'))
		<div class="alert alert-success" style="position: relative;">
			{{ session('success') }}
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
		</div>
		@endif

		@if(session('error'))
		<div class="alert alert-error" style="position: relative;">
			{{ session('error') }}
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
		</div>
		@endif
		<div class="book-box">
		@foreach ($products as $product)
			<a href="{{ route('productShow', $product->id) }}">
			<div class="card">
				<div class="image-container">
					<img src="{{ e(
						Str::startsWith($product->image_1, 'https://')
								? $product->image_1
								: (Str::startsWith($product->image_1, 'image/books/')
										? asset($product->image_1)
										: (Str::startsWith($product->image_1, 'images/books/')
												? asset($product->image_1)
												: asset('storage/' . $product->image_1)
										)
								)
				) }}" alt="">
				</div>
				<h6 class="title">{{ $product->title }}</h6>
				@if (isset($product->category) && !empty($product->category->name))
					<p class="category">{{ $product->category->name }}</p>
				@endif
				@if (isset($product->price) && !empty($product->category->name))
					<p class="price">€ {{ $product->price }}</p>
				@endif
			</div>
			</a>
		@endforeach
		</div>
</main>
</x-layout>