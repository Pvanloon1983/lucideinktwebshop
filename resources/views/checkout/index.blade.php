<x-layout>
  <main class="container page checkout">
    <h2>Afrekenen</h2>

    @if(session('success'))
      <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
      </div>
    @endif

    <form class="form" action="{{ route('storeCheckout') }}" method="POST">
      @csrf
      <div class="checkout-grid">

        <div>
          <div class="item customer-details">
            <h3>Factuurgegevens</h3>

            <div class="form-input">
              <label for="billing_email">E-mailadres</label>
              <input type="email" name="billing_email" autocomplete="email" value="{{ old('billing_email') }}">
              @error('billing_email') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="name-box">
              <div class="form-input">
                <label for="billing_first_name">Voornaam</label>
                <input type="text" name="billing_first_name" autocomplete="given-name" value="{{ old('billing_first_name') }}">
                @error('billing_first_name') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="form-input">
                <label for="billing_last_name">Achternaam</label>
                <input type="text" name="billing_last_name" autocomplete="family-name" value="{{ old('billing_last_name') }}">
                @error('billing_last_name') <div class="error">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="street-box">
              <div class="form-input">
                <label for="billing_street">Straatnaam</label>
                <input type="text" name="billing_street" autocomplete="address-line1" value="{{ old('billing_street') }}">
                @error('billing_street') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="housnumber-box">
                <div class="form-input">
                  <label for="billing_housenumber">Huisnummer</label>
                  <input type="number" name="billing_housenumber" autocomplete="address-line2" value="{{ old('billing_housenumber') }}">
                  @error('billing_housenumber') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-input">
                  <label for="billing_housenumber-add">Toevoeging</label>
                  <input type="text" name="billing_housenumber-add" autocomplete="address-line2" value="{{ old('billing_housenumber-add') }}">
                  @error('billing_housenumber-add') <div class="error">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="form-input">
              <label for="billing_postal-zip-code">Postcode</label>
              <input type="text" name="billing_postal-zip-code" autocomplete="postal-code" value="{{ old('billing_postal-zip-code') }}">
              @error('billing_postal-zip-code') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="billing_city">Plaats</label>
              <input type="text" name="billing_city" autocomplete="address-level2" value="{{ old('billing_city') }}">
              @error('billing_city') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="billing_phone">Telefoonnummer</label>
              <input type="text" name="billing_phone" autocomplete="tel" value="{{ old('billing_phone') }}">
              @error('billing_phone') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="billing_company">Bedrijfsnaam (optioneel)</label>
              <input type="text" name="billing_company" autocomplete="organization" value="{{ old('billing_company') }}">
              @error('billing_company') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="billing_country">Land</label>
              <select name="billing_country" autocomplete="country">
                <option value="NL" {{ old('billing_country')=='nl' ? 'selected' : '' }}>Nederland</option>
                <option value="BE" {{ old('billing_country')=='be' ? 'selected' : '' }}>België</option>
              </select>
              @error('billing_country') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input customer-account">
              <label for="customer-account">Account aanmaken? Vul dan een wachtwoord in.</label>
            </div>

            <div class="create-account-box">
              <div class="form-input">
                <label for="password">Wachtwoord</label>
                <input type="password" name="password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="form-input">
                <label for="password_confirmation">Bevestig wachtwoord</label>
                <input type="password" name="password_confirmation">
              </div>
            </div>

            <div class="form-input alt-shipping">
              <label for="alt-shipping">Verzenden naar een ander adres?</label>
              <input type="checkbox" name="alt-shipping" id="alt-shipping" {{ old('alt-shipping') ? 'checked' : '' }}>
            </div>
          </div>

          <div class="item customer-details alternate" id="shipping-fields">
            <h3>Alternatief verzendadres</h3>

            <div class="name-box">
              <div class="form-input">
                <label for="shipping_first_name">Voornaam</label>
                <input type="text" name="shipping_first_name" autocomplete="shipping given-name" value="{{ old('shipping_first_name') }}">
                @error('shipping_first_name') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="form-input">
                <label for="shipping_last_name">Achternaam</label>
                <input type="text" name="shipping_last_name" autocomplete="shipping family-name" value="{{ old('shipping_last_name') }}">
                @error('shipping_last_name') <div class="error">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="street-box">
              <div class="form-input">
                <label for="shipping_street">Straatnaam</label>
                <input type="text" name="shipping_street" autocomplete="shipping address-line1" value="{{ old('shipping_street') }}">
                @error('shipping_street') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="housnumber-box">
                <div class="form-input">
                  <label for="shipping_housenumber">Huisnummer</label>
                  <input type="number" name="shipping_housenumber" autocomplete="shipping address-line2" value="{{ old('shipping_housenumber') }}">
                  @error('shipping_housenumber') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-input">
                  <label for="shipping_housenumber-add">Toevoeging</label>
                  <input type="text" name="shipping_housenumber-add" value="{{ old('shipping_housenumber-add') }}">
                  @error('shipping_housenumber-add') <div class="error">{{ $message }}</div> @enderror
                </div>
              </div>
            </div>

            <div class="form-input">
              <label for="shipping_postal-zip-code">Postcode</label>
              <input type="text" name="shipping_postal-zip-code" autocomplete="shipping postal-code" value="{{ old('shipping_postal-zip-code') }}">
              @error('shipping_postal-zip-code') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="shipping_city">Plaats</label>
              <input type="text" name="shipping_city" autocomplete="shipping address-level2" value="{{ old('shipping_city') }}">
              @error('shipping_city') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="shipping_phone">Telefoonnummer</label>
              <input type="text" name="shipping_phone" autocomplete="shipping tel" value="{{ old('shipping_phone') }}">
              @error('shipping_phone') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="shipping_company">Bedrijfsnaam (optioneel)</label>
              <input type="text" name="shipping_company" autocomplete="shipping organization" value="{{ old('shipping_company') }}">
              @error('shipping_company') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-input">
              <label for="shipping_country">Land</label>
              <select name="shipping_country" autocomplete="shipping country">
                <option value="NL">Nederland</option>
                <option value="BE">België</option>
              </select>
              @error('shipping_country') <div class="error">{{ $message }}</div> @enderror
            </div>
          </div>
        </div>

        <div class="item order-details">
          <h3>Bestelling</h3>

          <table class="order-table" style="width:100%;">
            <thead>
              <tr><th>Product</th><th style="text-align:right">Subtotaal</th></tr>
            </thead>
            <tbody>
            @foreach ($cart as $item)
              <tr>
                <td>{{ $item['quantity'] }} &times; {{ $item['name'] }}</td>
                <td style="text-align:right">&euro; {{ number_format($item['subtotal'] ?? ($item['price'] * $item['quantity']), 2, ',', '.') }}</td>
              </tr>
            @endforeach
            </tbody>
            <tfoot>
              <tr class="total-price">
                <td><strong>Totaal</strong></td>
                <td style="text-align:right"><strong>&euro; {{ number_format(collect($cart)->sum(fn($i)=>$i['price']*$i['quantity']), 2, ',', '.') }}</strong></td>
              </tr>
            </tfoot>
          </table>

<!-- 1) CSS van de widget -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/style.css" />

<!-- 2) Keuze met radiobuttons (alleen tonen bij compleet adres) -->
<div class="mp-choice" id="mp-choice-block" style="margin:.5rem 0 1rem; display:none;">
  <label>
    <input type="radio" name="ship_mode" value="delivery" checked>
    <span>Bezorgd op het opgegeven adres</span>
  </label>
  <label>
    <input type="radio" name="ship_mode" value="pickup">
    <span>Ophalen bij een gekozen afhaalpunt</span>
  </label>
</div>

<!-- 3) De widget wrapper (alleen zichtbaar bij 'pickup') -->
<div id="myparcel-wrapper" style="display:none;margin:.5rem 0">
  <div id="myparcel-address-message" style="display:none;padding:1em;color:#b30000;"></div>
  <div id="myparcel-delivery-options" style="display:none;"></div>
  <div id="myparcel-error" class="error" style="margin-top:.5rem"></div>
</div>

<!-- 4) Hierin bewaren we de selectie (bezorging of pickup) -->
<input type="hidden" name="myparcel_delivery_options" id="myparcel_delivery_options">

<!-- 5) JS van de widget -->
<script src="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/myparcel.js"></script>
<!-- Gebruik dezelfde init-logica als in resources/js/app.js (custom pickup widget) -->

			<div class="place-order">
				<button type="submit" class="btn"><span class="loader"></span>Plaats bestelling</button>
			</div>

    </form>
  </main>
</x-layout>
