<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        // Cache categories for 1 hour
        $categories = cache()->remember('home_categories', 3600, function () {
            return Category::active()
                ->select('id', 'name', 'slug', 'image')
                ->take(6)
                ->get();
        });

        // Cache featured products for 30 minutes
        $featuredProducts = cache()->remember('featured_products', 1800, function () {
            return Product::active()
                ->inStock()
                ->with('category:id,name,slug')
                ->select('id', 'name', 'slug', 'price', 'images', 'category_id', 'stock_quantity', 'in_stock')
                ->take(8)
                ->get();
        });

        return view('home', compact('categories', 'featuredProducts'));
    }
}
