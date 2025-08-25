<x-layout>
	<main class="container page checkout">
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
		@elseif(isset($success) && $success)
			<div class="checkout-message success">
				<strong>Betaling geslaagd!</strong>
				<p>Bedankt voor je bestelling! Je betaling is succesvol verwerkt.<br>
				Je ontvangt zo snel mogelijk een bevestiging per e-mail met alle ordergegevens en een factuur als PDF.</p>
				<a class="link-back" href="{{ route('shop') }}">Verder winkelen</a>
			</div>

			@php
				$order = $order ?? null;
			@endphp

			@if($order)
				<div class="order-summary" style="margin-top:2rem;">
					<h2>Jouw bestelling</h2>
					<p><strong>Ordernummer:</strong> {{ $order->id }}<br>
					<strong>Besteldatum:</strong> {{ $order->created_at->format('d-m-Y H:i') }}</p>
					<table width="100%" cellpadding="8" cellspacing="0" style="border-collapse:collapse;margin-bottom:18px;">
						<thead>
							<tr style="background:#f7f7f7;">
								<th align="left">Product</th>
								<th align="center">Aantal</th>
								<th align="right">Stukprijs</th>
								<th align="right">Subtotaal</th>
							</tr>
						</thead>
						<tbody>
							@foreach($order->items as $item)
							<tr>
								<td>{{ $item->product_name }}</td>
								<td align="center">{{ $item->quantity }}</td>
								<td align="right">€ {{ number_format($item->unit_price, 2, ',', '.') }}</td>
								<td align="right">€ {{ number_format($item->subtotal, 2, ',', '.') }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
					<p><strong>Totaal:</strong> € {{ number_format($order->total, 2, ',', '.') }}</p>
					<p style="color:#888;font-size:15px;">Je ontvangt een bevestiging en factuur per e-mail. Bewaar deze e-mail voor je administratie.</p>
				</div>
			@endif
		@endif
	</main>
</x-layout>