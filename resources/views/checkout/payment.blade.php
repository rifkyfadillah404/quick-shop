@extends('layouts.app')

@section('title', 'Payment - Order #' . $order->order_number)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Complete Your Payment</h1>
            <p class="text-gray-600">Order #{{ $order->order_number }}</p>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Payment Information</h2>
            
            @if($order->payment_method === 'xendit_va' && $order->payment_data)
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                    <h3 class="font-semibold text-blue-900 mb-2">Virtual Account Payment</h3>
                    <div class="space-y-2">
                        <p><strong>Bank:</strong> {{ strtoupper($order->payment_data['bank_code']) }}</p>
                        <p><strong>Virtual Account Number:</strong> 
                            <span class="font-mono text-lg">{{ $order->payment_data['account_number'] }}</span>
                            <button onclick="copyToClipboard('{{ $order->payment_data['account_number'] }}')" 
                                    class="ml-2 text-blue-600 hover:text-blue-800">
                                📋 Copy
                            </button>
                        </p>
                        <p><strong>Amount:</strong> Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                        <p class="text-sm text-gray-600">Transfer the exact amount to complete your payment</p>
                    </div>
                </div>
            @endif

            @if($order->payment_url)
                <div class="text-center">
                    <a href="{{ $order->payment_url }}" target="_blank"
                       class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300">
                        Continue Payment
                    </a>
                </div>
            @endif
        </div>

        <!-- Order Summary -->
        <div class="bg-white rounded-lg shadow-md p-6">
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

        <div class="text-center mt-8">
            <a href="{{ route('account.orders.show', $order) }}" 
               class="text-indigo-600 hover:text-indigo-800">
                View Order Details
            </a>
        </div>
    </div>

    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Virtual Account number copied to clipboard!');
            });
        }
    </script>
@endsection
