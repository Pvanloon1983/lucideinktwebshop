<x-dashboard-layout>
@php
    function shippingCountries() {
        return [
            'NL' => 'Nederland',
            'BE' => 'België',
            // Voeg hier eenvoudig meer landen toe, bijvoorbeeld:
            // 'DE' => 'Duitsland',
            // 'FR' => 'Frankrijk',
        ];
    }
@endphp

<main class="container page dashboard">
    <h2>Verzendkosten bijwerken</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

    <form action="{{ route('shippingCostUpdate', $shippingCost->id) }}" method="POST" class="form profile">
        @method('PUT')
        @csrf

        <div class="form-input">
            <label for="amount">Bedrag</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount', $shippingCost->amount) }}">
            @error('amount')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-input">
            <label for="country">Land</label>
            <select name="country" id="country">
                @foreach(shippingCountries() as $code => $name)
                    <option value="{{ $code }}" {{ old('country', $shippingCost->country) == $code ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('country')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-input">
            <label for="is_published">Publiceren</label>
            <select name="is_published" id="is_published">
                <option value="0" {{ old('is_published', $shippingCost->is_published) == 0 ? 'selected' : '' }}>Nee</option>
                <option value="1" {{ old('is_published', $shippingCost->is_published) == 1 ? 'selected' : '' }}>Ja</option>
            </select>
            @error('is_published')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-input">
            <button type="submit" class="btn"><span class="loader"></span>Opslaan</button>
        </div>
    </form>
</main>
</x-dashboard-layout>

