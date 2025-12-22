<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartItems->load('product.brand');

        $subtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        $tax = $subtotal * 0.08; // 8% tax
        $shipping = $subtotal >= 10000 ? 0 : 500; // Free shipping over ¥10,000
        $total = $subtotal + $tax + $shipping;

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping' => $shipping,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1',
                'variant' => 'nullable|array',
            ]);

            $product = Product::findOrFail($request->product_id);

            if (!$product->in_stock) {
                return back()->withErrors(['product' => 'This product is out of stock.']);
            }

            // Check stock quantity (allow null stock_quantity for unlimited stock)
            if ($product->stock_quantity !== null && $product->stock_quantity <= 0) {
                return back()->withErrors(['product' => 'This product is out of stock.']);
            }

            // Check if item already exists in cart
            $userId = Auth::id();
            $sessionId = Auth::check() ? null : session()->getId();
            $variant = $request->variant ?? null;
            
            // Ensure session is started for guest users
            if (!$userId && !$sessionId) {
                session()->start();
                $sessionId = session()->getId();
            }
            
            // Build query to find existing cart item
            $query = CartItem::where('product_id', $product->id);
            
            // Match by user or session
            if ($userId) {
                $query->where('user_id', $userId)->whereNull('session_id');
            } else {
                $query->whereNull('user_id')->where('session_id', $sessionId);
            }
            
            // Match variant if provided, otherwise match null/empty variants
            // PostgreSQL requires explicit casting for JSON comparisons
            if ($variant && is_array($variant) && count($variant) > 0) {
                // For PostgreSQL, compare JSON by casting to text
                $variantJson = json_encode($variant, JSON_UNESCAPED_SLASHES);
                $query->whereRaw('variant::text = ?', [$variantJson]);
            } else {
                // For products without variants, check for null or empty JSON
                // PostgreSQL: cast JSON to text for comparison
                $query->where(function ($q) {
                    $q->whereNull('variant')
                      ->orWhereRaw('variant::text = ?', ['[]'])
                      ->orWhereRaw('variant::text = ?', ['null'])
                      ->orWhereRaw('variant::text = ?', ['""']);
                });
            }
            
            $cartItem = $query->first();

            $price = $product->sale_price ?? $product->price;

            if ($cartItem) {
                // Calculate new total quantity
                $newQuantity = $cartItem->quantity + $request->quantity;
                
                // Check if new quantity exceeds available stock (only if stock_quantity is set)
                if ($product->stock_quantity !== null && $newQuantity > $product->stock_quantity) {
                    return back()->withErrors(['product' => "Only {$product->stock_quantity} items available in stock."]);
                }
                
                // Update quantity if item already exists
                $cartItem->update([
                    'quantity' => $newQuantity,
                    'price' => $price,
                ]);
            } else {
                // Check if requested quantity exceeds available stock (only if stock_quantity is set)
                if ($product->stock_quantity !== null && $request->quantity > $product->stock_quantity) {
                    return back()->withErrors(['product' => "Only {$product->stock_quantity} items available in stock."]);
                }
                
                // Create new cart item
                $cartItem = CartItem::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'variant' => $variant,
                ]);
            }

            return back()->with('success', 'Product added to cart!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Cart store validation error', [
                'errors' => $e->errors(),
                'request' => $request->all(),
            ]);
            return back()->withErrors($e->errors());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error('Cart store error: Product not found', [
                'product_id' => $request->product_id,
                'request' => $request->all(),
            ]);
            return back()->withErrors(['product' => 'Product not found.']);
        } catch (\Exception $e) {
            \Log::error('Cart store error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'request' => $request->all(),
                'trace' => $e->getTraceAsString(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return back()->withErrors(['error' => 'Failed to add product to cart: ' . $e->getMessage()]);
        }
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Check ownership
        if (Auth::check()) {
            if ($cartItem->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                abort(403);
            }
        }

        // Load product to check stock
        $cartItem->load('product');
        $product = $cartItem->product;

        if (!$product) {
            return back()->withErrors(['error' => 'Product not found.']);
        }

        // Check if product is in stock
        if (!$product->in_stock || $product->stock_quantity <= 0) {
            return back()->withErrors(['error' => 'This product is out of stock.']);
        }

        // Check if requested quantity exceeds available stock
        if ($request->quantity > $product->stock_quantity) {
            return back()->withErrors(['error' => "Only {$product->stock_quantity} items available in stock."]);
        }

        $cartItem->update([
            'quantity' => $request->quantity,
        ]);

        return back()->with('success', 'Cart updated!');
    }

    public function destroy(CartItem $cartItem)
    {
        // Check ownership
        if (Auth::check()) {
            if ($cartItem->user_id !== Auth::id()) {
                abort(403);
            }
        } else {
            if ($cartItem->session_id !== session()->getId()) {
                abort(403);
            }
        }

        $cartItem->delete();

        return back()->with('success', 'Item removed from cart!');
    }

    public function count()
    {
        $count = $this->getCartItems()->sum('quantity');
        return response()->json(['count' => $count]);
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
