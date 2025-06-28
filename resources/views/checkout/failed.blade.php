@extends('layouts.app')

@section('title', 'Payment Failed')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                <svg class="h-8 w-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Failed</h1>
            <p class="text-gray-600">Unfortunately, your payment could not be processed. Please try again.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Information</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    <p><strong>Payment Status:</strong> 
                        <span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-sm">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    <p><strong>Order Status:</strong> 
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Retry Payment Options -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-yellow-900 mb-2">What Can You Do?</h2>
            <ul class="text-yellow-800 space-y-2">
                <li>• Check your payment details and try again</li>
                <li>• Try a different payment method</li>
                <li>• Contact your bank if using a card</li>
                <li>• Contact our customer support for assistance</li>
            </ul>
        </div>

        @if($order->payment_url)
            <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Retry Payment</h2>
                <p class="text-gray-600 mb-4">You can try to complete your payment again using the link below:</p>
                <a href="{{ $order->payment_url }}" target="_blank"
                   class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300">
                    Retry Payment
                </a>
            </div>
        @endif

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Summary</h2>
            
            <div class="space-y-3 mb-6">
                @foreach($order->orderItems as $item)
                    <div class="flex justify-between">
                        <span>{{ $item->product_name }} ({{ $item->quantity }}x)</span>
                        <span>Rp {{ number_format($item->total, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>

            <div class="border-t pt-4 space-y-2">
                <div class="flex justify-between">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($order->shipping_amount > 0)
                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span>Rp {{ number_format($order->shipping_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                @if($order->tax_amount > 0)
                    <div class="flex justify-between">
                        <span>Tax</span>
                        <span>Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</span>
                    </div>
                @endif
                <div class="flex justify-between font-bold text-lg border-t pt-2">
                    <span>Total</span>
                    <span>Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <div class="text-center space-x-4">
            <a href="{{ route('account.orders.show', $order) }}" 
               class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300">
                View Order Details
            </a>
            <a href="{{ route('shop.index') }}" 
               class="inline-block bg-gray-200 text-gray-800 px-6 py-3 rounded-md hover:bg-gray-300 transition duration-300">
                Continue Shopping
            </a>
        </div>
    </div>
@endsection
