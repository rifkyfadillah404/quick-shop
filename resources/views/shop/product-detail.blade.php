@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('home') }}" class="text-gray-700 hover:text-indigo-600">Home</a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('shop.index') }}"
                            class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">Shop</a>
                    </div>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('categories.show', $product->category->slug) }}"
                            class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">{{ $product->category->name }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">{{ $product->name }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Product Details -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">
            <!-- Product Images -->
            <div>
                <!-- Main Image -->
                <div class="bg-gray-200 rounded-lg h-96 overflow-hidden mb-4">
                    <img id="mainImage" src="{{ $product->main_image }}" alt="{{ $product->name }}"
                        class="w-full h-full object-cover">
                </div>

                <!-- Thumbnail Images -->
                @if (count($product->all_images) > 1)
                    <div class="grid grid-cols-4 gap-2">
                        @foreach ($product->all_images as $index => $image)
                            <div class="bg-gray-200 rounded-lg h-20 overflow-hidden cursor-pointer hover:opacity-75 transition duration-300 {{ $index === 0 ? 'ring-2 ring-indigo-500' : '' }}"
                                onclick="changeMainImage('{{ $image }}', this)">
                                <img src="{{ $image }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Info -->
            <div>
                <div class="text-sm text-indigo-600 mb-2">{{ $product->category->name }}</div>
                <h1 class="text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>

                <!-- Price -->
                <div class="flex items-center space-x-4 mb-6">
                    @if ($product->hasDiscount())
                        <span
                            class="text-3xl font-bold text-indigo-600">${{ number_format($product->sale_price, 2) }}</span>
                        <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                        <span class="bg-red-100 text-red-800 text-sm px-3 py-1 rounded-full">
                            Save {{ $product->discount_percentage }}%
                        </span>
                    @else
                        <span class="text-3xl font-bold text-indigo-600">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <!-- Short Description -->
                @if ($product->short_description)
                    <p class="text-gray-600 mb-6">{{ $product->short_description }}</p>
                @endif

                <!-- Stock Status -->
                <div class="mb-6">
                    @if ($product->in_stock && $product->stock_quantity > 0)
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            In Stock ({{ $product->stock_quantity }} available)
                        </span>
                    @else
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                            Out of Stock
                        </span>
                    @endif
                </div>

                <!-- SKU -->
                <div class="text-sm text-gray-500 mb-6">
                    SKU: {{ $product->sku }}
                </div>

                <!-- Add to Cart -->
                @if ($product->in_stock && $product->stock_quantity > 0)
                    <form action="{{ route('cart.add', $product) }}" method="POST" class="mb-6">
                        @csrf
                        <div class="flex items-center space-x-4 mb-4">
                            <label for="quantity" class="text-sm font-medium text-gray-700">Quantity:</label>
                            <input type="number" id="quantity" name="quantity" value="1" min="1"
                                max="{{ $product->stock_quantity }}"
                                class="w-20 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <button type="submit"
                            class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300 font-medium">
                            Add to Cart
                        </button>
                    </form>
                @else
                    <button disabled
                        class="w-full bg-gray-300 text-gray-500 px-6 py-3 rounded-md cursor-not-allowed font-medium">
                        Out of Stock
                    </button>
                @endif

                <!-- Product Features -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Product Features</h3>
                    <ul class="space-y-2 text-gray-600">
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Free shipping on orders over $50
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            30-day return policy
                        </li>
                        <li class="flex items-center">
                            <svg class="w-4 h-4 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            Secure payment processing
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Product Description -->
        @if ($product->description)
            <div class="bg-white rounded-lg shadow-md p-6 mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Product Description</h2>
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($product->description)) !!}
                </div>
            </div>
        @endif

        <!-- Related Products -->
        @if ($relatedProducts->count() > 0)
            <div class="mb-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Related Products</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition duration-300">
                            <div class="h-48 bg-gray-200 overflow-hidden">
                                <img src="{{ $relatedProduct->main_image }}" alt="{{ $relatedProduct->name }}"
                                    class="w-full h-full object-cover hover:scale-105 transition duration-300">
                            </div>
                            <div class="p-4">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $relatedProduct->name }}</h3>
                                <p class="text-gray-600 text-sm mb-3">{{ $relatedProduct->short_description }}</p>

                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-2">
                                        @if ($relatedProduct->hasDiscount())
                                            <span
                                                class="text-lg font-bold text-indigo-600">${{ number_format($relatedProduct->sale_price, 2) }}</span>
                                            <span
                                                class="text-sm text-gray-500 line-through">${{ number_format($relatedProduct->price, 2) }}</span>
                                        @else
                                            <span
                                                class="text-lg font-bold text-indigo-600">${{ number_format($relatedProduct->price, 2) }}</span>
                                        @endif
                                    </div>
                                </div>

                                <a href="{{ route('products.show', $relatedProduct->slug) }}"
                                    class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 text-center block">
                                    View Details
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <script>
        function changeMainImage(imageSrc, thumbnailElement) {
            // Update main image
            document.getElementById('mainImage').src = imageSrc;

            // Remove ring from all thumbnails
            document.querySelectorAll('.grid .ring-2').forEach(function(element) {
                element.classList.remove('ring-2', 'ring-indigo-500');
            });

            // Add ring to clicked thumbnail
            thumbnailElement.classList.add('ring-2', 'ring-indigo-500');
        }
    </script>
@endsection
