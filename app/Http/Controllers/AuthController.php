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
      // Preserve the cart session data before invalidating the session
      $cart = $request->session()->get('cart');

      $request->session()->invalidate();
      $request->session()->regenerateToken();

      // Restore the cart session data after session regeneration
      if ($cart !== null) {
          $request->session()->put('cart', $cart);
      }
      return redirect()->route('login')->with('success', 'Je bent nu uitgelogd.');
    }

}
