<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);

        // Normalize any legacy composite keys (e.g. "123-456") to product_id-only keys
        $normalized = [];
        foreach ($cart as $key => $item) {
            if (!empty($item['product_id'])) {
                $pid = (string) $item['product_id'];
                if (isset($normalized[$pid])) {
                    $normalized[$pid]['quantity'] = ($normalized[$pid]['quantity'] ?? 0) + ($item['quantity'] ?? 0);
                } else {
                    $normalized[$pid] = $item;
                }
            }
        }
        $cart = $normalized;

        $changed = false;
        foreach ($cart as $key => $item) {
            if (!empty($item['product_id'])) {
                $p = Product::find($item['product_id']);
                if ($p) {
                    $newPrice = $p->price;
                    if (!isset($item['price']) || $item['price'] != $newPrice) {
                        $cart[$key]['price'] = $newPrice;
                        $changed = true;
                    }
                    // Optional name/image sync
                    $cart[$key]['name'] = $p->title;
                    $cart[$key]['image_1'] = $p->image_1 ?? $cart[$key]['image_1'] ?? '';
                }
            }
        }
        if ($changed) {
            session(['cart' => $cart]);
        } else {
            // If we normalized keys, make sure session contains normalized cart
            session(['cart' => $cart]);
        }
        return view('cart.index', ['cart' => $cart]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:10',
        ], [
            'product_id.required' => 'Selecteer een product.',
            'product_id.exists' => 'Geselecteerd product bestaat niet.',
            'quantity.required' => 'Geef een hoeveelheid op.',
            'quantity.integer' => 'Hoeveelheid moet een heel getal zijn.',
            'quantity.min' => 'Minimaal :min item toevoegen.',
            'quantity.max' => 'Maximaal :max items toegestaan.',
        ]);

        $productId = $validated['product_id'];
        $quantity = $validated['quantity'];

        $product = Product::find($productId);
        if (!$product) {
            return back()->with('error', 'Product niet gevonden.');
        }

        $cart = session()->get('cart', []);

        // Use product_id string as cart key
        $cartKey = (string) $productId;
        $currentQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newQty = $currentQty + $quantity;
        if ($newQty > $product->stock) {
            return back()->withInput()->withErrors([
                'stock' => 'Je probeert meer toe te voegen dan op voorraad. Maximaal '.$product->stock.' beschikbaar.'
            ]);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $newQty;
            $cart[$cartKey]['price'] = $product->price; // ensure latest price
            $cart[$cartKey]['image_1'] = $product->image_1 ?? '';
            $cart[$cartKey]['name'] = $product->title;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->title,
                'price' => $product->price,
                'image_1' => $product->image_1 ?? '',
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
            $cartKey = (string) $productId;
            $product = Product::find($productId);

            if (isset($cart[$cartKey])) {
                if ($quantity > 0) {
                    if ($quantity > $product->stock) {
                        $errors[] = 'Geen voldoende voorraad meer van '.$product->title.'.<br>Er zijn nog maar '.$product->stock.' op voorraad.';
                        continue;
                    }
                    $cart[$cartKey]['quantity'] = $quantity;
                    $updated = true;
                } else {
                    unset($cart[$cartKey]);
                    $updated = true;
                }
            } else {
                $errors[] = 'Product niet gevonden in winkelwagen: '.($product ? $product->title : $cartKey);
            }
        }

        session(['cart' => $cart]);

        if (!empty($errors)) {
            return redirect()->back()->withInput()->withErrors(['stock' => implode('<br>', $errors)]);
        }

        if ($updated) {
            return redirect()->back()->with('success', 'Winkelwagen is bijgewerkt.');
        } else {
            return redirect()->back()->with('error', 'Geen producten bijgewerkt.');
        }
    }

    public function deleteItemFromCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        $productId = $request->input('product_id');
        $cartKey = (string) $productId;
        $cart = session()->get('cart', []);

        if (isset($cart[$cartKey])) {
            unset($cart[$cartKey]);
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
