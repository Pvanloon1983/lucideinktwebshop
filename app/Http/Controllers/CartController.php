<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('cart.index', ['cart' => $cart]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        $productId = $request->input('product_id');        
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);        

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $product = Product::findOrFail($productId);
            $cart[$productId] = [
                'product_id' => $product->id,
                'name' => $product->title,
                'price' => $product->price,
                'image_1' => $product->image_1, 
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);

        return redirect()->back()->with('success_add_to_cart', 'Product toegevoegd aan winkelwagen!');
    }


    public function updateCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            if ($quantity > 0) {
                $cart[$productId]['quantity'] = $quantity;
            } else {
                unset($cart[$productId]);
            }
            session(['cart' => $cart]);
            return redirect()->back()->with('success', 'Winkelwagen is bijgewerkt');
        } else {
            return redirect()->back()->with('error', 'Product niet gevonden in winkelwagen');
        }
    }

    public function deleteItemFromCart(Request $request) 
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
            return redirect()->back()->with('success', 'Product is verwijderd uit winkelwagen');
        } else {
            return redirect()->back()->with('error', 'Product niet gevonden in winkelwagen');
        }
    }

    public function removeCart(Request $request) 
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Winkelwagen is geleegd');
    }
}
