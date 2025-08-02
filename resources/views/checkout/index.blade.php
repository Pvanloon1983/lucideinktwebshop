<x-layout>
<main class="container page checkout">
    <h1>Afrekenen</h1>
			<form class="form" action="" method="POST">
				@csrf
			<div class="checkout-grid">
				<div class="item customer-details">
					<h2>Klantdetails</h2>

						<div class="form-input">
								<label for="email">Email</label>
								<input type="email" name="email" value="{{ old('email') }}">
								@error('email')
								<div class="error">{{ $message }}</div>
								@enderror
						</div>

						<div class="name-box">
							<div class="form-input">
									<label for="first_name">Voornaam</label>
									<input type="text" name="first_name" value="{{ old('first_name') }}">
									@error('first_name')
									<div class="error">{{ $message }}</div>
									@enderror
							</div>
							<div class="form-input">
									<label for="last_name">Achternaam</label>
									<input type="text" name="last_name" value="{{ old('last_name') }}">
									@error('last_name')
									<div class="error">{{ $message }}</div>
									@enderror
							</div>
						</div>

						<div class="street-box">
							<div class="form-input">
									<label for="street">Straat</label>
									<input type="text" name="street" value="{{ old('street') }}">
									@error('street')
									<div class="error">{{ $message }}</div>
									@enderror
							</div>
							<div class="form-input">
									<label for="housenumber">Huisnummer</label>
									<input type="number" name="housenumber" value="{{ old('housenumber') }}">
									@error('housenumber')
									<div class="error">{{ $message }}</div>
									@enderror
							</div>
						</div>

						<div class="form-input">
								<label for="postal-zip-code">Postcode</label>
								<input type="text" name="postal-zip-code" value="{{ old('postal-zip-code') }}">
								@error('postal-zip-code')
								<div class="error">{{ $message }}</div>
								@enderror
						</div>

						<div class="form-input">
								<label for="city">Stad</label>
								<input type="text" name="city" value="{{ old('city') }}">
								@error('city')
								<div class="error">{{ $message }}</div>
								@enderror
						</div>

						<div class="form-input">
								<label for="country">Land</label>
								<select name="country" id="">
									<option value="nl">Nederland</option>
									<option value="be">België</option>
								</select>

								@error('country')
								<div class="error">{{ $message }}</div>
								@enderror
						</div>

				</div>
				<div class="item order-details">
					<h2>Bestelling</h2>
				</div>
			</div>
			</form>
</main>
</x-layout>