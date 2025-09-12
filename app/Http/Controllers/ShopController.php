<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCopy as ProductCopy;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        // Group products by base_slug, only show parent/base product for each group
        $products = Product::with('category')
            ->where('is_published', 1)
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique('base_slug');

        return view('shop.index', ['products' => $products]);
    }

    public function show(string $slug)
    {
        // Find the parent/base product by base_slug
        $product = Product::where('base_slug', $slug)
            ->firstOrFail();

        // Get all exemplaren (variants) for this base product
        $exemplaren = Product::where('base_slug', $slug)
            ->where('is_published', 1)
            ->get();

        // Get all unique product_copy_id values
        $productCopyIds = $exemplaren->pluck('product_copy_id')->unique()->filter();

        // Fetch all published, not deleted ProductCopy records for these IDs
        $productCopies = [];
        if ($productCopyIds->isNotEmpty()) {
            $productCopies = ProductCopy::whereIn('id', $productCopyIds)
                ->where('is_published', 1)
                ->whereNull('deleted_at')
                ->get();
        }

        return view('shop.show', [
            'product' => $product,
            'productCopies' => $productCopies,
            'exemplaren' => $exemplaren,
        ]);
    }

}
