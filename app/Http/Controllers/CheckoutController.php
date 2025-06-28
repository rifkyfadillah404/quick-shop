<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\XenditService;

class CheckoutController extends Controller
{
    public function index()
    {
        Log::info('Checkout index accessed', [
            'user_id' => Auth::id(),
            'is_authenticated' => Auth::check()
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->count() === 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->display_price;
        });

        $shipping = $subtotal >= 50 ? 0 : 5;
        $tax = $subtotal * 0.1;
        $total = $subtotal + $shipping + $tax;

        return view('checkout.index', compact('cartItems', 'subtotal', 'shipping', 'tax', 'total'));
    }

    public function store(Request $request)
    {
        Log::info('=== CHECKOUT STORE METHOD CALLED ===');
        Log::info('Checkout attempt', [
            'user_id' => Auth::id(),
            'request_data' => $request->all(),
            'headers' => $request->headers->all()
        ]);

        try {
            $request->validate([
                'billing_name' => 'required|string|max:255',
                'billing_email' => 'required|email|max:255',
                'billing_phone' => 'required|string|max:20',
                'billing_address' => 'required|string|max:500',
                'billing_city' => 'required|string|max:100',
                'billing_state' => 'required|string|max:100',
                'billing_zip' => 'required|string|max:20',
                'billing_country' => 'required|string|max:100',
                'payment_method' => 'required|in:cod,xendit_invoice,xendit_va,xendit_ewallet',
                'same_as_billing' => 'nullable|boolean',
                'shipping_name' => 'nullable|string|max:255',
                'shipping_email' => 'nullable|email|max:255',
                'shipping_phone' => 'nullable|string|max:20',
                'shipping_address' => 'nullable|string|max:500',
                'shipping_city' => 'nullable|string|max:100',
                'shipping_state' => 'nullable|string|max:100',
                'shipping_zip' => 'nullable|string|max:20',
                'shipping_country' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
            ]);

            Log::info('Validation passed');
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation failed', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ]);
            throw $e;
        }

        $cartItems = $this->getCartItems();

        Log::info('Cart items retrieved', [
            'count' => $cartItems->count(),
            'items' => $cartItems->toArray()
        ]);

        if ($cartItems->count() === 0) {
            Log::warning('Cart is empty during checkout');
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        // Validate stock availability
        foreach ($cartItems as $item) {
            Log::info('Checking stock for item', [
                'product_name' => $item->product->name,
                'is_active' => $item->product->is_active,
                'in_stock' => $item->product->in_stock,
                'stock_quantity' => $item->product->stock_quantity,
                'requested_quantity' => $item->quantity
            ]);

            if (!$item->product->is_active || !$item->product->in_stock || $item->product->stock_quantity < $item->quantity) {
                Log::warning('Stock validation failed', [
                    'product_name' => $item->product->name,
                    'is_active' => $item->product->is_active,
                    'in_stock' => $item->product->in_stock,
                    'stock_quantity' => $item->product->stock_quantity,
                    'requested_quantity' => $item->quantity
                ]);
                return back()->with('error', "Product '{$item->product->name}' is not available or insufficient stock.");
            }
        }

        Log::info('Starting DB transaction for checkout');

        DB::beginTransaction();

        try {
            // Calculate totals
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->product->display_price;
            });

            $shipping = $subtotal >= 50 ? 0 : 5;
            $tax = $subtotal * 0.1;
            $total = $subtotal + $shipping + $tax;

            // Prepare addresses
            $billingAddress = [
                'name' => $request->billing_name,
                'email' => $request->billing_email,
                'phone' => $request->billing_phone,
                'address' => $request->billing_address,
                'city' => $request->billing_city,
                'state' => $request->billing_state,
                'zip' => $request->billing_zip,
                'country' => $request->billing_country,
            ];

            $shippingAddress = $request->same_as_billing ? $billingAddress : [
                'name' => $request->shipping_name,
                'email' => $request->shipping_email,
                'phone' => $request->shipping_phone,
                'address' => $request->shipping_address,
                'city' => $request->shipping_city,
                'state' => $request->shipping_state,
                'zip' => $request->shipping_zip,
                'country' => $request->shipping_country,
            ];

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => Order::generateOrderNumber(),
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_amount' => $shipping,
                'total_amount' => $total,
                'payment_method' => $request->payment_method,
                'payment_status' => $request->payment_method === 'cod' ? 'pending' : 'pending',
                'billing_address' => $billingAddress,
                'shipping_address' => $shippingAddress,
                'notes' => $request->notes,
            ]);

            // Create order items and update stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product->id,
                    'product_name' => $item->product->name,
                    'product_sku' => $item->product->sku,
                    'price' => $item->product->display_price,
                    'quantity' => $item->quantity,
                    'total' => $item->quantity * $item->product->display_price,
                ]);

                // Decrease stock
                $item->product->decreaseStock($item->quantity);
            }

            // Handle payment based on method
            if (in_array($request->payment_method, ['xendit_invoice', 'xendit_va', 'xendit_ewallet'])) {
                $xenditService = new XenditService();

                try {
                    switch ($request->payment_method) {
                        case 'xendit_invoice':
                            $payment = $xenditService->createInvoice($order);
                            $order->update([
                                'payment_id' => $payment['id'],
                                'payment_url' => $payment['invoice_url'],
                                'payment_data' => $payment
                            ]);
                            break;

                        case 'xendit_va':
                            $bankCode = $request->input('bank_code', 'BCA');
                            $payment = $xenditService->createVirtualAccount($order, $bankCode);
                            $order->update([
                                'payment_id' => $payment['id'],
                                'payment_data' => $payment
                            ]);
                            break;

                        case 'xendit_ewallet':
                            $ewalletType = $request->input('ewallet_type', 'OVO');
                            $payment = $xenditService->createEWalletPayment($order, $ewalletType);
                            $order->update([
                                'payment_id' => $payment['id'],
                                'payment_url' => $payment['checkout_url'] ?? null,
                                'payment_data' => $payment
                            ]);
                            break;
                    }
                } catch (\Exception $e) {
                    Log::error('Xendit payment creation failed', [
                        'order_id' => $order->id,
                        'payment_method' => $request->payment_method,
                        'error' => $e->getMessage()
                    ]);
                    // Continue with order creation but mark payment as failed
                    $order->update(['payment_status' => 'failed']);
                }
            }

            // Clear cart
            if (Auth::check()) {
                CartItem::where('user_id', Auth::id())->delete();
            } else {
                session()->forget('cart');
            }

            session()->forget('cart_count');

            DB::commit();

            // Redirect based on payment method
            if ($request->payment_method === 'cod') {
                return redirect()->route('account.orders.show', $order)->with('success', 'Order placed successfully!');
            } elseif (in_array($request->payment_method, ['xendit_invoice', 'xendit_ewallet']) && $order->payment_url) {
                return redirect($order->payment_url);
            } else {
                return redirect()->route('checkout.payment', $order)->with('success', 'Order created! Please complete your payment.');
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Checkout failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Something went wrong. Please try again. Error: ' . $e->getMessage());
        }
    }

    public function payment(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.payment', compact('order'));
    }

    public function success(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.success', compact('order'));
    }

    public function failed(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        return view('checkout.failed', compact('order'));
    }

    public function webhook(Request $request)
    {
        $xenditService = new XenditService();

        // Verify webhook signature
        $signature = $request->header('x-callback-token');
        $payload = $request->getContent();

        if (!$xenditService->verifyWebhookSignature($payload, $signature)) {
            Log::warning('Invalid Xendit webhook signature');
            return response('Invalid signature', 400);
        }

        $data = $request->all();

        Log::info('Xendit webhook received', $data);

        // Handle different webhook events
        switch ($data['event'] ?? null) {
            case 'invoice.paid':
                $this->handleInvoicePaid($data);
                break;
            case 'invoice.expired':
                $this->handleInvoiceExpired($data);
                break;
            case 'virtual_account.paid':
                $this->handleVirtualAccountPaid($data);
                break;
            case 'ewallet.payment.paid':
                $this->handleEWalletPaid($data);
                break;
        }

        return response('OK', 200);
    }

    private function handleInvoicePaid($data)
    {
        $externalId = $data['external_id'] ?? null;
        if (!$externalId) return;

        $order = Order::where('order_number', $externalId)->first();
        if (!$order) return;

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);

        Log::info('Invoice paid', ['order_id' => $order->id]);
    }

    private function handleInvoiceExpired($data)
    {
        $externalId = $data['external_id'] ?? null;
        if (!$externalId) return;

        $order = Order::where('order_number', $externalId)->first();
        if (!$order) return;

        $order->update(['payment_status' => 'expired']);

        Log::info('Invoice expired', ['order_id' => $order->id]);
    }

    private function handleVirtualAccountPaid($data)
    {
        $externalId = $data['external_id'] ?? null;
        if (!$externalId) return;

        $order = Order::where('order_number', $externalId)->first();
        if (!$order) return;

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);

        Log::info('Virtual Account paid', ['order_id' => $order->id]);
    }

    private function handleEWalletPaid($data)
    {
        $externalId = $data['external_id'] ?? null;
        if (!$externalId) return;

        $order = Order::where('order_number', $externalId)->first();
        if (!$order) return;

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing'
        ]);

        Log::info('E-Wallet paid', ['order_id' => $order->id]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::with('product')->where('user_id', Auth::id())->get();
        } else {
            $cart = session()->get('cart', []);
            $cartItems = collect();

            foreach ($cart as $item) {
                $product = Product::find($item['product_id']);
                if ($product) {
                    $cartItems->push((object) [
                        'id' => $item['product_id'],
                        'product' => $product,
                        'quantity' => $item['quantity'],
                    ]);
                }
            }

            return $cartItems;
        }
    }
}
