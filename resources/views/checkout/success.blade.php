@extends('layouts.app')

@section('title', 'Payment Success')

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-8">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Payment Successful!</h1>
            <p class="text-gray-600">Thank you for your order. Your payment has been processed successfully.</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Details</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p><strong>Order Number:</strong> {{ $order->order_number }}</p>
                    <p><strong>Order Date:</strong> {{ $order->created_at->format('d M Y, H:i') }}</p>
                    <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                    <p><strong>Payment Status:</strong> 
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-sm">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p><strong>Total Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                    <p><strong>Order Status:</strong> 
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Order Items</h2>
            
            <div class="space-y-4">
                @foreach($order->orderItems as $item)
                    <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                        <div class="flex-1">
                            <h3 class="font-semibold">{{ $item->product_name }}</h3>
                            <p class="text-gray-600">SKU: {{ $item->product_sku }}</p>
                            <p class="text-gray-600">Quantity: {{ $item->quantity }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold">Rp {{ number_format($item->total, 0, ',', '.') }}</p>
                            <p class="text-sm text-gray-600">Rp {{ number_format($item->price, 0, ',', '.') }} each</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h2 class="text-lg font-semibold text-blue-900 mb-2">What's Next?</h2>
            <ul class="text-blue-800 space-y-1">
                <li>• We'll process your order within 1-2 business days</li>
                <li>• You'll receive an email confirmation with tracking information</li>
                <li>• Your order will be shipped to: {{ $order->shipping_address['address'] }}, {{ $order->shipping_address['city'] }}</li>
            </ul>
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
