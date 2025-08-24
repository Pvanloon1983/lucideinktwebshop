<x-dashboard-layout>
    <main class="container page dashboard">
        <h2>Gebruiker toevoegen</h2>
        @if (session('success'))
            <div class="alert alert-success" style="position: relative;">
                {{ session('success') }}
                <button type="button" class="alert-close"
                    onclick="this.parentElement.style.display='none';">&times;</button>
            </div>
        @endif

        <form action="{{ route('userStore') }}" method="POST" class="form profile">
            @csrf
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
            @php
                $userRoles = [
                    'user' => 'Gebruiker',
                    'admin' => 'Admin',
                ];
            @endphp
            <label for="user_role">Rol</label>
            <select style="width: fit-content;" name="user_role">
                @if (!empty($userRoles))
                    @foreach ($userRoles as $key => $label)
                        <option value="{{ $key }}" {{ old('user_role')==$key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach                                
                @endif                          
            </select>
            </div>
            <div class="form-input">
                <button type="submit" class="btn"><span class="loader"></span>Opslaan</button>
            </div>
        </form>

    </main>
</x-dashboard-layout>
