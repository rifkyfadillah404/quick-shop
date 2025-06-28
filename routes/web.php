<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\AccountController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Shop & Products
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');

// Categories
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

// Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/update/{cartItem}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/remove/{cartItem}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/remove-session/{productId}', [CartController::class, 'removeFromSession'])->name('cart.remove.session');

// Webhook (no auth required)
Route::post('/webhook/xendit', [CheckoutController::class, 'webhook'])->name('webhook.xendit');

// Debug route
Route::post('/test-checkout', function (\Illuminate\Http\Request $request) {
    \Log::info('Test checkout called', $request->all());
    return response()->json(['status' => 'success', 'data' => $request->all()]);
});

// Checkout
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/payment/{order}', [CheckoutController::class, 'payment'])->name('checkout.payment');
    Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed/{order}', [CheckoutController::class, 'failed'])->name('checkout.failed');

    // Account
    Route::get('/account/orders', [AccountController::class, 'orders'])->name('account.orders');
    Route::get('/account/orders/{order}', [AccountController::class, 'orderShow'])->name('account.orders.show');
    Route::post('/account/orders/{order}/cancel', [AccountController::class, 'cancelOrder'])->name('account.orders.cancel');
    Route::get('/account/profile', [AccountController::class, 'profile'])->name('account.profile');
    Route::patch('/account/profile', [AccountController::class, 'updateProfile'])->name('account.profile.update');
});
