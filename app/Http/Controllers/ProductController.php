<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true)
            ->with(['brand', 'category']);

        // Search
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->has('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Brand filter
        if ($request->has('brand')) {
            $query->where('brand_id', $request->get('brand'));
        }

        $products = $query->orderBy('sort_order')
            ->paginate(20);

        $categories = Category::where('is_active', true)->get();
        $brands = Brand::where('is_active', true)->get();

        return Inertia::render('Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'filters' => $request->only(['search', 'category', 'brand']),
        ]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->with(['brand', 'category'])
            ->firstOrFail();

        // Related products
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('is_active', true)
            ->with(['brand'])
            ->limit(4)
            ->get();

        return Inertia::render('Products/Show', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
        ]);
    }

    public function bestSellers()
    {
        $products = Product::where('is_best_seller', true)
            ->where('is_active', true)
            ->with(['brand', 'category'])
            ->orderBy('sort_order')
            ->paginate(20);

        return Inertia::render('Products/BestSellers', [
            'products' => $products,
        ]);
    }

    public function newArrivals()
    {
        $products = Product::where('is_new_arrival', true)
            ->where('is_active', true)
            ->with(['brand', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Products/NewArrivals', [
            'products' => $products,
        ]);
    }
}
