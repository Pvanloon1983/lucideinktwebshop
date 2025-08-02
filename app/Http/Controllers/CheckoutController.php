<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function index()
    {
       $cart = session()->get('cart', []);

        return view('checkout.index', ['cart' => $cart]);
    }
}
