<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartItems->load('product.brand', 'product.category');

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Please add items before checkout.');
        }

        // Filter out items that are no longer available or have insufficient stock
        $availableItems = $cartItems->filter(function ($item) {
            $product = $item->product;
            if (!$product) {
                return false;
            }
            
            // Check if product is in stock
            if (!$product->in_stock || $product->stock_quantity <= 0) {
                return false;
            }
            
            // Check if cart quantity exceeds available stock
            if ($item->quantity > $product->stock_quantity) {
                return false;
            }
            
            return true;
        });

        // If some items were removed, redirect back to cart with a message
        if ($availableItems->count() < $cartItems->count()) {
            $removedCount = $cartItems->count() - $availableItems->count();
            return redirect()->route('cart.index')
                ->with('error', "{$removedCount} item(s) in your cart are no longer available or have insufficient stock. Please update your cart.");
        }

        $subtotal = $availableItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $tax = 0; // Tax removed
        $shipping = $subtotal >= 10000 ? 0 : 500; // Free shipping over ¥10,000
        $total = $subtotal + $shipping; // Total without tax

        return Inertia::render('Checkout', [
            'cartItems' => $availableItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->get();
        } else {
            return CartItem::where('session_id', session()->getId())->get();
        }
    }
}
