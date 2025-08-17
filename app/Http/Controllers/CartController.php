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
        $product = Product::find($productId);
        $cart = session()->get('cart', []);

        $huidigeAantal = isset($cart[$productId]) ? $cart[$productId]['quantity'] : 0;
        $nieuwAantal = $huidigeAantal + $quantity;
        if ($nieuwAantal > $product->stock) {
            return back()->withInput()->withErrors([
                'stock' => 'Je probeert meer toe te voegen dan op voorraad. Maximaal ' . $product->stock . ' beschikbaar.'
            ]);
        }

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $nieuwAantal;
        } else {
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
            'products' => 'required|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);
        $errors = [];
        $updated = false;

        foreach ($request->input('products') as $item) {
            $productId = $item['product_id'];
            $quantity = $item['quantity'];
            $product = Product::find($productId);

            if (isset($cart[$productId])) {
                if ($quantity > 0) {
                    if ($quantity > $product->stock) {
                        $errors[] = 'Geen voldoende voorraad meer van ' . $product->title . '.<br>Er zijn nog maar ' . $product->stock . ' op voorraad.';
                        continue;
                    }
                    $cart[$productId]['quantity'] = $quantity;
                    $updated = true;
                } else {
                    unset($cart[$productId]);
                    $updated = true;
                }
            } else {
                $errors[] = 'Product niet gevonden in winkelwagen: ' . ($product ? $product->title : $productId);
            }
        }

        session(['cart' => $cart]);

        if (!empty($errors)) {
            return redirect()->back()->withInput()->withErrors(['stock' => implode('<br>', $errors)]);
        }

        if ($updated) {
            return redirect()->back()->with('success', 'Winkelwagen is bijgewerkt');
        } else {
            return redirect()->back()->with('error', 'Geen producten bijgewerkt');
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
