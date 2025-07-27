<x-dashboard-layout>
<main class="container page dashboard">
    <h2>Profiel bijwerken</h2>
    @if(session('success'))
        <div class="alert alert-success" style="position: relative;">
            {{ session('success') }}
            <button type="button" class="alert-close" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
    @endif

			<form action="{{ route('updateProfile') }}" method="POST" class="form profile">
        @csrf
        <div class="form-input">
          <label for="first_name">Voornaam</label>
          <input type="text" name="first_name" value="{{ $user->first_name }}">
          @error('first_name')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>
        <div class="form-input">
          <label for="last_name">Achternaam</label>
          <input type="text" name="last_name" value="{{ $user->last_name }}">
          @error('last_name')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>
				<div class="form-input">
          <label for="email">E-mail</label>
          <input type="email" name="email" value="{{ $user->email }}">
          @error('email')
          <div class="error">{{ $message }}</div>
          @enderror
        </div>

        <div class="form-input">
          <label for="password">Nieuw Wachtwoord</label>
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
      </form>

</main>
</x-dashboard-layout>