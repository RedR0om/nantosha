<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['brand', 'category'])
            ->orderBy('name');

        // Filter by stock status
        if ($request->has('stock_status')) {
            switch ($request->stock_status) {
                case 'in_stock':
                    $query->where('in_stock', true)->where('stock_quantity', '>', 0);
                    break;
                case 'out_of_stock':
                    $query->where(function ($q) {
                        $q->where('in_stock', false)
                          ->orWhere('stock_quantity', '<=', 0);
                    });
                    break;
                case 'low_stock':
                    $query->where('stock_quantity', '>', 0)
                          ->where('stock_quantity', '<=', 10);
                    break;
            }
        }

        // Search
        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('sku', 'like', '%' . $request->search . '%');
            });
        }

        $products = $query->paginate(20);

        // Calculate inventory stats
        $stats = [
            'total_products' => Product::count(),
            'in_stock' => Product::where('in_stock', true)->where('stock_quantity', '>', 0)->count(),
            'out_of_stock' => Product::where(function ($q) {
                $q->where('in_stock', false)->orWhere('stock_quantity', '<=', 0);
            })->count(),
            'low_stock' => Product::where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 10)
                ->count(),
            'total_quantity' => Product::sum('stock_quantity'),
        ];

        return Inertia::render('Admin/Inventory/Index', [
            'products' => $products,
            'stats' => $stats,
            'filters' => $request->only(['search', 'stock_status']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock_quantity' => 'required|integer|min:0',
            'in_stock' => 'boolean',
        ]);

        // Auto-update in_stock based on quantity
        if ($validated['stock_quantity'] > 0) {
            $validated['in_stock'] = true;
        } else {
            $validated['in_stock'] = false;
        }

        $product->update($validated);

        return redirect()->back()->with('success', 'Inventory updated successfully!');
    }

    public function bulkUpdate(Request $request)
    {
        $validated = $request->validate([
            'products' => 'required|array',
            'products.*.id' => 'required|exists:products,id',
            'products.*.stock_quantity' => 'required|integer|min:0',
            'products.*.in_stock' => 'boolean',
        ]);

        foreach ($validated['products'] as $productData) {
            $product = Product::find($productData['id']);
            
            $updateData = [
                'stock_quantity' => $productData['stock_quantity'],
            ];

            // Auto-update in_stock based on quantity
            if ($productData['stock_quantity'] > 0) {
                $updateData['in_stock'] = true;
            } else {
                $updateData['in_stock'] = false;
            }

            $product->update($updateData);
        }

        return redirect()->back()->with('success', 'Inventory updated successfully!');
    }
}

