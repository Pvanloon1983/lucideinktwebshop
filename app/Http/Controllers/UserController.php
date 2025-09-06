<?php

namespace App\Http\Controllers;

use App\Mail\NewUserMail;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'role:admin']);
  }

    public function index()
    {
      $this->authorize('viewAny', User::class);
      $users = User::paginate(10);
      return view('users.index', ['users' => $users]);
    }

    public function show(String $id)
    {
      $user = User::findOrFail($id);
      $this->authorize('view', $user);
      $customer = Customer::where('billing_email', $user->email)->first();
      return view('users.show', [
        'user' => $user,
        'customer' => $customer
      ]);
    }

    public function create()
    {
      $this->authorize('create', User::class);
      return view('users.create');
    }

    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $validated = $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email:rfc,dns|unique:users,email|confirmed',
        'password' => 'required|string|min:8|confirmed',
        'user_role' => [
                'required',
                'in:admin,user'
              ]
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
        'user_role.required' => 'Selecteer een geldige rol.',
        'user_role.in' => 'De gekozen rol is ongeldig.',
        ]);

      $user = User::create([
        'first_name' => $validated['first_name'],
        'last_name' => $validated['last_name'],
        'user_role' => $validated['user_role'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
      ]);

      // Fire the Registered event
      event(new Registered($user));

      // Send registration email
      Mail::to($user->email)->send(new NewUserMail($user));

      // Optionally log the user in
      // auth()->login($user);

      // Redirect to login page
      return redirect()->route('userIndex')->with('success', 'Nieuw gebruiker is toegevoegd!');
    }

  public function update(Request $request, string $id)
  {
    $user = User::findOrFail($id);
    $this->authorize('update', $user);
    $request->validate([
      'user_role' => [
        'required',
        'in:admin,user'
      ]
    ], [
      'user_role.required' => 'Selecteer een geldige rol.',
      'user_role.in' => 'De gekozen role is ongeldig.',
    ]);

    $user->update(['role' => $request->input('user_role')]);

    if ($user->role == 'user') {
      return redirect()->route('dashboard')->with('success', 'Gebruiker ' . $user->first_name . ' ' . $user->last_name . ' is bijgewerkt.');
    } else {
      return back()->with('success', 'Gebruiker is bijgewerkt.');
    }
  }
}
