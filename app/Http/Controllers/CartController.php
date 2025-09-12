<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        $cart = session()->get('cart', []);
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
        }
        return view('cart.index', ['cart' => $cart]);
    }

    public function addToCart(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_copy_id' => 'required|exists:product_copies,id',
            'quantity' => 'required|integer|min:1|max:1000',
        ]);

        $baseProductId = $request->input('product_id');
        $productCopyId = $request->input('product_copy_id');
        $quantity = $request->input('quantity');
        $baseProduct = Product::find($baseProductId);
        if (!$baseProduct) {
            return back()->with('error', 'Product niet gevonden.');
        }

        $cart = session()->get('cart', []);

        // All variant products share the same slug; pick the one that matches the chosen product_copy_id if it exists
        $variant = Product::where('base_slug', $baseProduct->base_slug)
            ->where('product_copy_id', $productCopyId)
            ->whereNull('deleted_at')
            ->first();

        // If no explicit variant product row, fallback to base product
        $product = $variant ?: $baseProduct;

        // Validate that the chosen product_copy actually belongs to this base_slug family
        $validCopyIds = Product::where('base_slug', $baseProduct->base_slug)
            ->whereNull('deleted_at')
            ->pluck('product_copy_id')
            ->filter()
            ->unique();
        if (!$validCopyIds->contains($productCopyId)) {
            return back()->withInput()->withErrors([
                'product_copy_id' => 'Het gekozen exemplaar hoort niet bij dit product.'
            ]);
        }

        // Validate published copy
        $productCopy = \App\Models\ProductCopy::where('id', $productCopyId)
            ->where('is_published', 1)
            ->first();
        if (!$productCopy) {
            return back()->withInput()->withErrors([
                'product_copy_id' => 'Het gekozen exemplaar bestaat niet of is niet gepubliceerd.'
            ]);
        }

        // Use variant product id for cart key to differentiate prices per variant
        $productId = $product->id;
        $cartKey = $productId . '-' . $productCopyId;
        $currentQty = isset($cart[$cartKey]) ? $cart[$cartKey]['quantity'] : 0;
        $newQty = $currentQty + $quantity;
        if ($newQty > $product->stock) {
            return back()->withInput()->withErrors([
                'stock' => 'Je probeert meer toe te voegen dan op voorraad. Maximaal ' . $product->stock . ' beschikbaar.'
            ]);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] = $newQty;
            $cart[$cartKey]['product_copy_id'] = $productCopyId;
            $cart[$cartKey]['product_copy_name'] = $productCopy->name;
            $cart[$cartKey]['image_1'] = $product->image_1 ?? '';
            $cart[$cartKey]['price'] = $product->price; // ensure latest price
            $cart[$cartKey]['name'] = $product->title;
        } else {
            $cart[$cartKey] = [
                'product_id' => $product->id,
                'name' => $product->title,
                'price' => $product->price,
                'image_1' => $product->image_1 ?? '',
                'quantity' => $quantity,
                'product_copy_id' => $productCopyId,
                'product_copy_name' => $productCopy->name,
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
            'products.*.product_copy_id' => 'required|exists:product_copies,id',
            'products.*.quantity' => 'required|integer|min:0',
        ]);

        $cart = session()->get('cart', []);
        $errors = [];
        $updated = false;

        foreach ($request->input('products') as $item) {
            $productId = $item['product_id'];
            $productCopyId = $item['product_copy_id'];
            $quantity = $item['quantity'];
            $cartKey = $productId . '-' . $productCopyId;
            $product = Product::find($productId);

            if (isset($cart[$cartKey])) {
                if ($quantity > 0) {
                    if ($quantity > $product->stock) {
                        $errors[] = 'Geen voldoende voorraad meer van ' . $product->title . '.<br>Er zijn nog maar ' . $product->stock . ' op voorraad.';
                        continue;
                    }
                    $cart[$cartKey]['quantity'] = $quantity;
                    $updated = true;
                } else {
                    unset($cart[$cartKey]);
                    $updated = true;
                }
            } else {
                $errors[] = 'Product niet gevonden in winkelwagen: ' . ($product ? $product->title : $cartKey);
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
            'product_copy_id' => 'required|exists:product_copies,id',
        ]);

        $productId = $request->input('product_id');
        $productCopyId = $request->input('product_copy_id');
        $cartKey = $productId . '-' . $productCopyId;
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
