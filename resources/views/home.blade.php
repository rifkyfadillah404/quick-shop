@extends('layouts.app')

@section('title', 'Home')

@section('content')
    <!-- Hero Section -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-bold mb-6">
                    Welcome to QuickShop
                </h1>
                <p class="text-xl md:text-2xl mb-8 text-indigo-100">
                    Discover amazing products at unbeatable prices
                </p>
                <a href="{{ route('shop.index') }}"
                    class="bg-white text-indigo-600 px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-100 transition duration-300">
                    Shop Now
                </a>
            </div>
        </div>
    </div>

    <!-- Featured Categories -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-900 mb-4">Shop by Category</h2>
            <p class="text-gray-600">Browse our wide selection of products</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach ($categories as $category)
                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                    <div class="h-48 bg-gradient-to-br from-indigo-400 to-purple-500"></div>
                    <div class="p-6">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $category->name }}</h3>
                        <p class="text-gray-600 mb-4">{{ $category->description }}</p>
                        <a href="{{ route('categories.show', $category->slug) }}"
                            class="text-indigo-600 hover:text-indigo-800 font-medium">
                            View Products →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Featured Products -->
    <div class="bg-gray-100 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-gray-900 mb-4">Featured Products</h2>
                <p class="text-gray-600">Check out our best-selling items</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($featuredProducts as $product)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                        <div class="h-48 bg-gray-200 overflow-hidden">
                            @if ($product->main_image)
                                <img src="{{ $product->main_image }}" alt="{{ $product->name }}" loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover hover:scale-105 transition duration-300"
                                    style="content-visibility: auto;">
                            @else
                                <div class="w-full h-full flex items-center justify-center">
                                    <span class="text-gray-400">No Image</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 text-sm mb-3">{{ $product->short_description }}</p>

                            <div class="flex items-center justify-between">
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

                            <div class="mt-4 space-y-2">
                                @if ($product->in_stock && $product->stock_quantity > 0)
                                    <form action="{{ route('cart.add', $product) }}" method="POST" class="w-full">
                                        @csrf
                                        <input type="hidden" name="quantity" value="1">
                                        <button type="submit"
                                            class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition duration-300 font-medium">
                                            Add to Cart
                                        </button>
                                    </form>
                                @else
                                    <button disabled
                                        class="w-full bg-gray-300 text-gray-500 px-4 py-2 rounded-md cursor-not-allowed font-medium">
                                        Out of Stock
                                    </button>
                                @endif

                                <a href="{{ route('products.show', $product->slug) }}"
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 text-center block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('shop.index') }}"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition duration-300">
                    View All Products
                </a>
            </div>
        </div>
    </div>


@endsection
