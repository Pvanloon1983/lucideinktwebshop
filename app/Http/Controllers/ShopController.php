<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 products per page

        return view('shop.index', ['products' => $products]);
    }

    public function show(string $id)
    {
        $product = Product::findOrFail($id);

        return view('shop.show', ['product' => $product]);
    }

    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $quantity = 1;
        // $quantity = $request->input('quantity', 1);

        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $product = Product::findOrFail($productId);
            $cart[$productId] = [
                'name' => $product->title,
                'price' => $product->price,
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);

        // session()->forget('cart');

        return redirect()->back()->with('success', 'Product toegevoegd aan winkelwagen!');
    }
}
