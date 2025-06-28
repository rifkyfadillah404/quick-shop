@extends('layouts.app')

@section('title', 'My Orders')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <a href="{{ route('shop.index') }}"
                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300">
                Continue Shopping
            </a>
        </div>

        @if ($orders->count() > 0)
            <div class="space-y-6">
                @foreach ($orders as $order)
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <!-- Order Header -->
                        <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">Order #{{ $order->order_number }}
                                        </h3>
                                        <p class="text-sm text-gray-600">Placed on
                                            {{ $order->created_at->format('M d, Y') }}</p>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium
                                        @if ($order->status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->status === 'processing') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'shipped') bg-purple-100 text-purple-800
                                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                        @elseif($order->status === 'cancelled') bg-red-100 text-red-800 @endif">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                        <span
                                            class="px-3 py-1 rounded-full text-xs font-medium
                                        @if ($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                        @elseif($order->payment_status === 'paid') bg-green-100 text-green-800
                                        @elseif($order->payment_status === 'failed') bg-red-100 text-red-800
                                        @elseif($order->payment_status === 'refunded') bg-gray-100 text-gray-800 @endif">
                                            Payment: {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="mt-4 sm:mt-0 flex items-center space-x-4">
                                    <span
                                        class="text-lg font-bold text-indigo-600">${{ number_format($order->total_amount, 2) }}</span>
                                    <a href="{{ route('account.orders.show', $order) }}"
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition duration-300 text-sm">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items Preview -->
                        <div class="px-6 py-4">
                            <div class="flex items-center space-x-4">
                                <div class="flex -space-x-2">
                                    @foreach ($order->orderItems->take(3) as $item)
                                        <div class="w-12 h-12 bg-gray-200 rounded-md border-2 border-white overflow-hidden">
                                            @if ($item->product && $item->product->main_image)
                                                <img src="{{ $item->product->main_image }}" alt="{{ $item->product_name }}"
                                                    class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center">
                                                    <span class="text-gray-400 text-xs">No Image</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                    @if ($order->orderItems->count() > 3)
                                        <div
                                            class="w-12 h-12 bg-gray-100 rounded-md border-2 border-white flex items-center justify-center">
                                            <span
                                                class="text-gray-600 text-xs">+{{ $order->orderItems->count() - 3 }}</span>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">
                                        {{ $order->orderItems->count() }}
                                        item{{ $order->orderItems->count() > 1 ? 's' : '' }}
                                    </p>
                                    <p class="text-sm text-gray-500">
                                        {{ $order->orderItems->pluck('product_name')->take(2)->implode(', ') }}
                                        @if ($order->orderItems->count() > 2)
                                            and {{ $order->orderItems->count() - 2 }} more
                                        @endif
                                    </p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-600">Payment Method</p>
                                    <p class="text-sm font-medium text-gray-900">
                                        {{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Order Actions -->
                        @if ($order->canBeCancelled())
                            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex justify-end">
                                    <form action="{{ route('account.orders.cancel', $order) }}" method="POST"
                                        onsubmit="return confirm('Are you sure you want to cancel this order? This action cannot be undone.')"
                                        class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            Cancel Order
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $orders->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="text-gray-400 text-6xl mb-4">📦</div>
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">No orders yet</h2>
                <p class="text-gray-600 mb-8">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="{{ route('shop.index') }}"
                    class="bg-indigo-600 text-white px-8 py-3 rounded-md hover:bg-indigo-700 transition duration-300 font-medium">
                    Start Shopping
                </a>
            </div>
        @endif
    </div>
@endsection
