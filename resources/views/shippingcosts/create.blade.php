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
    <h2>Verzendkosten aanmaken</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

    <form action="{{ route('shippingCostStore') }}" method="POST" class="form profile">
        @csrf
        @method('POST')

        <div class="form-input">
            <label for="amount">Bedrag</label>
            <input type="number" step="0.01" name="amount" value="{{ old('amount') }}">
            @error('amount')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-input">
            <label for="country">Land</label>
            <select name="country" id="country">
                @foreach(shippingCountries() as $code => $name)
                    <option value="{{ $code }}" {{ old('country') == $code ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
            @error('country')
            <div class="error">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-input">
            <label for="is_published">Publiceren</label>
            <select name="is_published" id="is_published">
                <option value="0" {{ old('is_published')=='0' ? 'selected' : '' }}>Nee</option>
                <option value="1" {{ old('is_published')=='1' ? 'selected' : '' }}>Ja</option>
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
