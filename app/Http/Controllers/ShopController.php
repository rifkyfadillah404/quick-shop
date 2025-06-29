<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        // Cache categories for 1 hour (they don't change often)
        $categories = cache()->remember('shop_categories', 3600, function () {
            return Category::active()
                ->select('id', 'name', 'slug')
                ->get();
        });

        // Build query with optimized select and eager loading
        $query = Product::active()
            ->inStock()
            ->with('category:id,name,slug')
            ->select('id', 'name', 'slug', 'price', 'images', 'category_id', 'stock_quantity', 'in_stock', 'created_at');

        // Search with index optimization
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $searchTerm . '%');
            });
        }

        // Category filter - more efficient with join
        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Price filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        // Use regular pagination to support total count
        $products = $query->paginate(12);

        return view('shop.index', compact('products', 'categories'));
    }
}
