@extends('layouts.app')

@section('title', 'Order #' . $order->order_number)

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
                        <a href="{{ route('account.orders') }}" class="ml-1 text-gray-700 hover:text-indigo-600 md:ml-2">My
                            Orders</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-500 md:ml-2">Order #{{ $order->order_number }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Order Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Order #{{ $order->order_number }}</h1>
                    <p class="text-gray-600">Placed on {{ $order->created_at->format('M d, Y \a\t g:i A') }}</p>
                </div>
                <div class="mt-4 lg:mt-0 flex items-center space-x-4">
                    <span
                        class="px-4 py-2 rounded-full text-sm font-medium
                    @if ($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                    @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800 @endif">
                        {{ ucfirst($order->status) }}
                    </span>
                    <span
                        class="px-4 py-2 rounded-full text-sm font-medium
                    @if ($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                    @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                    @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800 @endif">
                        Payment: {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach ($order->orderItems as $item)
                            <div class="p-6 flex items-center space-x-4">
                                <!-- Product Image -->
                                <div class="flex-shrink-0 w-20 h-20 bg-gray-200 rounded-md overflow-hidden">
                                    @if ($item->product)
                                        <img src="{{ $item->product->main_image }}" alt="{{ $item->product_name }}"
                                            class="w-full h-full object-cover">
                                    @else
                                        <img src="{{ asset('images/no-image.svg') }}" alt="{{ $item->product_name }}"
                                            class="w-full h-full object-cover">
                                    @endif
                                </div>

                                <!-- Product Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ $item->product_name }}</h3>
                                    <p class="text-sm text-gray-500">SKU: {{ $item->product_sku }}</p>
                                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>

                                    @if ($item->product)
                                        <a href="{{ route('products.show', $item->product->slug) }}"
                                            class="text-sm text-indigo-600 hover:text-indigo-800">
                                            View Product →
                                        </a>
                                    @endif
                                </div>

                                <!-- Price -->
                                <div class="text-right">
                                    <div class="text-sm text-gray-600">Unit Price</div>
                                    <div class="text-lg font-semibold text-gray-900">${{ number_format($item->price, 2) }}
                                    </div>
                                    <div class="text-sm text-gray-600">Total: ${{ number_format($item->total, 2) }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary & Details -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Order Summary -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-semibold">${{ number_format($order->subtotal, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-semibold">${{ number_format($order->shipping_amount, 2) }}</span>
                        </div>

                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-semibold">${{ number_format($order->tax_amount, 2) }}</span>
                        </div>

                        <div class="border-t pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span
                                    class="text-lg font-bold text-indigo-600">${{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h2>

                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Method</span>
                            <span class="font-semibold">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Status</span>
                            <span
                                class="font-semibold
                            @if ($order->payment_status === 'pending') text-yellow-600
                            @elseif($order->payment_status === 'paid') text-green-600
                            @elseif($order->payment_status === 'failed') text-red-600
                            @elseif($order->payment_status === 'refunded') text-gray-600 @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Shipping Address</h2>

                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="font-semibold text-gray-900">{{ $order->shipping_address['name'] }}</div>
                        <div>{{ $order->shipping_address['address'] }}</div>
                        <div>{{ $order->shipping_address['city'] }}, {{ $order->shipping_address['state'] }}
                            {{ $order->shipping_address['zip'] }}</div>
                        <div>{{ $order->shipping_address['country'] }}</div>
                        <div class="mt-2">
                            <span class="font-medium">Phone:</span> {{ $order->shipping_address['phone'] }}
                        </div>
                        <div>
                            <span class="font-medium">Email:</span> {{ $order->shipping_address['email'] }}
                        </div>
                    </div>
                </div>

                <!-- Billing Information -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Billing Address</h2>

                    <div class="text-sm text-gray-600 space-y-1">
                        <div class="font-semibold text-gray-900">{{ $order->billing_address['name'] }}</div>
                        <div>{{ $order->billing_address['address'] }}</div>
                        <div>{{ $order->billing_address['city'] }}, {{ $order->billing_address['state'] }}
                            {{ $order->billing_address['zip'] }}</div>
                        <div>{{ $order->billing_address['country'] }}</div>
                        <div class="mt-2">
                            <span class="font-medium">Phone:</span> {{ $order->billing_address['phone'] }}
                        </div>
                        <div>
                            <span class="font-medium">Email:</span> {{ $order->billing_address['email'] }}
                        </div>
                    </div>
                </div>

                @if ($order->notes)
                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Notes</h2>
                        <p class="text-sm text-gray-600">{{ $order->notes }}</p>
                    </div>
                @endif

                <!-- Order Actions -->
                @if ($order->canBeCancelled())
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Actions</h2>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Cancel Order
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Once cancelled, this order cannot be restored. Stock will be returned to
                                            inventory.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('account.orders.cancel', $order) }}" method="POST"
                            onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')">
                            @csrf
                            <button type="submit"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition duration-300 font-medium">
                                Cancel Order
                            </button>
                        </form>
                    </div>
                @else
                    @if ($order->payment_status === 'paid')
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-blue-800">
                                        Order Cannot Be Cancelled
                                    </h3>
                                    <div class="mt-2 text-sm text-blue-700">
                                        <p>This order has been approved by admin and cannot be cancelled. Please contact
                                            customer service if you need assistance.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
