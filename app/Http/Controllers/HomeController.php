<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::active()->take(6)->get();
        $featuredProducts = Product::active()
            ->inStock()
            ->take(8)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }
}
