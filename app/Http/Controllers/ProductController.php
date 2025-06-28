<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(Product $product)
    {
        // Check if product is active and in stock
        if (!$product->is_active || !$product->in_stock) {
            abort(404);
        }

        // Get related products from same category
        $relatedProducts = Product::active()
            ->inStock()
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('shop.product-detail', compact('product', 'relatedProducts'));
    }
}
