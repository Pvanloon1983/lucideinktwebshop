<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::with('category')
            ->where('is_published', 1)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 products per page

        return view('shop.index', ['products' => $products]);
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();

        return view('shop.show', ['product' => $product]);
    }

}
