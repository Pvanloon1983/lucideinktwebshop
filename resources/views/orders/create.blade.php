<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Bestelling aanmaken</h2>
    @if(session('success'))
    <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}

				@if(session('chosen_items'))
					<ul style="margin-top:8px;">
						@foreach(session('chosen_items') as $li)
							<li>
								{{ $li['title'] }} × {{ $li['qty'] }}
								(€ {{ number_format($li['unit_price'],2,',','.') }} p/st = € {{ number_format($li['subtotal'],2,',','.') }})
							</li>
						@endforeach
					</ul>
					<div style="margin-top:6px;">
						Totaal vóór korting: € {{ number_format(session('total_before_discount'),2,',','.') }}<br>
						@php $discountAmount = session('discount_amount'); @endphp
						@if($discountAmount > 0)
							@php $discountType = session('discount_type'); $discountVal = session('discount_value'); @endphp
							Ingevoerde korting: 
							@if($discountType === 'percent')
								{{ number_format($discountVal,2,',','.') }}%
							@else
								€ {{ number_format($discountVal,2,',','.') }}
							@endif
							<br>
							Korting toegepast: € {{ number_format($discountAmount,2,',','.') }}<br>
						@else
							Geen korting toegepast<br>
						@endif
						Totaal na korting: <strong>€ {{ number_format(session('total_after_discount'),2,',','.') }}</strong>
					</div>
				@endif
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
    </div>
    @endif

			<form class="form" action="{{ route('orderStore') }}" method="POST">

			@csrf

			<div class="product-list">
			@if($errors->has('items'))
				<div class="alert alert-error">
					{{ $errors->first('items') }}
			<button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
				</div>
			@endif
				<ul>
					@foreach ($products as $product)
						@php
							$inputName = "items.{$product->id}.qty";
						@endphp

						<li>
							<div class="input-box">
								<label for="product_{{ $product->id }}">{{ $product->title }} - {{ $product->price }}</label>
								<div class="quantity">
									<span>Aantal: </span>
									<input 
										type="number" 
										name="items[{{ $product->id }}][qty]" 
										id="product_{{ $product->id }}" 
										value="{{ old($inputName, 0) }}" 
										min="0"
										class="qty-input"
										data-price="{{ $product->price }}"
										data-id="{{ $product->id }}"
									>
									<span class="sub-item-price" id="sub-item-price-{{ $product->id }}"></span>
								</div>
							</div>
						</li>
						@endforeach
					</ul>
						<div style="margin-top: 10px;">
							@php $oldDiscountType = old('discount_type', session('discount_type', 'amount')); @endphp
							<label for="discount_value">Korting:</label>
							<input type="number" step="0.01" min="0" id="discount_value" name="discount_value" style="width: 80px;" value="{{ old('discount_value', session('discount_value')) }}">
							<select id="discount_type" name="discount_type">
								<option value="amount" {{ $oldDiscountType==='amount' ? 'selected' : '' }}>Bedrag (€)</option>
								<option value="percent" {{ $oldDiscountType==='percent' ? 'selected' : '' }}>Percentage (%)</option>
							</select>
						</div>
						<span id="total-price"></span>
						<span id="discounted-total" style="display:block; font-weight:bold; margin-top:6px;"></span>
			</div>

			<div class="order-grid">

					<div class="item customer-details">
						<h3>Factuurgegevens</h3>

						<div class="form-input">
							<label for="billing_email">E-mailadres</label>
							<input type="email" name="billing_email" autocomplete="email" value="{{ old('billing_email') }}">
							@error('billing_email')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="name-box">
							<div class="form-input">
								<label for="billing_first_name">Voornaam</label>
								<input type="text" name="billing_first_name" autocomplete="given-name"
									value="{{ old('billing_first_name') }}">
								@error('billing_first_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="form-input">
								<label for="billing_last_name">Achternaam</label>
								<input type="text" name="billing_last_name" autocomplete="family-name"
									value="{{ old('billing_last_name') }}">
								@error('billing_last_name')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
						</div>

						<div class="street-box">
							<div class="form-input">
								<label for="billing_street">Straatnaam</label>
								<input type="text" name="billing_street" autocomplete="address-line1"
									value="{{ old('billing_street') }}">
								@error('billing_street')
								<div class="error">{{ $message }}</div>
								@enderror
							</div>
							<div class="housnumber-box">
								<div class="form-input">
									<label for="billing_housenumber">Huisnummer</label>
									<input type="number" name="billing_housenumber" autocomplete="address-line2"
										value="{{ old('billing_housenumber') }}">
									@error('billing_housenumber')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
								<div class="form-input">
									<label for="billing_housenumber-add">Toevoeging</label>
									<input type="number" name="billing_housenumber-add" autocomplete="address-line2"
										value="{{ old('billing_housenumber-add') }}">
									@error('billing_housenumber-add')
									<div class="error">{{ $message }}</div>
									@enderror
								</div>
							</div>
						</div>

						<div class="form-input">
							<label for="billing_postal-zip-code">Postcode</label>
							<input type="text" name="billing_postal-zip-code" autocomplete="postal-code"
								value="{{ old('billing_postal-zip-code') }}">
							@error('billing_postal-zip-code')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing_city">Plaats</label>
							<input type="text" name="billing_city" autocomplete="address-level2" value="{{ old('billing_city') }}">
							@error('billing_city')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing_phone">Telefoonnummer</label>
							<input type="text" name="billing_phone" autocomplete="tel" value="{{ old('billing_phone') }}">
							@error('phone')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing_company">Bedrijsnaam (optioneel)</label>
							<input type="text" name="billing_company" autocomplete="organization"
								value="{{ old('billing_company') }}">
							@error('billing_company')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="billing_country">Land</label>
							<select name="billing_country" id="" autocomplete="country">
								<option value="NL" {{ old('billing_country')=='NL' ? 'selected' : '' }}>Nederland</option>
								<option value="BE" {{ old('billing_country')=='BE' ? 'selected' : '' }}>België</option>
							</select>
							@error('billing_country')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						
					<div class="place-order">
						<button type="submit" class="btn"><span class="loader"></span>Plaats bestelling</button>
					</div>

					</div>

					<div class="item customer-details alternate">
						<h3>Alternatief verzendadres</h3>

						<div class="form-input alt-shipping">
							<label for="alt-shipping">Vink aan als je een alternatief verzendadres wilt gebruiken</label>
							<input type="checkbox" name="alt-shipping" id="alt-shipping">
						</div>

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
							<label for="shipping_company">Bedrijsnaam (optioneel)</label>
							<input type="text" name="shipping_company" autocomplete="shipping organization"
								value="{{ old('shipping_company') }}">
							@error('shipping_company')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

						<div class="form-input">
							<label for="shipping_country">Land</label>
							<select name="shipping_country" id="" autocomplete="country">
								<option value="NL" {{ old('shipping_country')=='NL' ? 'selected' : '' }}>Nederland</option>
								<option value="BE" {{ old('shipping_country')=='BE' ? 'selected' : '' }}>België</option>
							</select>
							@error('shipping_country')
							<div class="error">{{ $message }}</div>
							@enderror
						</div>

					</div>


			</div>
		</form>

</main>
</x-dashboard-layout>