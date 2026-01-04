<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DrLandritoProfileContent;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DrLandritoProfileController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contents = DrLandritoProfileContent::orderBy('section_type')
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('section_type');

        return Inertia::render('Admin/DrLandritoProfile/Index', [
            'contents' => $contents,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/DrLandritoProfile/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Handle bulk items (awards, publications, photos)
        if ($request->has('items')) {
            // Handle JSON string or array
            $itemsData = $request->input('items');
            if (is_string($itemsData)) {
                $itemsData = json_decode($itemsData, true);
            }
            
            if (!is_array($itemsData) || empty($itemsData)) {
                return back()->withErrors(['items' => 'Items must be a non-empty array']);
            }
            
            $baseData = $request->validate([
                'section_type' => 'required|string|in:award,publication,photo',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            // Validate items manually
            $items = [];
            foreach ($itemsData as $index => $item) {
                $items[] = [
                    'title' => $item['title'] ?? null,
                    'text' => $item['text'] ?? null,
                    'caption' => $item['caption'] ?? null,
                    'description' => $item['description'] ?? null,
                    'image_url' => $item['image_url'] ?? null,
                    'image_alt' => $item['image_alt'] ?? null,
                    'key' => $item['key'] ?? null,
                    'sort_order' => $item['sort_order'] ?? null,
                ];
            }
            $baseData['is_active'] = $baseData['is_active'] ?? true;
            $baseData['sort_order'] = $baseData['sort_order'] ?? 0;

            // Handle multiple image uploads
            $images = $request->file('images', []);
            
            // Create multiple entries
            foreach ($items as $index => $item) {
                $entryData = [
                    'section_type' => $baseData['section_type'],
                    'title' => $item['title'] ?? null,
                    'text' => $item['text'] ?? null,
                    'caption' => $item['caption'] ?? null,
                    'description' => $item['description'] ?? null,
                    'image_url' => $item['image_url'] ?? null,
                    'image_alt' => $item['image_alt'] ?? null,
                    'key' => $item['key'] ?? null,
                    'sort_order' => $item['sort_order'] ?? ($baseData['sort_order'] + $index),
                    'is_active' => $baseData['is_active'],
                ];

                // Handle image upload if provided
                if (isset($images[$index]) && $images[$index]) {
                    try {
                        $uploadResult = $this->cloudinaryService->uploadImage(
                            $images[$index],
                            'dr-landrito-profile'
                        );
                        $entryData['image_url'] = $uploadResult['secure_url'];
                    } catch (\Exception $e) {
                        return back()->withErrors(['images' => 'Failed to upload image ' . ($index + 1) . ': ' . $e->getMessage()]);
                    }
                }

                DrLandritoProfileContent::create($entryData);
            }

            return redirect()->route('admin.dr-landrito-profile.index')
                ->with('success', count($items) . ' items created successfully!');
        }

        // Regular single entry
        $validated = $request->validate([
            'section_type' => 'required|string|in:page_title,profile_header,education,career,clinical_practice,current_focus,award,publication,photo,newsletter_info,medical_professionals',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'text' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:1000',
            'image_alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload to Cloudinary
        if ($request->hasFile('image')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'dr-landrito-profile'
                );
                $validated['image_url'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        // Remove the image file from validated array (we only need the URL)
        unset($validated['image']);

        DrLandritoProfileContent::create($validated);

        return redirect()->route('admin.dr-landrito-profile.index')
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
    public function edit(string $drLandritoProfile)
    {
        $content = DrLandritoProfileContent::findOrFail($drLandritoProfile);
        return Inertia::render('Admin/DrLandritoProfile/Edit', [
            'content' => $content,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $drLandritoProfile)
    {
        $content = DrLandritoProfileContent::findOrFail($drLandritoProfile);
        
        $validated = $request->validate([
            'section_type' => 'required|string|in:page_title,profile_header,education,career,clinical_practice,current_focus,award,publication,photo,newsletter_info,medical_professionals',
            'key' => 'nullable|string|max:255',
            'title' => 'nullable|string|max:500',
            'subtitle' => 'nullable|string|max:500',
            'text' => 'nullable|string',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'image_url' => 'nullable|string|max:1000',
            'image_alt' => 'nullable|string|max:255',
            'caption' => 'nullable|string|max:500',
            'content' => 'nullable|array',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload to Cloudinary if new image is provided
        if ($request->hasFile('image')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'dr-landrito-profile'
                );
                $validated['image_url'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing image URL if no new image is uploaded
            $validated['image_url'] = $validated['image_url'] ?? $content->image_url;
        }

        // Remove the image file from validated array (we only need the URL)
        unset($validated['image']);

        $content->update($validated);

        return redirect()->route('admin.dr-landrito-profile.index')
            ->with('success', 'Content updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $drLandritoProfile)
    {
        $content = DrLandritoProfileContent::findOrFail($drLandritoProfile);
        $content->delete();

        return redirect()->route('admin.dr-landrito-profile.index')
            ->with('success', 'Content deleted successfully!');
    }
}
