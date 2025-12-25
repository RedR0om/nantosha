<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use App\Services\PriceCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $cartItems->load('product.brand');

        // Calculate base subtotal (cart stores base prices)
        $baseSubtotal = $cartItems->sum(function ($item) {
            // For bottle-based products, calculate using variant data
            // Prices in cart items are base prices
            if ($item->variant && isset($item->variant['type']) && $item->variant['type'] === 'bottle' 
                && isset($item->variant['tier']['price']) && isset($item->variant['bottles'])) {
                // Use base tier price
                return $item->variant['tier']['price'] * $item->variant['bottles'];
            }
            // For regular products, use base price
            return $item->price * $item->quantity;
        });

        // For cart page, show base prices with tax breakdown
        $subtotal = $baseSubtotal;
        // Calculate tax amount from base subtotal (for display)
        $taxAmount = PriceCalculationService::calculateTaxAmount(
            PriceCalculationService::calculateTaxInclusivePrice($baseSubtotal)
        );
        // Calculate shipping based on base subtotal using settings
        $shipping = PriceCalculationService::calculateShipping($baseSubtotal);
        // Total is base subtotal + tax + shipping
        $total = $subtotal + $taxAmount + $shipping;

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'subtotal' => $subtotal,
            'tax' => $taxAmount,
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
                'price' => 'nullable|numeric|min:0',
            ]);

            $product = Product::findOrFail($request->product_id);

            if (!$product->in_stock) {
                return back()->withErrors(['product' => 'This product is out of stock.']);
            }

            // Check if product requires bottle-only purchases
            if ($product->bottles_only && $product->is_bottle_based) {
                // Verify that the request includes bottle tier information
                if (!isset($request->variant['type']) || $request->variant['type'] !== 'bottle') {
                    return back()->withErrors(['product' => 'This product can only be purchased in bottle quantities. Please select a bottle option.']);
                }
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
            // Use database-specific syntax based on driver
            $driver = DB::connection()->getDriverName();
            $isPostgreSQL = $driver === 'pgsql';
            $isMySQL = in_array($driver, ['mysql', 'mariadb']);
            
            if ($variant && is_array($variant) && count($variant) > 0) {
                // Compare JSON variant - use driver-specific syntax
                $variantJson = json_encode($variant, JSON_UNESCAPED_SLASHES);
                
                if ($isPostgreSQL) {
                    // PostgreSQL: cast JSON to text for comparison
                    $query->whereRaw('variant::text = ?', [$variantJson]);
                } elseif ($isMySQL) {
                    // MySQL/MariaDB: cast JSON to CHAR for comparison
                    $query->whereRaw('CAST(variant AS CHAR) = ?', [$variantJson]);
                } else {
                    // Fallback: compare as JSON string (for SQLite, etc.)
                    $query->where('variant', $variantJson);
                }
            } else {
                // For products without variants, check for null or empty JSON
                $query->where(function ($q) use ($isPostgreSQL, $isMySQL) {
                    $q->whereNull('variant');
                    
                    if ($isPostgreSQL) {
                        // PostgreSQL: cast JSON to text for comparison
                        $q->orWhereRaw('variant::text = ?', ['[]'])
                          ->orWhereRaw('variant::text = ?', ['null'])
                          ->orWhereRaw('variant::text = ?', ['""']);
                    } elseif ($isMySQL) {
                        // MySQL/MariaDB: check for null, empty JSON array, or empty JSON object
                        $q->orWhereRaw('CAST(variant AS CHAR) = ?', ['[]'])
                          ->orWhereRaw('CAST(variant AS CHAR) = ?', ['{}'])
                          ->orWhereRaw('JSON_LENGTH(variant) = 0');
                    } else {
                        // Fallback: check for empty array or empty string (for SQLite, etc.)
                        $q->orWhere('variant', '[]')
                          ->orWhere('variant', '{}')
                          ->orWhere('variant', '');
                    }
                });
            }
            
            $cartItem = $query->first();

            // Determine price based on product type
            // Save base prices (tax-inclusive calculation happens at checkout)
            $basePrice = $product->sale_price ?? $product->price;
            $price = $basePrice; // Save base price, not tax-inclusive
            
            // For bottle-based products, use the tier price and check stock by bottles
            $requestedBottles = null;
            $requestedCapsules = $request->quantity;
            
            // If explicit price is provided, use it (should be base price from frontend)
            if ($request->has('price') && is_numeric($request->price)) {
                $price = (float)$request->price;
                Log::info('Using explicit price from request in store', [
                    'product_id' => $product->id,
                    'price' => $price,
                ]);
            } elseif ($product->is_bottle_based && isset($variant['tier']) && isset($variant['tier']['price'])) {
                // Fall back to tier price (should be base price from frontend)
                $tierPrice = $variant['tier']['price'];
                $price = is_numeric($tierPrice) ? (float)$tierPrice : $price;
                // For bottle-based products, quantity in request is total capsules
                // Calculate number of bottles from variant data
                if (isset($variant['bottles'])) {
                    $requestedBottles = $variant['bottles'];
                    $requestedCapsules = $variant['total_capsules'] ?? ($variant['tier']['capsules'] * $variant['bottles']);
                } else {
                    // Fallback: calculate bottles from quantity and tier
                    $requestedBottles = floor($request->quantity / $variant['tier']['capsules']);
                    $requestedCapsules = $request->quantity;
                }
            }

            if ($cartItem) {
                // Calculate new total quantity
                $newQuantity = $cartItem->quantity + $request->quantity;
                $newBottles = null;
                $newCapsules = $newQuantity;
                
                // For bottle-based products, calculate new bottles from existing cart item
                if ($product->is_bottle_based && $cartItem->variant && isset($cartItem->variant['bottles'])) {
                    $existingBottles = $cartItem->variant['bottles'] ?? 0;
                    $newBottles = $existingBottles + ($requestedBottles ?? 0);
                    $newCapsules = $cartItem->quantity + $requestedCapsules;
                }
                
                // Check if new quantity exceeds available stock (only if stock_quantity is set)
                if ($product->stock_quantity !== null) {
                    // For bottle-based products, stock_quantity is in capsules
                    if ($product->is_bottle_based && $newBottles !== null) {
                        if ($newCapsules > $product->stock_quantity) {
                            $availableBottles = floor($product->stock_quantity / ($variant['tier']['capsules'] ?? $product->capsules_per_bottle ?? 50));
                            return back()->withErrors(['product' => "Only {$product->stock_quantity} capsules ({$availableBottles} " . ($availableBottles === 1 ? 'bottle' : 'bottles') . ") available in stock."]);
                        }
                    } else {
                        if ($newQuantity > $product->stock_quantity) {
                            return back()->withErrors(['product' => "Only {$product->stock_quantity} items available in stock."]);
                        }
                    }
                }
                
                // Update quantity if item already exists
                $updateData = [
                    'quantity' => $newQuantity,
                    'price' => $price,
                ];
                
                // Update variant with new bottle count for bottle-based products
                // Preserve all tier information while updating bottles and capsules
                if ($product->is_bottle_based && $newBottles !== null && $cartItem->variant) {
                    $updatedVariant = $cartItem->variant;
                    // Preserve tier information if it exists
                    if (isset($cartItem->variant['tier'])) {
                        $updatedVariant['tier'] = $cartItem->variant['tier'];
                    }
                    // If new variant tier is provided and different, update it
                    if (isset($variant['tier']) && isset($variant['tier']['capsules'])) {
                        // Only update tier if it's actually different (different tier selection)
                        if (!isset($cartItem->variant['tier']) || $cartItem->variant['tier']['capsules'] != $variant['tier']['capsules']) {
                            $updatedVariant['tier'] = $variant['tier'];
                            // Update price to match new tier - ensure it's numeric
                            $tierPrice = $variant['tier']['price'];
                            $updateData['price'] = is_numeric($tierPrice) ? (float)$tierPrice : $cartItem->price;
                        }
                    }
                    
                    // If explicit price is provided in request, always use it (overrides tier price)
                    if ($request->has('price') && is_numeric($request->price)) {
                        $updateData['price'] = (float)$request->price;
                        Log::info('Overriding price with explicit request price in store', [
                            'cart_item_id' => $cartItem->id,
                            'price' => $updateData['price'],
                        ]);
                    }
                    $updatedVariant['bottles'] = $newBottles;
                    $updatedVariant['total_capsules'] = $newCapsules;
                    $updatedVariant['type'] = 'bottle'; // Ensure type is preserved
                    $updateData['variant'] = $updatedVariant;
                }
                
                $cartItem->update($updateData);
            } else {
                // Check if requested quantity exceeds available stock (only if stock_quantity is set)
                if ($product->stock_quantity !== null) {
                    // For bottle-based products, stock_quantity is in capsules
                    if ($product->is_bottle_based && $requestedBottles !== null) {
                        if ($requestedCapsules > $product->stock_quantity) {
                            $availableBottles = floor($product->stock_quantity / ($variant['tier']['capsules'] ?? $product->capsules_per_bottle ?? 50));
                            return back()->withErrors(['product' => "Only {$product->stock_quantity} capsules ({$availableBottles} " . ($availableBottles === 1 ? 'bottle' : 'bottles') . ") available in stock."]);
                        }
                    } else {
                        if ($request->quantity > $product->stock_quantity) {
                            return back()->withErrors(['product' => "Only {$product->stock_quantity} items available in stock."]);
                        }
                    }
                }
                
                // Create new cart item
                // Ensure variant contains all necessary tier information for bottle-based products
                $variantToSave = $variant;
                if ($product->is_bottle_based && $variant && isset($variant['tier'])) {
                    // Ensure variant has all required fields
                    if (!isset($variantToSave['type'])) {
                        $variantToSave['type'] = 'bottle';
                    }
                    // Ensure tier has all pricing information (price_per_capsule if not provided)
                    if (isset($variant['tier']) && !isset($variant['tier']['price_per_capsule']) && isset($variant['tier']['price']) && isset($variant['tier']['capsules']) && $variant['tier']['capsules'] > 0) {
                        $variantToSave['tier']['price_per_capsule'] = $variant['tier']['price'] / $variant['tier']['capsules'];
                    }
                }
                
                $cartItem = CartItem::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'product_id' => $product->id,
                    'quantity' => $request->quantity,
                    'price' => $price,
                    'variant' => $variantToSave,
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
            'variant' => 'nullable|array',
            'price' => 'nullable|numeric|min:0',
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
        if (!$product->in_stock || ($product->stock_quantity !== null && $product->stock_quantity <= 0)) {
            return back()->withErrors(['error' => 'This product is out of stock.']);
        }

        // Check if requested quantity exceeds available stock
        // For bottle-based products, handle tier updates even if variant doesn't exist yet (e.g., added from ProductCard)
        if ($product->is_bottle_based) {
            // Use tier from request if provided, otherwise use existing tier
            $tier = null;
            if ($request->has('variant') && isset($request->variant['tier'])) {
                $tier = $request->variant['tier'];
            } elseif ($cartItem->variant && isset($cartItem->variant['tier'])) {
                $tier = $cartItem->variant['tier'];
            }
            
            $capsulesPerBottle = $tier['capsules'] ?? $product->capsules_per_bottle ?? 50;
            $requestedCapsules = $request->quantity * $capsulesPerBottle;
            
            // Check stock if stock_quantity is set
            if ($product->stock_quantity !== null) {
                if ($requestedCapsules > $product->stock_quantity) {
                    $availableBottles = floor($product->stock_quantity / $capsulesPerBottle);
                    return back()->withErrors(['error' => "Only {$product->stock_quantity} capsules ({$availableBottles} " . ($availableBottles === 1 ? 'bottle' : 'bottles') . ") available in stock."]);
                }
            }
            
            // Update variant with new bottle count and/or tier
            // Initialize variant if it doesn't exist (e.g., when added from ProductCard)
            $updatedVariant = $cartItem->variant ?: [];
            
            // If a new variant (with tier) is provided, use it; otherwise preserve existing tier
            if ($request->has('variant') && isset($request->variant['tier'])) {
                // Update tier information from request
                $updatedVariant['tier'] = $request->variant['tier'];
                // Ensure price_per_capsule is calculated if missing
                if (!isset($updatedVariant['tier']['price_per_capsule']) && isset($updatedVariant['tier']['price']) && isset($updatedVariant['tier']['capsules']) && $updatedVariant['tier']['capsules'] > 0) {
                    $updatedVariant['tier']['price_per_capsule'] = $updatedVariant['tier']['price'] / $updatedVariant['tier']['capsules'];
                }
            } elseif (isset($cartItem->variant['tier'])) {
                // Preserve existing tier information
                $updatedVariant['tier'] = $cartItem->variant['tier'];
            }
            
            $updatedVariant['bottles'] = $request->quantity;
            $updatedVariant['total_capsules'] = $requestedCapsules;
            $updatedVariant['type'] = 'bottle'; // Ensure type is preserved
            
            // Update price - prioritize explicit price from request, then tier price, then keep existing
            $updatePrice = $cartItem->price;
            
            // Log all request data for debugging
            Log::info('Cart update request received', [
                'cart_item_id' => $cartItem->id,
                'request_all' => $request->all(),
                'has_price' => $request->has('price'),
                'request_price' => $request->input('price'),
                'request_price_type' => gettype($request->input('price')),
                'variant_tier_price' => $updatedVariant['tier']['price'] ?? null,
                'current_price' => $cartItem->price,
            ]);
            
            // First check if price is explicitly provided in request
            if ($request->has('price')) {
                $requestPrice = $request->input('price');
                if (is_numeric($requestPrice)) {
                    $updatePrice = (float)$requestPrice;
                    Log::info('Using explicit price from request', [
                        'cart_item_id' => $cartItem->id,
                        'price' => $updatePrice,
                        'original_value' => $requestPrice,
                    ]);
                } else {
                    Log::warning('Price in request is not numeric', [
                        'cart_item_id' => $cartItem->id,
                        'price_value' => $requestPrice,
                        'price_type' => gettype($requestPrice),
                    ]);
                }
            } elseif (isset($updatedVariant['tier']['price'])) {
                // Fall back to tier price if no explicit price (should be base price from frontend)
                $tierPrice = $updatedVariant['tier']['price'];
                // Convert to float if it's numeric (handles both string and numeric)
                if (is_numeric($tierPrice)) {
                    $updatePrice = (float)$tierPrice;
                } elseif (is_string($tierPrice) && is_numeric(trim($tierPrice))) {
                    $updatePrice = (float)trim($tierPrice);
                }
                Log::info('Using tier price from variant', [
                    'cart_item_id' => $cartItem->id,
                    'old_price' => $cartItem->price,
                    'new_price' => $updatePrice,
                    'tier_price' => $tierPrice,
                ]);
            } else {
                Log::warning('No price source found, keeping existing price', [
                    'cart_item_id' => $cartItem->id,
                    'current_price' => $cartItem->price,
                ]);
            }
            
            // Ensure price is properly formatted as decimal
            $finalPrice = is_numeric($updatePrice) ? (float)$updatePrice : $cartItem->price;
            
            // Directly set the attributes and save to ensure price is persisted
            $cartItem->quantity = $requestedCapsules;
            $cartItem->price = $finalPrice;
            $cartItem->variant = $updatedVariant;
            
            // Save and refresh
            $cartItem->save();
            $cartItem->refresh();
            
            // Verify the price was actually saved by querying fresh from DB
            $freshItem = CartItem::find($cartItem->id);
            $savedPrice = $freshItem ? $freshItem->price : $cartItem->price;
            
            Log::info('Cart item updated', [
                'cart_item_id' => $cartItem->id,
                'requested_price' => $updatePrice,
                'final_price' => $finalPrice,
                'saved_price' => $savedPrice,
                'saved_variant' => $cartItem->variant,
                'price_match' => abs($finalPrice - $savedPrice) < 0.01,
            ]);
            
            // If price still didn't save correctly, force update using DB query
            if (abs($finalPrice - $savedPrice) >= 0.01) {
                Log::warning('Price mismatch detected, forcing DB update', [
                    'cart_item_id' => $cartItem->id,
                    'expected' => $finalPrice,
                    'actual' => $savedPrice,
                ]);
                DB::table('cart_items')
                    ->where('id', $cartItem->id)
                    ->update(['price' => $finalPrice]);
                $cartItem->refresh();
            }
            
            return back()->with('success', 'Cart updated!');
        } else {
            // For regular products, check stock if stock_quantity is set
            if ($product->stock_quantity !== null) {
                if ($request->quantity > $product->stock_quantity) {
                    return back()->withErrors(['error' => "Only {$product->stock_quantity} items available in stock."]);
                }
            }
            
            // For non-bottle products, update quantity
            $cartItem->update([
                'quantity' => $request->quantity,
            ]);
            
            return back()->with('success', 'Cart updated!');
        }
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
