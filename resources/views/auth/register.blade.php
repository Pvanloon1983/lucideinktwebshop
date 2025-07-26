<x-layout>
  <main class="container auth-page">
      <form action="{{ route('registerUser')  }}" method="POST" class="form">
        @csrf
        <h2>Registreren</h2>
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
        <div class="form-input">
          <label for="email">E-mail</label>
          <input type="email" name="email" value="{{ old('email') }}">
          @error('email')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-input">
          <label for="email_confirmation">Bevestig e-mail</label>
          <input type="email" name="email_confirmation" value="{{ old('email_confirmation') }}">
          @error('email_confirmation')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>
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
        <div class="form-input">
          <button type="submit" class="btn">Verzenden</button>
        </div>
        <div class="form-input">
          <span>All een account? <a href="{{ route('login') }}">Inloggen</a></span>
        </div>
      </form>
  </main>
</x-layout>