<x-dashboard-layout>
  <main class="container page dashboard">
    <h2>Bestelling aanmaken</h2>

    @if(session('success'))
      <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
      </div>
    @endif

    <form class="form" action="{{ route('orderStore') }}" method="POST">
      @csrf

      <div class="order-grid">

        {{-- Producten --}}
        <div class="item product-list" id="product-list">
          <h3>Voeg producten toe</h3>

          @if($errors->has('items'))
            <div class="alert alert-error">
              {{ $errors->first('items') }}
              <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
          @endif

          <ul>
            @foreach ($products as $product)
              @php $inputName = "items.{$product->id}.qty"; @endphp
              <li>
                <div class="input-box">
                  <label for="product_{{ $product->id }}">{{ $product->title }}</label>
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

          <div class="total-price" id="total-price"></div>
        </div>

        {{-- Korting --}}
        <div class="item discount_value">
          <h3>Voeg korting toe</h3>
          @php
            $resetDiscount = !$errors->any() && !old('discount_value') && !old('discount_type');
            $discountValue = $resetDiscount ? 0 : old('discount_value', session('discount_value', 0));
            $discountType  = $resetDiscount ? 'amount' : old('discount_type', session('discount_type', 'amount'));
          @endphp

          <label for="discount_value">Korting:</label>
          <input type="number" step="0.01" min="0" id="discount_value" name="discount_value" value="{{ $discountValue }}">

          <span class="discount_type">Kies soort korting:</span>
          <select id="discount_type" name="discount_type">
            <option value="amount"  {{ $discountType==='amount'  ? 'selected' : '' }}>Bedrag (€)</option>
            <option value="percent" {{ $discountType==='percent' ? 'selected' : '' }}>Percentage (%)</option>
          </select>

          <div class="discounted-total" id="discounted-total"></div>
        </div>

        {{-- Factuur + MyParcel --}}
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
                <input type="number" name="billing_housenumber-add" autocomplete="address-line2" value="{{ old('billing_housenumber-add') }}">
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
            @error('phone') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="form-input">
            <label for="billing_company">Bedrijsnaam (optioneel)</label>
            <input type="text" name="billing_company" autocomplete="organization" value="{{ old('billing_company') }}">
            @error('billing_company') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="form-input">
            <label for="billing_country">Land</label>
            <select name="billing_country" autocomplete="country">
              <option value="NL" {{ old('billing_country')=='NL' ? 'selected' : '' }}>Nederland</option>
              <option value="BE" {{ old('billing_country')=='BE' ? 'selected' : '' }}>België</option>
            </select>
            @error('billing_country') <div class="error">{{ $message }}</div> @enderror
          </div>

          {{-- MyParcel keuze + widget --}}
          @error('myparcel_delivery_options')
            <div class="alert alert-error">{{ $message }}</div>
          @enderror

          {{-- Widget CSS/JS (load once) --}}
          <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/style.css" />
          <script src="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/myparcel.js"></script>

          {{-- Radiokeuze (tonen bij compleet adres) --}}
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

          {{-- Widget wrapper (niet verbergen met display:none in pickup) --}}
          <div id="myparcel-wrapper" style="margin:.5rem 0">
            <div id="myparcel-address-message" style="display:none;padding:1em;color:#b30000;"></div>
            <div id="myparcel-delivery-options"></div>
            <div id="myparcel-error" class="error" style="margin-top:.5rem"></div>
          </div>

          {{-- Hidden selectie --}}
          <input type="hidden" name="myparcel_delivery_options" id="myparcel_delivery_options">

          {{-- Handmatige afhaalpunt invoer (fallback/alternatief voor admin) --}}
          <fieldset id="manual-pickup" style="margin:.75rem 0; display:none;">
            <legend style="font-weight:600; margin-bottom:.25rem;">Afhaalpunt handmatig invoeren (admin)</legend>
            <div class="name-box">
              <div class="form-input">
                <label for="pickup_location_name">Naam locatie</label>
                <input type="text" name="pickup_location_name" id="pickup_location_name" value="{{ old('pickup_location_name') }}">
                @error('pickup_location_name') <div class="error">{{ $message }}</div> @enderror
              </div>
            </div>
            <div class="street-box">
              <div class="form-input">
                <label for="pickup_street">Straat</label>
                <input type="text" name="pickup_street" id="pickup_street" value="{{ old('pickup_street') }}">
                @error('pickup_street') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="housnumber-box">
                <div class="form-input">
                  <label for="pickup_number">Nr</label>
                  <input type="text" name="pickup_number" id="pickup_number" value="{{ old('pickup_number') }}">
                  @error('pickup_number') <div class="error">{{ $message }}</div> @enderror
                </div>
                <div class="form-input">
                  <label for="pickup_numberSuffix">Toevoeging</label>
                  <input type="text" name="pickup_numberSuffix" id="pickup_numberSuffix" value="{{ old('pickup_numberSuffix') }}">
                </div>
              </div>
            </div>
            <div class="name-box">
              <div class="form-input">
                <label for="pickup_postalCode">Postcode</label>
                <input type="text" name="pickup_postalCode" id="pickup_postalCode" value="{{ old('pickup_postalCode') }}">
                @error('pickup_postalCode') <div class="error">{{ $message }}</div> @enderror
              </div>
              <div class="form-input">
                <label for="pickup_city">Plaats</label>
                <input type="text" name="pickup_city" id="pickup_city" value="{{ old('pickup_city') }}">
                @error('pickup_city') <div class="error">{{ $message }}</div> @enderror
              </div>
            </div>
            <p style="font-size:.9em;color:#555;margin-top:.25rem;">Als de widget niet laadt, vul het afhaalpunt hier in. Deze gegevens worden meegestuurd naar MyParcel als pick-up.</p>
          </fieldset>

          <div class="place-order">
            <button type="submit" class="btn"><span class="loader"></span>Plaats bestelling</button>
          </div>
        </div>

        {{-- Alternatief verzendadres --}}
        <div class="item customer-details alternate">
          <h3>Alternatief verzendadres</h3>

          <div class="form-input alt-shipping">
            <label for="alt-shipping">Vink aan als je een alternatief verzendadres wilt gebruiken</label>
            <input type="checkbox" name="alt-shipping" id="alt-shipping">
          </div>

          <div class="name-box">
            <div class="form-input">
              <label for="shipping_first_name">Voornaam</label>
              <input type="text" name="shipping_first_name" autocomplete="shipping given-name" value="{{ old('shipping_first_name') }}">
              @error('shipping_first_name') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-input">
              <label for="shipping_last_name">Achternaam</label>
              <input type="text" name="shipping_last_name" autocomplete="shipping family-name" value="{{ old('shipping_last_name') }}">
              @error('last_name') <div class="error">{{ $message }}</div> @enderror
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
                <input type="number" name="shipping_housenumber-add" value="{{ old('shipping_housenumber-add') }}">
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
            <label for="shipping_company">Bedrijsnaam (optioneel)</label>
            <input type="text" name="shipping_company" autocomplete="shipping organization" value="{{ old('shipping_company') }}">
            @error('shipping_company') <div class="error">{{ $message }}</div> @enderror
          </div>

          <div class="form-input">
            <label for="shipping_country">Land</label>
            <select name="shipping_country" autocomplete="country">
              <option value="NL" {{ old('shipping_country')=='NL' ? 'selected' : '' }}>Nederland</option>
              <option value="BE" {{ old('shipping_country')=='BE' ? 'selected' : '' }}>België</option>
            </select>
            @error('shipping_country') <div class="error">{{ $message }}</div> @enderror
          </div>
        </div>

      </div>
    </form>
  </main>
</x-dashboard-layout>

<style>
  /* List-only view: keep some height for content but no tall map needed */
  #myparcel-wrapper { min-height: 120px; }
  #myparcel-delivery-options { min-height: 120px; }
</style>
