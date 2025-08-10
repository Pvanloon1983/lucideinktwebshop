<x-layout>
	<main class="container page cart">
		<h2>Winkelwagen</h2>
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

		@if (count($cart))
		<div class="table-wrapper">
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
							<img
								src="{{ e(
										Str::startsWith($item['image_1'], 'https://')
												? $item['image_1']
												: (Str::startsWith($item['image_1'], 'image/books/')
														? asset($item['image_1'])
														: (Str::startsWith($item['image_1'], 'images/books/')
																? asset($item['image_1'])
																: asset('storage/' . $item['image_1'])
														)
												)
								) }}"
								alt="">
						</td>
						<td class="td-title" data-label="Titel">{{ $item['name'] }}</td>
						<td class="td-quantity" style="text-align: left;" data-label="Aantal">
							<form action="{{ route('updateCart') }}" method="POST">
								@csrf
								<input type="hidden" name="product_id" value="{{ $item['product_id'] }}">
								<div class="update-cart" style="display: flex; align-items: center; justify-content: flex-start;">
									<input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width: 70px;">
									<button class="btn" type="submit" style="margin-left: 8px;"><span class="loader"></span>Bijwerken</button>
								</div>
							</form>
						</td>
						<td style="min-width:80px;" data-label="Stukprijs">€ {{ $item['price'] }}</td>
						<td style="min-width:80px;" data-label="Totaal">€ {{ $item['quantity'] * $item['price'] }}</td>
						<td style="min-width:80px;" data-label="Actie">
						<form action="{{ route('deleteItemFromCart') }}" method="POST">
							@csrf
							@method('DELETE')
								<div class="delete-cart-item">
								<input hidden type="text" name="product_id" value="{{ $item['product_id'] }}">
								<button class="btn" class="btn" type="submit" onclick="return confirm('Weet je zeker dat je dit wilt verwijderen?');"><span class="loader"></span>Verwijderen</button>
							</div>
						</form>
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
					<a href="{{ route('checkoutPage') }}">
						<button class="btn checkout">Afrekenen</button>	
					</a>					
					<form action="{{ route('removeCart') }}" method="POST">
						@csrf
						<button type="submit" class="btn delete" onclick="return confirm('Weet je zeker dat je dit wilt verwijderen?');"><span class="loader"></span>Winkelwagen legen</button>
					</form>
				</div>

			</div>
		</div>
		@else
		<p>Winkelwagen is leeg</p>
		@endif
	</main>
</x-layout>