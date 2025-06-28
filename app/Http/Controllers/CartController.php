<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $subtotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->display_price;
        });

        return view('cart.index', compact('cartItems', 'subtotal'));
    }

    public function add(Request $request, Product $product)
    {


        $request->validate([
            'quantity' => 'nullable|integer|min:1|max:' . $product->stock_quantity
        ]);

        if (!$product->is_active || !$product->in_stock || $product->stock_quantity < $request->quantity) {

            return back()->with('error', 'Product is not available or insufficient stock.');
        }

        $quantity = $request->input('quantity', 1);


        if (Auth::check()) {
            // For authenticated users, use database
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                $newQuantity = $cartItem->quantity + $quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                $cartItem->update(['quantity' => $newQuantity]);
            } else {
                CartItem::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
            }
        } else {
            // For guest users, use session
            $cart = session()->get('cart', []);
            $productId = $product->id;

            if (isset($cart[$productId])) {
                $newQuantity = $cart[$productId]['quantity'] + $quantity;
                if ($newQuantity > $product->stock_quantity) {
                    return back()->with('error', 'Not enough stock available.');
                }
                $cart[$productId]['quantity'] = $newQuantity;
            } else {
                $cart[$productId] = [
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ];
            }

            session()->put('cart', $cart);
        }

        $this->updateCartCount();



        return back()->with('success', 'Product added to cart successfully!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if user owns this cart item
        if (Auth::check() && $cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $quantity = $request->input('quantity');
        $product = $cartItem->product;

        if ($quantity > $product->stock_quantity) {
            return back()->with('error', 'Not enough stock available.');
        }

        $cartItem->update(['quantity' => $quantity]);
        $this->updateCartCount();

        return back()->with('success', 'Cart updated successfully!');
    }

    public function remove(CartItem $cartItem)
    {
        // Check if user owns this cart item
        if (Auth::check() && $cartItem->user_id !== Auth::id()) {
            abort(403);
        }

        $cartItem->delete();
        $this->updateCartCount();

        return back()->with('success', 'Item removed from cart!');
    }

    public function removeFromSession(Request $request, $productId)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session()->put('cart', $cart);
        }

        $this->updateCartCount();

        return back()->with('success', 'Item removed from cart!');
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

    private function updateCartCount()
    {
        if (Auth::check()) {
            $count = CartItem::where('user_id', Auth::id())->sum('quantity');
        } else {
            $cart = session()->get('cart', []);
            $count = array_sum(array_column($cart, 'quantity'));
        }

        session()->put('cart_count', $count);
    }
}
