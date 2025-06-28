<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())
            ->with('orderItems.product')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    public function orderShow(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('orderItems.product');

        return view('account.order-detail', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . Auth::id(),
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
            'current_password' => 'nullable|required_with:password',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check current password if new password is provided
        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
        }

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
        ]);

        return back()->with('success', 'Profile updated successfully!');
    }

    public function cancelOrder(Order $order)
    {
        // Check if user owns this order
        if ($order->user_id !== Auth::id()) {
            abort(403);
        }

        // Check if order can be cancelled
        if (!$order->canBeCancelled()) {
            return back()->with('error', 'This order cannot be cancelled. It may have already been approved by admin or shipped.');
        }

        // Cancel the order
        $order->update([
            'status' => 'cancelled',
            'payment_status' => $order->payment_status === 'pending' ? 'cancelled' : $order->payment_status,
        ]);

        // If order has items, restore stock
        foreach ($order->orderItems as $item) {
            if ($item->product) {
                $item->product->increment('stock_quantity', $item->quantity);
            }
        }

        return back()->with('success', 'Order has been cancelled successfully. Stock has been restored.');
    }
}
