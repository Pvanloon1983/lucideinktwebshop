<x-layout>
  <main class="container auth-page">
    <form action="{{ route('loginUser') }}" method="POST" class="form">
      @csrf
      @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
          {{ session('success') }}
          <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-error">
          {{ session('error') }}
          <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
      @endif
      <h2>Inloggen</h2>
      <div class="form-input">
        <label for="email">E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
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
      <div class="remember-me-box">
        <input type="checkbox" class="remember-me" name="remember" {{ old('remember') ? 'checked'
						: '' }}>
        <label for="remember">Onthoud mij</label>
      </div>
      <div class="form-input">
        <button type="submit" class="btn"><span class="loader"></span>Inloggen</button>
      </div>
      <div class="form-input">
        <span>Nog geen account? <a href="{{ route('register') }}">Registreren</a></span>
      </div>
    </form>
  </main>
</x-layout>