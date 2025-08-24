<x-layout>
    <main class="container auth-page">
        <form action="{{ route('password.update') }}" method="POST" class="form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <h2>Wachtwoord resetten</h2>
            <div class="form-input">
                <label for="email">E-mail</label>
                <input type="email" name="email" value="{{ old('email', request()->email) }}">
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>
            <div class="form-input">
                <label for="password">Nieuw wachtwoord</label>
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
                <button type="submit" class="btn"><span class="loader"></span>Verzenden</button>
            </div>
        </form>
    </main>
</x-layout>
