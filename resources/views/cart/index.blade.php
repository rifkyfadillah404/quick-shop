@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Shopping Cart</h1>

        @if ($cartItems->count() > 0)
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Cart Items ({{ $cartItems->count() }})</h2>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @foreach ($cartItems as $item)
                                <div class="p-6 flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-md overflow-hidden">
                                        <img src="{{ $item->product->main_image }}" alt="{{ $item->product->name }}"
                                            class="w-full h-full object-cover">
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900">
                                            <a href="{{ route('products.show', $item->product->slug) }}"
                                                class="hover:text-indigo-600">
                                                {{ $item->product->name }}
                                            </a>
                                        </h3>
                                        <p class="text-sm text-gray-600">{{ $item->product->short_description }}</p>
                                        <p class="text-sm text-gray-500">SKU: {{ $item->product->sku }}</p>

                                        <!-- Price -->
                                        <div class="mt-2">
                                            @if ($item->product->hasDiscount())
                                                <span
                                                    class="text-lg font-bold text-indigo-600">${{ number_format($item->product->sale_price, 2) }}</span>
                                                <span
                                                    class="text-sm text-gray-500 line-through ml-2">${{ number_format($item->product->price, 2) }}</span>
                                            @else
                                                <span
                                                    class="text-lg font-bold text-indigo-600">${{ number_format($item->product->price, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        @auth
                                            <form action="{{ route('cart.update', $item) }}" method="POST"
                                                class="flex items-center space-x-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="number" name="quantity" value="{{ $item->quantity }}"
                                                    min="1" max="{{ $item->product->stock_quantity }}"
                                                    class="w-16 px-2 py-1 border border-gray-300 rounded-md text-center focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                                <button type="submit"
                                                    class="bg-indigo-600 text-white px-3 py-1 rounded-md hover:bg-indigo-700 transition duration-300 text-sm">
                                                    Update
                                                </button>
                                            </form>
                                        @else
                                            <div class="flex items-center space-x-2">
                                                <span class="text-sm text-gray-600">Qty: {{ $item->quantity }}</span>
                                            </div>
                                        @endauth
                                    </div>

                                    <!-- Total Price -->
                                    <div class="text-right">
                                        <div class="text-lg font-bold text-gray-900">
                                            ${{ number_format($item->quantity * $item->product->display_price, 2) }}
                                        </div>
                                    </div>

                                    <!-- Remove Button -->
                                    <div>
                                        @auth
                                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="text-red-600 hover:text-red-800 transition duration-300">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </form>
                                        @else
                                            <a href="{{ route('cart.remove.session', $item->id) }}"
                                                class="text-red-600 hover:text-red-800 transition duration-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </a>
                                        @endauth
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold">
                                    @if ($subtotal >= 50)
                                        <span class="text-green-600">Free</span>
                                    @else
                                        $5.00
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-semibold">${{ number_format($subtotal * 0.1, 2) }}</span>
                            </div>

                            <div class="border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-lg font-bold text-indigo-600">
                                        ${{ number_format($subtotal + ($subtotal >= 50 ? 0 : 5) + $subtotal * 0.1, 2) }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        @if ($subtotal < 50)
                            <div class="mt-4 p-3 bg-blue-50 rounded-md">
                                <p class="text-sm text-blue-800">
                                    Add ${{ number_format(50 - $subtotal, 2) }} more for free shipping!
                                </p>
                            </div>
                        @endif

                        <div class="mt-6 space-y-3">
                            @auth
                                <a href="{{ route('checkout.index') }}"
                                    class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300 text-center block font-medium">
                                    Proceed to Checkout
                                </a>
                            @else
                                <a href="{{ route('login') }}"
                                    class="w-full bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300 text-center block font-medium">
                                    Login to Checkout
                                </a>
                            @endauth

                            <a href="{{ route('shop.index') }}"
                                class="w-full bg-gray-200 text-gray-800 px-6 py-3 rounded-md hover:bg-gray-300 transition duration-300 text-center block font-medium">
                                Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Empty Cart -->
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">🛒</div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Your cart is empty</h2>
                <p class="text-gray-600 mb-8">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('shop.index') }}"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-md hover:bg-indigo-700 transition duration-300 font-medium">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
@endsection
