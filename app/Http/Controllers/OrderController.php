<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\CartItem;
use App\Services\PriceCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address_line_1' => 'required|string',
            'address_line_2' => 'nullable|string',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:255',
            'payment_method' => 'required|string|in:bank_transfer,credit_card',
            'health_check' => 'nullable|array',
            'risk_acknowledged' => 'required|in:1,true,on,yes',
            'resale_prohibited' => 'required|in:1,true,on,yes',
        ]);

        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return back()->withErrors(['cart' => 'Your cart is empty.']);
        }

        // Validate stock availability before processing order
        foreach ($cartItems as $cartItem) {
            $product = $cartItem->product;
            if (!$product) {
                return back()->withErrors(['cart' => 'One or more products in your cart are no longer available.']);
            }

            // Check if product is in stock
            if (!$product->in_stock || ($product->stock_quantity !== null && $product->stock_quantity <= 0)) {
                return back()->withErrors(['cart' => "{$product->name} is out of stock."]);
            }

            // Check if requested quantity exceeds available stock
            if ($product->stock_quantity !== null) {
                // For bottle-based products, quantity is stored as total capsules
                if ($product->is_bottle_based && $cartItem->variant && isset($cartItem->variant['type']) && $cartItem->variant['type'] === 'bottle') {
                    if ($cartItem->quantity > $product->stock_quantity) {
                        $availableBottles = floor($product->stock_quantity / ($cartItem->variant['tier']['capsules'] ?? $product->capsules_per_bottle ?? 50));
                        return back()->withErrors(['cart' => "Insufficient stock for {$product->name}. Only {$product->stock_quantity} capsules ({$availableBottles} " . ($availableBottles === 1 ? 'bottle' : 'bottles') . ") available."]);
                    }
                } else {
                    // For regular products, check quantity directly
                    if ($cartItem->quantity > $product->stock_quantity) {
                        return back()->withErrors(['cart' => "Insufficient stock for {$product->name}. Only {$product->stock_quantity} available."]);
                    }
                }
            }
        }

        DB::beginTransaction();
        try {
            // Calculate base subtotal (cart stores base prices)
            $baseSubtotal = $cartItems->sum(function ($item) {
                // For bottle-based products, calculate using saved price and bottles from variant
                // Prices are base prices
                if ($item->variant && isset($item->variant['type']) && $item->variant['type'] === 'bottle' 
                    && isset($item->variant['bottles'])) {
                    return $item->price * $item->variant['bottles'];
                }
                // For regular products, use base price
                return $item->price * $item->quantity;
            });

            // Calculate tax-inclusive subtotal for order
            $subtotal = PriceCalculationService::calculateTaxInclusivePrice($baseSubtotal);
            // Calculate tax amount from tax-inclusive subtotal (for display)
            $taxAmount = PriceCalculationService::calculateTaxAmount($subtotal);
            // Calculate shipping based on tax-inclusive subtotal
            $shipping = PriceCalculationService::calculateShipping($subtotal);
            // Total is tax-inclusive subtotal + shipping
            $total = $subtotal + $shipping;

            $order = Order::create([
                'user_id' => Auth::id(),
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'phone' => $request->phone,
                'address_line_1' => $request->address_line_1,
                'address_line_2' => $request->address_line_2,
                'city' => $request->city,
                'state' => $request->state,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'subtotal' => $subtotal,
                'tax' => $taxAmount,
                'shipping' => $shipping,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'health_check' => $request->health_check,
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            // Create order items and update stock
            foreach ($cartItems as $cartItem) {
                $product = $cartItem->product;
                
                // Determine quantity and total for order item
                // For bottle-based products, use bottles for quantity display and total calculation
                $orderQuantity = $cartItem->quantity; // Default: use cart quantity
                $orderTotal = $cartItem->price * $cartItem->quantity; // Default: price * quantity
                
                if ($product->is_bottle_based && $cartItem->variant && isset($cartItem->variant['type']) && $cartItem->variant['type'] === 'bottle' 
                    && isset($cartItem->variant['bottles'])) {
                    // For bottle-based products, use bottles for quantity and total calculation
                    $bottles = $cartItem->variant['bottles'];
                    $orderQuantity = $bottles; // Store bottles as quantity for display
                    $orderTotal = $cartItem->price * $bottles; // Total = price per bottle * bottles
                }
                
                // Create order item
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_name' => $cartItem->product->name,
                    'product_sku' => $cartItem->product->sku,
                    'quantity' => $orderQuantity,
                    'price' => $cartItem->price,
                    'total' => $orderTotal,
                    'variant' => $cartItem->variant,
                ]);

                // Update product stock
                $newStockQuantity = $product->stock_quantity - $cartItem->quantity;
                $product->update([
                    'stock_quantity' => max(0, $newStockQuantity), // Ensure it doesn't go below 0
                    'in_stock' => $newStockQuantity > 0, // Update in_stock status
                ]);
            }

            // Clear cart
            $cartItems->each->delete();

            DB::commit();

            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to place order. Please try again.']);
        }
    }

    public function show(Order $order)
    {
        if (Auth::check() && $order->user_id !== Auth::id()) {
            abort(403);
        }

        $order->load('items.product');

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    private function getCartItems()
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->with('product')->get();
        } else {
            return CartItem::where('session_id', session()->getId())->with('product')->get();
        }
    }
}
