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

}
