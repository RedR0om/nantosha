<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CheckoutContent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutContentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = CheckoutContent::orderBy('section_type')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('section_type');

        return Inertia::render('Admin/CheckoutContent/Index', [
            'contents' => $contents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/CheckoutContent/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle bulk creation for risks_prohibitions
        if ($request->has('items') && is_array($request->items)) {
            $validated = $request->validate([
                'section_type' => 'required|string|in:risks_prohibitions,shipping_customs',
                'items' => 'required|array|min:1',
                'items.*.key' => 'required|string|max:255',
                'items.*.title' => 'nullable|string|max:500',
                'items.*.content' => 'nullable|string',
                'items.*.sort_order' => 'nullable|integer|min:0',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $isActive = $validated['is_active'] ?? true;
            $baseSortOrder = $validated['sort_order'] ?? 0;

            foreach ($validated['items'] as $item) {
                CheckoutContent::create([
                    'section_type' => $validated['section_type'],
                    'key' => $item['key'],
                    'title' => $item['title'] ?? null,
                    'content' => $item['content'] ?? null,
                    'description' => null,
                    'sort_order' => ($item['sort_order'] ?? 0) + $baseSortOrder,
                    'is_active' => $isActive,
                ]);
            }

            return redirect()->route('admin.checkout-content.index')
                ->with('success', 'Content created successfully!');
        }

        // Regular single entry creation
        $validated = $request->validate([
            'section_type' => 'required|string|in:risks_prohibitions,shipping_customs',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Set defaults
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        CheckoutContent::create($validated);

        return redirect()->route('admin.checkout-content.index')
            ->with('success', 'Content created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $checkoutContent)
    {
        $content = CheckoutContent::findOrFail($checkoutContent);
        
        // For risks_prohibitions, load all related items
        $allItems = null;
        if ($content->section_type === 'risks_prohibitions') {
            $allItems = CheckoutContent::where('section_type', 'risks_prohibitions')
                ->orderBy('sort_order')
                ->get();
        }
        
        return Inertia::render('Admin/CheckoutContent/Edit', [
            'content' => $content,
            'allItems' => $allItems,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $checkoutContent)
    {
        $content = CheckoutContent::findOrFail($checkoutContent);
        
        // Handle bulk update for risks_prohibitions
        if ($request->has('items') && is_array($request->items)) {
            $validated = $request->validate([
                'section_type' => 'required|string|in:risks_prohibitions,shipping_customs',
                'items' => 'required|array|min:1',
                'items.*.id' => 'nullable|integer|exists:checkout_contents,id',
                'items.*.key' => 'required|string|max:255',
                'items.*.title' => 'nullable|string|max:500',
                'items.*.content' => 'nullable|string',
                'items.*.sort_order' => 'nullable|integer|min:0',
                'delete_ids' => 'nullable|array',
                'delete_ids.*' => 'integer|exists:checkout_contents,id',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $isActive = $validated['is_active'] ?? true;
            $baseSortOrder = $validated['sort_order'] ?? 0;

            // Delete items that are no longer needed
            if (isset($validated['delete_ids']) && is_array($validated['delete_ids'])) {
                CheckoutContent::whereIn('id', $validated['delete_ids'])
                    ->where('section_type', $validated['section_type'])
                    ->delete();
            }

            // Update or create items
            foreach ($validated['items'] as $item) {
                if (isset($item['id']) && $item['id']) {
                    // Update existing item
                    CheckoutContent::where('id', $item['id'])
                        ->update([
                            'key' => $item['key'],
                            'title' => $item['title'] ?? null,
                            'content' => $item['content'] ?? null,
                            'sort_order' => ($item['sort_order'] ?? 0) + $baseSortOrder,
                            'is_active' => $isActive,
                        ]);
                } else {
                    // Create new item
                    CheckoutContent::create([
                        'section_type' => $validated['section_type'],
                        'key' => $item['key'],
                        'title' => $item['title'] ?? null,
                        'content' => $item['content'] ?? null,
                        'description' => null,
                        'sort_order' => ($item['sort_order'] ?? 0) + $baseSortOrder,
                        'is_active' => $isActive,
                    ]);
                }
            }

            return redirect()->route('admin.checkout-content.index')
                ->with('success', 'Content updated successfully!');
        }

        // Regular single entry update
        $validated = $request->validate([
            'section_type' => 'required|string|in:risks_prohibitions,shipping_customs',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        $content->update($validated);

        return redirect()->route('admin.checkout-content.index')
            ->with('success', 'Content updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $checkoutContent)
    {
        $content = CheckoutContent::findOrFail($checkoutContent);
        $content->delete();

        return redirect()->route('admin.checkout-content.index')
            ->with('success', 'Content deleted successfully!');
    }
}
