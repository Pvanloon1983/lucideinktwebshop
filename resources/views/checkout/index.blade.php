<x-layout>
	<main class="container page checkout">
		<h2>Afrekenen</h2>
		@if(session('success'))
		<div class="alert alert-success" style="position: relative;">
			{{ session('success') }}
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
		</div>
		@endif

		{{-- @if(session('error'))
		<div class="alert alert-error" style="position: relative;">
			{{ session('error') }}
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
		</div>
		@endif --}}

		<form class="form" action="{{ route('storeCheckout') }}" method="POST">
			@csrf
			<div class="checkout-grid">

				<div>
					<div class="item customer-details">
						<h3>Factuurgegevens</h3>

						<div class="form-input">
							<label for="billing-email">E-mailadres</label>
							<input type="email" name="billing-email" autocomplete="email" value="{{ old('billing-email') }}">
							@error('billing-email')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="name-box">
							<div class="form-input">
								<label for="billing-first_name">Voornaam</label>
								<input type="text" name="billing-first_name" autocomplete="given-name"
									value="{{ old('billing-first_name') }}">
								@error('billing-first_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="form-input">
								<label for="billing-last_name">Achternaam</label>
								<input type="text" name="billing-last_name" autocomplete="family-name"
									value="{{ old('billing-last_name') }}">
								@error('billing-last_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="street-box">
							<div class="form-input">
								<label for="billing-street">Straatnaam</label>
								<input type="text" name="billing-street" autocomplete="address-line1"
									value="{{ old('billing-street') }}">
								@error('billing-street')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="housnumber-box">
								<div class="form-input">
									<label for="billing-housenumber">Huisnummer</label>
									<input type="number" name="billing-housenumber" autocomplete="address-line2"
										value="{{ old('billing-housenumber') }}">
									@error('billing-housenumber')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
								<div class="form-input">
									<label for="billing-housenumber-add">Toevoeging</label>
									<input type="number" name="billing-housenumber-add" autocomplete="address-line2"
										value="{{ old('billing-housenumber-add') }}">
									@error('billing-housenumber-add')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-input">
							<label for="billing-postal-zip-code">Postcode</label>
							<input type="text" name="billing-postal-zip-code" autocomplete="postal-code"
								value="{{ old('billing-postal-zip-code') }}">
							@error('billing-postal-zip-code')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing-city">Plaats</label>
							<input type="text" name="billing-city" autocomplete="address-level2" value="{{ old('billing-city') }}">
							@error('billing-city')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing-phone">Telefoonnummer</label>
							<input type="text" name="billing-phone" autocomplete="tel" value="{{ old('billing-phone') }}">
							@error('phone')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing-company">Bedrijfsnaam (optioneel)</label>
							<input type="text" name="billing-company" autocomplete="organization"
								value="{{ old('billing-company') }}">
							@error('billing-company')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing-country">Land</label>
							<select name="billing-country" id="" autocomplete="country">
								<option value="nl" {{ old('billing-country')=='nl' ? 'selected' : '' }}>Nederland</option>
								<option value="be" {{ old('billing-country')=='be' ? 'selected' : '' }}>België</option>
							</select>
							@error('billing-country')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input customer-account">
							<label for="customer-account">Account aanmaken? Vul dan een wachtwoord in.</label>
						</div>

						<div class="create-account-box">
							<div class="form-input">
								<label for="password">Wachtwoord</label>
								<input type="password" name="password">
								@error('password')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="form-input">
								<label for="password_confirmation">Bevestig wachtwoord</label>
								<input type="password" name="password_confirmation">
							</div>
						</div>

						<div class="form-input alt-shipping">
							<label for="alt-shipping">Verzenden naar een ander adres?</label>
							<input type="checkbox" name="alt-shipping" id="alt-shipping">
						</div>


					</div>

					<div class="item customer-details alternate">
						<h3>Alternatief verzendadres</h3>

						<div class="name-box">
							<div class="form-input">
								<label for="shipping_first_name">Voornaam</label>
								<input type="text" name="shipping_first_name" autocomplete="shipping given-name"
									value="{{ old('shipping_first_name') }}">
								@error('shipping_first_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="form-input">
								<label for="shipping_last_name">Achternaam</label>
								<input type="text" name="shipping_last_name" autocomplete="shipping family-name"
									value="{{ old('shipping_last_name') }}">
								@error('last_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="street-box">
							<div class="form-input">
								<label for="shipping_street">Straatnaam</label>
								<input type="text" name="shipping_street" autocomplete="shipping address-line1"
									value="{{ old('shipping_street') }}">
								@error('shipping_street')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="housnumber-box">
								<div class="form-input">
									<label for="shipping_housenumber">Huisnummer</label>
									<input type="number" name="shipping_housenumber" autocomplete="shipping address-line2"
										value="{{ old('shipping_housenumber') }}">
									@error('shipping_housenumber')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
								<div class="form-input">
									<label for="shipping_housenumber">Huisnummer</label>
									<input type="number" name="shipping_housenumber" value="{{ old('shipping_housenumber') }}">
									@error('shipping_housenumber')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-input">
							<label for="shipping_postal-zip-code">Postcode</label>
							<input type="text" name="shipping_postal-zip-code" autocomplete="shipping postal-code"
								value="{{ old('shipping_postal-zip-code') }}">
							@error('shipping_postal-zip-code')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="shipping_city">Plaats</label>
							<input type="text" name="shipping_city" autocomplete="shipping address-level2"
								value="{{ old('shipping_city') }}">
							@error('shipping_city')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="shipping_phone">Telefoonnummer</label>
							<input type="text" name="shipping_phone" autocomplete="shipping tel" value="{{ old('shipping_phone') }}">
							@error('shipping_phone')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="shipping_company">Bedrijfsnaam (optioneel)</label>
							<input type="text" name="shipping_company" autocomplete="shipping organization"
								value="{{ old('shipping_company') }}">
							@error('shipping_company')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="shipping_country">Land</label>
							<select name="shipping_country" id="" autocomplete="shipping country">
								<option value="nl">Nederland</option>
								<option value="be">België</option>
							</select>
							@error('shipping_country')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

					</div>
				</div>

				<div class="item order-details">
					<h3>Bestelling</h3>

					<table class="order-table" style="width: 100%;">
						<thead>
							<tr>
								<th>Product</th>
								<th style="text-align: right">Subtotaal</th>
							</tr>
						</thead>
						<tbody>
							@foreach ($cart as $item)
							<tr>
								<td>
									{{ $item['quantity'] }} &times; {{ $item['name'] }}
								</td>
								<td style="text-align: right">
									{{-- Assuming $item['subtotal'] exists, otherwise calculate: $item['price'] * $item['quantity'] --}}
									&euro; {{ number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') }}
								</td>
							</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr class="total-price">
								<td><strong>Totaal</strong></td>
								<td style="text-align: right">
									<strong>
										&euro; {{ number_format(collect($cart)->sum(function($item) { return ($item['price'] *
										$item['quantity']); }), 2, ',', '.') }}
									</strong>
								</td>
							</tr>
						</tfoot>
					</table>
					<div class="place-order">
						<button type="submit" class="btn"><span class="loader"></span>Plaats bestelling</button>
					</div>
				</div>
			</div>
		</form>
	</main>
</x-layout>