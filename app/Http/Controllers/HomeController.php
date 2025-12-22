<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\CarouselSlide;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $carouselSlides = CarouselSlide::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $multiProductMode = Setting::isEnabled('multi_product_mode');

        if ($multiProductMode) {
            // Multiple products mode - show carousels
            $bestSellers = Product::where('is_best_seller', true)
                ->where('is_active', true)
                ->with(['brand', 'category'])
                ->orderBy('sort_order')
                ->limit(10)
                ->get();

            $newArrivals = Product::where('is_new_arrival', true)
                ->where('is_active', true)
                ->with(['brand', 'category'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            $customerFavorites = Product::where('is_customer_favorite', true)
                ->where('is_active', true)
                ->with(['brand', 'category'])
                ->orderBy('sort_order')
                ->limit(10)
                ->get();

            return Inertia::render('Home', [
                'carouselSlides' => $carouselSlides,
                'bestSellers' => $bestSellers,
                'newArrivals' => $newArrivals,
                'customerFavorites' => $customerFavorites,
                'multiProductMode' => true,
                'featuredProduct' => null,
            ]);
        } else {
            // Single product mode - show one featured product
            $featuredProduct = Product::where('is_featured', true)
                ->where('is_active', true)
                ->with(['brand', 'category'])
                ->orderBy('sort_order')
                ->first();

            // If no featured product, get the first active product
            if (!$featuredProduct) {
                $featuredProduct = Product::where('is_active', true)
                    ->with(['brand', 'category'])
                    ->orderBy('created_at', 'desc')
                    ->first();
            }

            return Inertia::render('Home', [
                'carouselSlides' => $carouselSlides,
                'bestSellers' => collect(),
                'newArrivals' => collect(),
                'customerFavorites' => collect(),
                'multiProductMode' => false,
                'featuredProduct' => $featuredProduct,
            ]);
        }
    }
}
