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
				Je ontvangt zo snel mogelijk een bevestiging per e-mail.</p>
				<a class="link-back" href="{{ route('shop') }}">Verder winkelen</a>
			</div>
		@endif
	</main>
</x-layout>