<x-layout>
    <main class="container page checkout">
        <h2>Afrekenen</h2>

        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error" style="position: relative;">
                {{ session('error') }}
                <button type="button" class="alert-close"
                        onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        @if ($errors->has('stock'))
            <div class="alert alert-error">
                <div>
                    <div>
                        <a style="text-decoration: none" href="{{ route('cartPage') }}">← Terug naar winkelwagen</a>
                    </div>
                    {!! $errors->first('stock') !!}
                </div>
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        <form class="form checkout" action="{{ route('storeCheckout') }}" method="POST">
            @csrf
            <div class="checkout-grid">

                <div>
                    <div class="item customer-details">
                        <h3>Factuurgegevens</h3>

                        <div class="form-input">
                            <label for="billing_email">E-mailadres</label>
                            <input type="email" name="billing_email" autocomplete="email"
                                value="{{ old('billing_email') }}">
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
                                    <label for="billing_house_number">Huisnummer</label>
                                    <input type="number" name="billing_house_number" autocomplete="address-line2"
                                        value="{{ old('billing_house_number') }}">
                                    @error('billing_house_number')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-input">
                                    <label for="billing_house_number-add">Toevoeging</label>
                                    <input type="text" name="billing_house_number-add" autocomplete="address-line2"
                                        value="{{ old('billing_house_number-add') }}">
                                    @error('billing_house_number-add')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-input">
                            <label for="billing_postal_code">Postcode</label>
                            <input type="text" name="billing_postal_code" autocomplete="postal-code"
                                value="{{ old('billing_postal_code') }}">
                            @error('billing_postal_code')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-input">
                            <label for="billing_city">Plaats</label>
                            <input type="text" name="billing_city" autocomplete="address-level2"
                                value="{{ old('billing_city') }}">
                            @error('billing_city')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-input">
                            <label for="billing_phone">Telefoonnummer</label>
                            <input type="text" name="billing_phone" autocomplete="tel"
                                value="{{ old('billing_phone') }}">
                            @error('billing_phone')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-input">
                            <label for="billing_company">Bedrijfsnaam (optioneel)</label>
                            <input type="text" name="billing_company" autocomplete="organization"
                                value="{{ old('billing_company') }}">
                            @error('billing_company')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-input">
                            <label for="billing_country">Land</label>
                            <select name="billing_country" autocomplete="country">
                                <option value="NL" {{ old('billing_country') == 'nl' ? 'selected' : '' }}>Nederland
                                </option>
                                <option value="BE" {{ old('billing_country') == 'be' ? 'selected' : '' }}>België
                                </option>
                            </select>
                            @error('billing_country')
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
                            <input type="checkbox" name="alt-shipping" id="alt-shipping"
                                {{ old('alt-shipping') ? 'checked' : '' }}>
                        </div>
                    </div>

                    <div class="item customer-details alternate" id="shipping-fields">
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
                                @error('shipping_last_name')
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
                                    <label for="shipping_house_number">Huisnummer</label>
                                    <input type="number" name="shipping_house_number"
                                        autocomplete="shipping address-line2"
                                        value="{{ old('shipping_house_number') }}">
                                    @error('shipping_house_number')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-input">
                                    <label for="shipping_house_number-add">Toevoeging</label>
                                    <input type="text" name="shipping_house_number-add"
                                        value="{{ old('shipping_house_number-add') }}">
                                    @error('shipping_house_number-add')
                                        <div class="error">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-input">
                            <label for="shipping_postal_code">Postcode</label>
                            <input type="text" name="shipping_postal_code" autocomplete="shipping postal-code"
                                value="{{ old('shipping_postal_code') }}">
                            @error('shipping_postal_code')
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
                            <input type="text" name="shipping_phone" autocomplete="shipping tel"
                                value="{{ old('shipping_phone') }}">
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
                            <select name="shipping_country" autocomplete="shipping country">
                                <option value="NL">Nederland</option>
                                <option value="BE">België</option>
                            </select>
                            @error('shipping_country')
                                <div class="error">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="item order-details">
                    <p class="back-to-cart"><a href="{{ route('cartPage') }}">← Terug naar winkelwagen</a></p>

                    <h3>Bestelling</h3>

                    <div class="form-input">
                        <div style="display: flex;flex-direction: column">
                            <input style="width: fit-content; margin-bottom: 10px" type="text" name="discount_code" id="discount_code" value="{{ old('discount_code') }}" placeholder="Vul kortingscode in">
                            <button id="add_discount_code" style="height: 32px" class="btn small"><span class="loader" style="display:none"></span>Kortingscode toepassen</button>
                        </div>
                        @error('discount_code')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <table class="order-table" style="width:100%;">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th style="text-align:right">Subtotaal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cart as $item)
                                <tr>
                                    <td>{{ $item['quantity'] }} &times; {{ $item['name'] }}</td>
                                    <td style="text-align:right">&euro;
                                        {{ number_format($item['subtotal'] ?? $item['price'] * $item['quantity'], 2, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="total-price" id="total-row">
                                <td><strong>Totaal</strong></td>
                                <td style="text-align:right"><strong id="order-total">&euro;
                                        {{ number_format(collect($cart)->sum(fn($i) => $i['price'] * $i['quantity']), 2, ',', '.') }}</strong>
                                </td>
                            </tr>
                            <tr id="discount-row" style="display:none">
                                <td><span>Korting</span> <span id="discount-code-label" style="font-size:12px;color:#666;"></span></td>
                                <td style="text-align:right;color:#b30000;">-<span id="discount-amount">0,00</span></td>
                            </tr>
                            <tr id="new-total-row" style="display:none">
                                <td><strong>Totaal na korting</strong></td>
                                <td style="text-align:right"><strong id="order-new-total">&euro; 0,00</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                    <div id="remove-discount-container" style="display:none;margin-bottom:10px;">
                        <button type="button" id="remove_discount_code" class="btn small" style="background:#eee;color:#b30000;">Verwijder kortingscode</button>
                    </div>

                    <div id="myparcel-delivery-options"></div>
                    <input type="hidden" name="myparcel_delivery_options" id="myparcel_delivery_options" />

                    <div class="place-order">
                        @error('myparcel_delivery_options')
                            <div class="error" style="color:#b30000; margin-bottom:10px;">{{ $message }}</div>
                        @enderror
                        <button type="submit" class="btn"><span class="loader"></span>Plaats bestelling</button>
                    </div>
                </div>
            </div>
        </form>
    </main>

</x-layout>
