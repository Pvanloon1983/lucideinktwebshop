<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function registerPage()
    {
      return view('auth.register');
    }

    public function loginPage(){
      return view('auth.login');
    }

    public function registerUser(Request $request): RedirectResponse
    {

      $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email:rfc,dns|unique:users,email|confirmed',
        'password' => 'required|string|min:8|confirmed',
      ], [
        'first_name.required' => 'Voornaam is verplicht.',
        'last_name.required' => 'Achternaam is verplicht.',
        'email.required' => 'E-mailadres is verplicht.',
        'email.email' => 'Voer een geldig e-mailadres in.',
        'email.unique' => 'Dit e-mailadres is al geregistreerd.',
        'email.confirmed' => 'De e-mailadressen komen niet overeen.',
        'password.required' => 'Wachtwoord is verplicht.',
        'password.min' => 'Wachtwoord moet minimaal 8 tekens bevatten.',
        'password.confirmed' => 'Wachtwoorden komen niet overeen.',
      ]);

      $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
      ]);

      // Fire the Registered event
      event(new Registered($user));

      // Optionally log the user in
      // auth()->login($user);

      // Redirect to login page
      return redirect()->route('login')->with('success', 'Registratie is gelukt! Je kunt nu inloggen.');

    }

    public function loginUser(Request $request): RedirectResponse
    {
      $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
      ], [
        'email.required' => 'E-mailadres is verplicht.',
        'email.email' => 'Voer een geldig e-mailadres in.',
        'password.required' => 'Wachtwoord is verplicht.',
      ]);

      if (auth()->attempt($validated, $request->filled('remember'))) {
        $request->session()->regenerate();
        return redirect()->route('dashboard')->with('success', 'Je bent ingelogd!');
      }

      return back()->with('error', 'De inloggegevens kloppen niet.');

    }

    public function logoutGet () {
      return redirect()->route('home');
    }

    public function logout(Request $request) {
      auth()->logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();
      return redirect()->route('login')->with('success', 'Je bent nu uitgelogd.');
    }

}
