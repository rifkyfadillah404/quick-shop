@extends('layouts.app')

@section('title', 'Shop')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Shop All Products</h1>
            <p class="text-gray-600">Discover our wide range of products</p>
        </div>

        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Sidebar Filters -->
            <div class="lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Filters</h3>

                    <form method="GET" action="{{ route('shop.index') }}" class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="Search products...">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                            <select id="category" name="category"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">All Categories</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->slug }}"
                                        {{ request('category') == $category->slug ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                            <div class="flex space-x-2">
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Min">
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    class="w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    placeholder="Max">
                            </div>
                        </div>

                        <!-- Sort -->
                        <div>
                            <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                            <select id="sort" name="sort"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price (Low
                                    to High)</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price
                                    (High to Low)</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First
                                </option>
                            </select>
                        </div>

                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">
                            Apply Filters
                        </button>
                    </form>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="lg:w-3/4">
                <!-- Results Info -->
                <div class="flex justify-between items-center mb-6">
                    <p class="text-gray-600">
                        Showing {{ $products->firstItem() ?? 0 }} - {{ $products->lastItem() ?? 0 }} of
                        {{ $products->total() }} products
                    </p>
                </div>

                @if ($products->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach ($products as $product)
                            <div
                                class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                                <div class="h-48 bg-gray-200 overflow-hidden">
                                    <img src="{{ $product->main_image }}" alt="{{ $product->name }}"
                                        class="w-full h-full object-cover hover:scale-105 transition duration-300">
                                </div>
                                <div class="p-4">
                                    <div class="text-sm text-indigo-600 mb-1">{{ $product->category->name }}</div>
                                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                                    <p class="text-gray-600 text-sm mb-3">{{ $product->short_description }}</p>

                                    <div class="flex items-center justify-between mb-4">
                                        <div class="flex items-center space-x-2">
                                            @if ($product->hasDiscount())
                                                <span
                                                    class="text-lg font-bold text-indigo-600">${{ number_format($product->sale_price, 2) }}</span>
                                                <span
                                                    class="text-sm text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                                            @else
                                                <span
                                                    class="text-lg font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                                            @endif
                                        </div>

                                        @if ($product->hasDiscount())
                                            <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded-full">
                                                -{{ $product->discount_percentage }}%
                                            </span>
                                        @endif
                                    </div>

                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.show', $product->slug) }}"
                                            class="flex-1 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 text-center text-sm">
                                            View Details
                                        </a>

                                        @if ($product->in_stock && $product->stock_quantity > 0)
                                            <form action="{{ route('cart.add', $product) }}" method="POST" class="flex-1">
                                                @csrf
                                                <input type="hidden" name="quantity" value="1">
                                                <button type="submit"
                                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300 text-sm">
                                                    Add to Cart
                                                </button>
                                            </form>
                                        @else
                                            <button disabled
                                                class="flex-1 bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed text-sm">
                                                Out of Stock
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-8">
                        {{ $products->appends(request()->query())->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 text-6xl mb-4">🔍</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No products found</h3>
                        <p class="text-gray-600 mb-4">Try adjusting your search or filter criteria</p>
                        <a href="{{ route('shop.index') }}"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition duration-300">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
