<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Setting;
use App\Services\PriceCalculationService;
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
            if (!$product->in_stock || ($product->stock_quantity !== null && $product->stock_quantity <= 0)) {
                return false;
            }
            
            // Check if cart quantity exceeds available stock
            if ($product->stock_quantity !== null) {
                // For bottle-based products, check stock based on variant
                if ($product->is_bottle_based && $item->variant && isset($item->variant['type']) && $item->variant['type'] === 'bottle') {
                    $requestedCapsules = $item->quantity; // quantity is stored as total capsules
                    if ($requestedCapsules > $product->stock_quantity) {
                        return false;
                    }
                } else {
                    // For regular products, check quantity directly
                    if ($item->quantity > $product->stock_quantity) {
                        return false;
                    }
                }
            }
            
            return true;
        });

        // If some items were removed, redirect back to cart with a message
        if ($availableItems->count() < $cartItems->count()) {
            $removedCount = $cartItems->count() - $availableItems->count();
            return redirect()->route('cart.index')
                ->with('error', "{$removedCount} item(s) in your cart are no longer available or have insufficient stock. Please update your cart.");
        }

        // Calculate base subtotal (cart stores base prices)
        $baseSubtotal = $availableItems->sum(function ($item) {
            // For bottle-based products, calculate using saved price and bottles
            // Prices are base prices
            if ($item->variant && isset($item->variant['type']) && $item->variant['type'] === 'bottle' 
                && isset($item->variant['bottles'])) {
                // Use saved base price * bottles
                return $item->price * $item->variant['bottles'];
            }
            // For regular products, use base price
            return $item->price * $item->quantity;
        });

        // Calculate tax-inclusive subtotal for checkout
        $subtotal = PriceCalculationService::calculateTaxInclusivePrice($baseSubtotal);
        // Calculate tax amount from tax-inclusive subtotal (for display)
        $taxAmount = PriceCalculationService::calculateTaxAmount($subtotal);
        // Calculate shipping based on tax-inclusive subtotal
        $shipping = PriceCalculationService::calculateShipping($subtotal);
        // Total is tax-inclusive subtotal + shipping
        $total = $subtotal + $shipping;

        // Pass tax settings to frontend for calculating tax-inclusive item prices
        $taxSettings = [
            'tax_enabled' => Setting::isEnabled('tax_enabled'),
            'tax_rate' => (float)Setting::get('tax_rate', '10'),
            'price_increase_percentage' => (float)Setting::get('price_increase_percentage', '10'),
        ];

        return Inertia::render('Checkout', [
            'cartItems' => $availableItems,
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
            'shipping' => $shipping,
            'total' => $total,
            'taxSettings' => $taxSettings,
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
