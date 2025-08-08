<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
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
}
