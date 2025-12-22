<?php

namespace App\Http\Middleware;

use App\Models\CartItem;
use Illuminate\Foundation\Inspiring;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        [$message, $author] = str(Inspiring::quotes()->random())->explode('-');

        // Get cart data for all pages
        $cartItems = $this->getCartItems($request);
        $cartItems->load('product.brand', 'product.category');
        
        $cartSubtotal = $cartItems->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $cartTax = $cartSubtotal * 0.08;
        $cartShipping = $cartSubtotal >= 10000 ? 0 : 500; // Free shipping over ¥10,000
        $cartTotal = $cartSubtotal + $cartTax + $cartShipping;
        $cartCount = $cartItems->sum('quantity');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'quote' => ['message' => trim($message), 'author' => trim($author)],
            'auth' => [
                'user' => $request->user(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'cart' => [
                'items' => $cartItems,
                'count' => $cartCount,
                'subtotal' => $cartSubtotal,
                'tax' => $cartTax,
                'shipping' => $cartShipping,
                'total' => $cartTotal,
            ],
        ];
    }

    private function getCartItems(Request $request)
    {
        if (Auth::check()) {
            return CartItem::where('user_id', Auth::id())->get();
        } else {
            $sessionId = $request->session()->getId();
            return CartItem::where('session_id', $sessionId)->get();
        }
    }
}
