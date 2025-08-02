<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect('/winkel')->with('error', 'Je winkelwagen is leeg.');
        }

        return view('checkout.index', ['cart' => $cart]);
    }
}
