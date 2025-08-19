<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
  public function __construct()
  {
    $this->middleware(['auth', 'role:admin']);
  }
    public function index()
    {
      $users = User::paginate(10);

      return view('users.index', ['users' => $users]);
    }

    public function show(String $id)
    {
      $user = User::findOrFail($id);
      $customer = Customer::where('billing_email', $user->email)->first();

      return view('users.show', [
        'user' => $user,
        'customer' => $customer
      ]);
    }

    public function update(Request $request, string $id)
		{
			$request->validate([
				'user-role' => [
					'required',
					'in:admin,user'
				]
			], [
				'user-role.required' => 'Selecteer een geldige rol.',
				'user-role.in' => 'De gekozen role is ongeldig.',
			]);

			$user = User::findOrFail($id);

			$user->update(['role' => $request->input('user-role')]);

      if ($user->role == 'user') {
        return redirect()->route('dashboard')->with('success', 'Gebruiker ' . $user->first_name . ' ' . $user->last_name . ' is bijgewerkt.');
      } else {
        return back()->with('success', 'Gebruiker is bijgewerkt.');
      }			

		}
}
