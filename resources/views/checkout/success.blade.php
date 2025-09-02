<x-layout>
	<main class="container page checkout success">

		@if(isset($error) && $error)
			<div class="checkout-message error">
				<strong>Er is iets misgegaan</strong>
				<p>{{ $error }}</p>
				<a class="link-back" href="{{ route('shop') }}">Terug naar de shop</a>
			</div>
		@elseif(isset($info) && $info)
			<div class="checkout-message info">
				<strong>Betaling in behandeling</strong>
				<p>{{ $info }}</p>
				<a class="link-back" href="{{ route('shop') }}">Terug naar de shop</a>
			</div>
		@elseif(isset($success) && $success && isset($order) && $order)
			<div class="order-summary">
				<h2 class="order-summary-title">Jouw bestelling</h2>
				<div class="order-bestelling-box">
					<div class="order-bestelling-header">
						<span class="order-bestelling-col product">Product</span>
						<span class="order-bestelling-col subtotal">Subtotaal</span>
					</div>
					@foreach($order->items as $item)
					<div class="order-bestelling-row">
						<span class="order-bestelling-product">
							{{ $item->quantity }} × {{ $item->product_name }}
						</span>
						<span class="order-bestelling-subtotal">€ {{ number_format($item->subtotal, 2, ',', '.') }}</span>
					</div>
					@endforeach
					<div class="order-bestelling-divider"></div>

					<div class="order-bestelling-total-row">
						<span class="order-bestelling-total-label"><strong>Totaal</strong></span>
						<span class="order-bestelling-total-value"><strong>€ {{ number_format($order->total, 2, ',', '.') }}</strong></span>
					</div>

					@if($order->discount_value > 0)
						<div class="order-bestelling-total-row">
							<span class="order-bestelling-total-label"><strong>Korting</strong></span>
							<span class="order-bestelling-total-value">
								<strong>
									@if($order->discount_value > 0 && $order->discount_type == 'percent')
										{{ (int)$order->discount_value }}%
									@elseif($order->discount_value > 0 && $order->discount_type == 'amount')
										- € {{ number_format($order->discount_value, 2, ',', '.') }}
									@endif
								</strong></span>
						</div>

						@if($order->discount_type == 'percent')
						<div class="order-bestelling-total-row">
							<span class="order-bestelling-total-label"><strong>Kortingsbedrag</strong></span>
							<span class="order-bestelling-total-value"><strong>€ {{ number_format($order->discount_price_total, 2, ',', '.') }}</strong></span>
						</div>
						@endif

						<div class="order-bestelling-total-row">
							<span class="order-bestelling-total-label"><strong>Totaal na korting</strong></span>
							<span class="order-bestelling-total-value"><strong>€ {{ number_format($order->total_after_discount, 2, ',', '.') }}</strong></span>
						</div>
					@endif

				</div>


				<div class="order-addresses">
					<div class="order-address-block">
						<h3>Factuuradres</h3>
						<div class="address-details">
							{{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
							@if($order->customer->billing_company)
								{{ $order->customer->billing_company }}<br>
							@endif
							{{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ $order->customer->billing_house_number_addition ? ' '.$order->customer->billing_house_number_addition : '' }}<br>
							{{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
							{{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
							@if($order->customer->billing_phone)
								Tel: {{ $order->customer->billing_phone }}<br>
							@endif
							<span class="address-email">Email: {{ $order->customer->billing_email }}</span>
						</div>
					</div>
					@if($order->shipping_street)
					<div class="order-address-block">
						<h3>Verzendadres</h3>
						<div class="address-details">
							{{ $order->shipping_first_name }} {{ $order->shipping_last_name }}<br>
							@if($order->shipping_company)
								{{ $order->shipping_company }}<br>
							@endif
							{{ $order->shipping_street }} {{ $order->shipping_house_number }}{{ $order->shipping_house_number_addition ? ' '.$order->shipping_house_number_addition : '' }}<br>
							{{ $order->shipping_postal_code }} {{ $order->shipping_city }}<br>
							{{ config('countries.' . $order->shipping_country) ?? $order->shipping_country }}<br>
							@if($order->shipping_phone)
								Tel: {{ $order->shipping_phone }}<br>
							@endif
						</div>
					</div>
					@else
					<div class="order-address-block">
						<h3>Verzendadres</h3>
						<div class="address-details">
							{{ $order->customer->billing_first_name }} {{ $order->customer->billing_last_name }}<br>
							@if($order->customer->billing_company)
								{{ $order->customer->billing_company }}<br>
							@endif
							{{ $order->customer->billing_street }} {{ $order->customer->billing_house_number }}{{ $order->customer->billing_house_number_addition ? ' '.$order->customer->billing_house_number_addition : '' }}<br>
							{{ $order->customer->billing_postal_code }} {{ $order->customer->billing_city }}<br>
							{{ config('countries.' . $order->customer->billing_country) ?? $order->customer->billing_country }}<br>
							@if($order->customer->billing_phone)
								Tel: {{ $order->customer->billing_phone }}<br>
							@endif
						</div>
					</div>
					@endif
				</div>
				<p class="order-summary-note">Je ontvangt een bevestiging en factuur per e-mail. Bewaar deze e-mail voor je administratie.</p>
			</div>
		@endif
	</main>
</x-layout>