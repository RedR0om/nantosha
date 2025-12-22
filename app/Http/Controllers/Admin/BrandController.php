<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class BrandController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        $brands = Brand::withCount('products')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Brands/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
        ]);

        // Generate slug
        $validated['slug'] = Str::slug($validated['name']);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('logo'),
                    'brands'
                );
                $validated['logo'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Failed to upload logo: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['is_active'] = $validated['is_active'] ?? true;

        Brand::create($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand created successfully!');
    }

    public function edit(Brand $brand)
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => $brand,
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:5120',
            'is_active' => 'boolean',
        ]);

        // Generate slug if name changed
        if ($validated['name'] !== $brand->name) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        // Handle logo upload
        if ($request->hasFile('logo')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('logo'),
                    'brands'
                );
                $validated['logo'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['logo' => 'Failed to upload logo: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing logo if not uploading new one
            $validated['logo'] = $brand->logo;
        }

        $brand->update($validated);

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        // Check if brand has products
        if ($brand->products()->count() > 0) {
            return back()->withErrors(['error' => 'Cannot delete brand with associated products.']);
        }

        $brand->delete();

        return redirect()->route('admin.brands.index')
            ->with('success', 'Brand deleted successfully!');
    }
}
