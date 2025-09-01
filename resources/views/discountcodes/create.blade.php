<x-dashboard-layout>
  <main class="container page dashboard">
    <h2>Kortingscode aanmaken</h2>
    @if(session('success'))
      <div class="alert alert-success" style="position: relative;">
        {{ session('success') }}
        <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
      </div>
    @endif

    <form action="{{ route('discountStore') }}" method="POST" class="form profile">
      @csrf
      @method('POST')

      <div class="form-input">
        <label for="code">Code</label>
        <input type="text" name="code" value="{{ old('code') }}">
        @error('code')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="description">Beschrijving</label>
        <input type="text" name="description" value="{{ old('description') }}">
        @error('description')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="discount_type">Kortingstype</label>
        <select name="discount_type" id="discount_type">
          <option value="percent" {{ old('percent')=='percent' ? 'selected' : '' }}>Procentuele Korting</option>
          <option value="amount" {{ old('amount')=='amount' ? 'selected' : '' }}>Vaste winkelwagenkorting</option>
        </select>
        @error('discount_type')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="discount">Korting</label>
        <input type="number" name="discount" value="{{ old('discount') }}" min="0.01" step="0.01">
        @error('discount')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="expiration_date">Vervaldatum</label>
        <input type="date" name="expiration_date" value="{{ old('expiration_date') }}">
        @error('expiration_date')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="usage_limit">Gebruikslimiet</label>
        <input type="number" name="usage_limit" value="{{ old('usage_limit') }}" min="1" step="1">
        @error('usage_limit')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-input">
        <label for="usage_limit_per_customer">Gebruikslimiet per klant</label>
        <input type="number" name="usage_limit_per_customer" value="{{ old('usage_limit_per_customer') }}" min="1" step="1">
        @error('usage_limit_per_customer')
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