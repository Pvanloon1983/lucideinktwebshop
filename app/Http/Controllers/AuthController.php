<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;

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

    public function forgotPassword()
    {
      return view('auth.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
      $request->validate(['email' => 'required|email']);

      $customerEmail = $request->input('email');
      $status = Password::sendResetLink(['email' => $customerEmail]);

      if ($status === Password::RESET_LINK_SENT) {
      return back()->with('success', __('Er is een e-mail verstuurd met instructies om je wachtwoord te resetten.'));
      } elseif ($status === Password::RESET_THROTTLED) {
      return back()->withErrors(['email' => __('Je hebt te vaak geprobeerd een resetlink aan te vragen. Probeer het later opnieuw.')]);
      } else {
      return back()->withErrors(['email' => __('We konden geen gebruiker vinden met dat e-mailadres.')]);
      }
    }

    public function resetPassword(string $token)
  {
    $email = request('email');
    // If token or email is missing, redirect to login
    if (empty($token) || empty($email)) {
      return redirect()->route('login')->with('no_right_link', __('Ongeldige of verlopen reset link.'));
    }
    // Optionally, check if the email exists in the database
    if (!User::where('email', $email)->exists()) {
      return redirect()->route('login')->with('no_right_link', __('Ongeldige of verlopen reset link.'));
    }
    return view('auth.reset-password', ['token' => $token, 'email' => $email]);
  }

    public function resetPasswordHandler(Request $request)
    {
      $request->validate([
          'token' => 'required',
          'email' => 'required|email',
          'password' => 'required|min:8|confirmed',
      ], [
          'token.required' => 'Reset token ontbreekt.',
          'email.required' => 'E-mailadres is verplicht.',
          'email.email' => 'Voer een geldig e-mailadres in.',
          'password.required' => 'Wachtwoord is verplicht.',
          'password.min' => 'Het wachtwoord moet minimaal 8 tekens bevatten.',
          'password.confirmed' => 'De wachtwoorden komen niet overeen.',
      ]);

      $status = Password::reset(
          $request->only('email', 'password', 'password_confirmation', 'token'),
          function (User $user, string $password) {
              $user->forceFill([
                  'password' => Hash::make($password)
              ])->setRememberToken(Str::random(60));

              $user->save();

              event(new PasswordReset($user));
          }
      );

      return $status === Password::PasswordReset
          ? redirect()->route('login')->with(['status' => __('Het wachtwoord van je account is gewijzigd.')])
          : back()->withErrors(['email' => [__($status)]]);
    }


    public function get () {
      return redirect()->route('login');
    }

}
