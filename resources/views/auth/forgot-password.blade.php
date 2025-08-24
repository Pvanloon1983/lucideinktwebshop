<x-layout>
  <main class="container auth-page page">
    <form action="{{ route('password.email') }}" method="POST" class="form">
      @csrf
        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif
      @if(session('error'))
        <div class="alert alert-error">
          {{ session('error') }}
          <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
      @endif
      <h2>Wachtwoord vergeten</h2>
      <div class="form-input">
        <label for="email">E-mail</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email')
        <div class="error">{{ $message }}</div>
        @enderror
      </div>
      <div class="form-input">
        <button type="submit" class="btn"><span class="loader"></span>Verzenden</button>
      </div>
      <div class="form-input">
         <span><a href="{{ route('login') }}">Terug naar inloggen</a></span>
      </div>
    </form>
  </main>
</x-layout>