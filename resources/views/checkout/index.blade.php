@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>

        <form action="{{ route('checkout.store') }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Checkout Form -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Billing Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Billing Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="billing_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name
                                    *</label>
                                <input type="text" id="billing_name" name="billing_name"
                                    value="{{ old('billing_name', Auth::user()->name) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_name') border-red-500 @enderror">
                                @error('billing_name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address *</label>
                                <input type="email" id="billing_email" name="billing_email"
                                    value="{{ old('billing_email', Auth::user()->email) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_email') border-red-500 @enderror">
                                @error('billing_email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number
                                    *</label>
                                <input type="tel" id="billing_phone" name="billing_phone"
                                    value="{{ old('billing_phone', Auth::user()->phone) }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_phone') border-red-500 @enderror">
                                @error('billing_phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_country" class="block text-sm font-medium text-gray-700 mb-2">Country
                                    *</label>
                                <select id="billing_country" name="billing_country" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_country') border-red-500 @enderror">
                                    <option value="">Select Country</option>
                                    <option value="US" {{ old('billing_country') == 'US' ? 'selected' : '' }}>United
                                        States</option>
                                    <option value="CA" {{ old('billing_country') == 'CA' ? 'selected' : '' }}>Canada
                                    </option>
                                    <option value="ID" {{ old('billing_country') == 'ID' ? 'selected' : '' }}>Indonesia
                                    </option>
                                </select>
                                @error('billing_country')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">Address
                                    *</label>
                                <textarea id="billing_address" name="billing_address" rows="3" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_address') border-red-500 @enderror">{{ old('billing_address', Auth::user()->address) }}</textarea>
                                @error('billing_address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_city" class="block text-sm font-medium text-gray-700 mb-2">City
                                    *</label>
                                <input type="text" id="billing_city" name="billing_city"
                                    value="{{ old('billing_city') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_city') border-red-500 @enderror">
                                @error('billing_city')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_state"
                                    class="block text-sm font-medium text-gray-700 mb-2">State/Province *</label>
                                <input type="text" id="billing_state" name="billing_state"
                                    value="{{ old('billing_state') }}" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_state') border-red-500 @enderror">
                                @error('billing_state')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="billing_zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal
                                    Code *</label>
                                <input type="text" id="billing_zip" name="billing_zip" value="{{ old('billing_zip') }}"
                                    required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 @error('billing_zip') border-red-500 @enderror">
                                @error('billing_zip')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Information -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-semibold text-gray-900">Shipping Information</h2>
                            <label class="flex items-center">
                                <input type="checkbox" id="same_as_billing" name="same_as_billing" value="1"
                                    {{ old('same_as_billing', true) ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Same as billing address</span>
                            </label>
                        </div>

                        <div id="shipping_fields" class="grid grid-cols-1 md:grid-cols-2 gap-6" style="display: none;">
                            <div>
                                <label for="shipping_name" class="block text-sm font-medium text-gray-700 mb-2">Full
                                    Name</label>
                                <input type="text" id="shipping_name" name="shipping_name"
                                    value="{{ old('shipping_name') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="shipping_email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                    Address</label>
                                <input type="email" id="shipping_email" name="shipping_email"
                                    value="{{ old('shipping_email') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="shipping_phone" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                    Number</label>
                                <input type="tel" id="shipping_phone" name="shipping_phone"
                                    value="{{ old('shipping_phone') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="shipping_country"
                                    class="block text-sm font-medium text-gray-700 mb-2">Country</label>
                                <select id="shipping_country" name="shipping_country"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Select Country</option>
                                    <option value="US" {{ old('shipping_country') == 'US' ? 'selected' : '' }}>United
                                        States</option>
                                    <option value="CA" {{ old('shipping_country') == 'CA' ? 'selected' : '' }}>Canada
                                    </option>
                                    <option value="ID" {{ old('shipping_country') == 'ID' ? 'selected' : '' }}>
                                        Indonesia</option>
                                </select>
                            </div>

                            <div class="md:col-span-2">
                                <label for="shipping_address"
                                    class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea id="shipping_address" name="shipping_address" rows="3"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('shipping_address') }}</textarea>
                            </div>

                            <div>
                                <label for="shipping_city"
                                    class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <input type="text" id="shipping_city" name="shipping_city"
                                    value="{{ old('shipping_city') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="shipping_state"
                                    class="block text-sm font-medium text-gray-700 mb-2">State/Province</label>
                                <input type="text" id="shipping_state" name="shipping_state"
                                    value="{{ old('shipping_state') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="shipping_zip" class="block text-sm font-medium text-gray-700 mb-2">ZIP/Postal
                                    Code</label>
                                <input type="text" id="shipping_zip" name="shipping_zip"
                                    value="{{ old('shipping_zip') }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Payment Method</h2>

                        <div class="space-y-4">
                            <!-- Cash on Delivery -->
                            <label
                                class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="cod"
                                    {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Cash on Delivery (COD)</div>
                                    <div class="text-sm text-gray-500">Pay when you receive your order</div>
                                </div>
                            </label>

                            <!-- Xendit Invoice -->
                            <label
                                class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="xendit_invoice"
                                    {{ old('payment_method') == 'xendit_invoice' ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Online Payment (Xendit)</div>
                                    <div class="text-sm text-gray-500">Pay with various methods: Bank Transfer, E-Wallet,
                                        Credit Card, etc.</div>
                                </div>
                            </label>

                            <!-- Virtual Account -->
                            <label
                                class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="xendit_va"
                                    {{ old('payment_method') == 'xendit_va' ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">Virtual Account</div>
                                    <div class="text-sm text-gray-500">Pay via Bank Virtual Account (BCA, BNI, BRI,
                                        Mandiri)</div>
                                </div>
                            </label>

                            <!-- E-Wallet -->
                            <label
                                class="flex items-center p-4 border border-gray-300 rounded-md cursor-pointer hover:bg-gray-50">
                                <input type="radio" name="payment_method" value="xendit_ewallet"
                                    {{ old('payment_method') == 'xendit_ewallet' ? 'checked' : '' }}
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">E-Wallet</div>
                                    <div class="text-sm text-gray-500">Pay with OVO, DANA, LinkAja, ShopeePay</div>
                                </div>
                            </label>
                        </div>

                        @error('payment_method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Notes (Optional)</h2>
                        <textarea id="notes" name="notes" rows="4" placeholder="Any special instructions for your order..."
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6 sticky top-4">
                        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>

                        <!-- Cart Items -->
                        <div class="space-y-3 mb-6">
                            @foreach ($cartItems as $item)
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0 w-12 h-12 bg-gray-200 rounded-md overflow-hidden">
                                        <img src="{{ $item->product->main_image }}" alt="{{ $item->product->name }}"
                                            class="w-full h-full object-cover">
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $item->product->name }}
                                        </p>
                                        <p class="text-sm text-gray-500">Qty: {{ $item->quantity }}</p>
                                    </div>
                                    <div class="text-sm font-medium text-gray-900">
                                        ${{ number_format($item->quantity * $item->product->display_price, 2) }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Totals -->
                        <div class="space-y-3 border-t pt-4">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-semibold">${{ number_format($subtotal, 2) }}</span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-semibold">
                                    @if ($shipping == 0)
                                        <span class="text-green-600">Free</span>
                                    @else
                                        ${{ number_format($shipping, 2) }}
                                    @endif
                                </span>
                            </div>

                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-semibold">${{ number_format($tax, 2) }}</span>
                            </div>

                            <div class="border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total</span>
                                    <span class="text-lg font-bold text-indigo-600">${{ number_format($total, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit"
                            class="w-full mt-6 bg-indigo-600 text-white px-6 py-3 rounded-md hover:bg-indigo-700 transition duration-300 font-medium">
                            Place Order
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('same_as_billing').addEventListener('change', function() {
            const shippingFields = document.getElementById('shipping_fields');
            if (this.checked) {
                shippingFields.style.display = 'none';
            } else {
                shippingFields.style.display = 'grid';
            }
        });
    </script>
@endsection
