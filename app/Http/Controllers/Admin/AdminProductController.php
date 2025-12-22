<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Category;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        $products = Product::with(['brand', 'category'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    public function create()
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Products/Create', [
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'price_per_capsule' => 'nullable|numeric|min:0',
            'price_per_mg' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku',
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock' => 'boolean',
            'image' => 'nullable|image|max:5120', // 5MB max
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_customer_favorite' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'supplement_facts' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'directions' => 'nullable|string',
            'warnings' => 'nullable|string',
            'country_of_origin' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle main image upload
        if ($request->hasFile('image')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'products'
                );
                $validated['image'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            try {
                $uploadedImages = $this->cloudinaryService->uploadImages(
                    $request->file('images'),
                    'products'
                );
                $validated['images'] = array_column($uploadedImages, 'secure_url');
            } catch (\Exception $e) {
                return back()->withErrors(['images' => 'Failed to upload images: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['in_stock'] = $validated['in_stock'] ?? true;
        $validated['is_featured'] = $validated['is_featured'] ?? false;
        $validated['is_best_seller'] = $validated['is_best_seller'] ?? false;
        $validated['is_new_arrival'] = $validated['is_new_arrival'] ?? false;
        $validated['is_customer_favorite'] = $validated['is_customer_favorite'] ?? false;
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $product = Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully!');
    }

    public function edit(Product $product)
    {
        $brands = Brand::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load(['brand', 'category']),
            'brands' => $brands,
            'categories' => $categories,
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'nullable|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'price_per_capsule' => 'nullable|numeric|min:0',
            'price_per_mg' => 'nullable|numeric|min:0',
            'sku' => 'nullable|string|max:255|unique:products,sku,' . $product->id,
            'stock_quantity' => 'nullable|integer|min:0',
            'in_stock' => 'boolean',
            'image' => 'nullable|image|max:5120',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_customer_favorite' => 'boolean',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
            'supplement_facts' => 'nullable|string',
            'ingredients' => 'nullable|string',
            'directions' => 'nullable|string',
            'warnings' => 'nullable|string',
            'country_of_origin' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string',
        ]);

        // Generate slug if name changed
        if ($validated['name'] !== $product->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle main image upload
        if ($request->hasFile('image')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'products'
                );
                $validated['image'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing image if not uploading new one
            $validated['image'] = $product->image;
        }

        // Handle multiple images upload
        if ($request->hasFile('images')) {
            try {
                $uploadedImages = $this->cloudinaryService->uploadImages(
                    $request->file('images'),
                    'products'
                );
                $newImageUrls = array_column($uploadedImages, 'secure_url');
                // Merge with existing images if any
                $existingImages = $product->images ?? [];
                $validated['images'] = array_merge($existingImages, $newImageUrls);
            } catch (\Exception $e) {
                return back()->withErrors(['images' => 'Failed to upload images: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing images if not uploading new ones
            $validated['images'] = $product->images;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully!');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted successfully!');
    }
}

