<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use App\Services\CloudinaryService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CarouselController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index()
    {
        $slides = CarouselSlide::orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Admin/Carousel/Index', [
            'slides' => $slides,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Carousel/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:5120', // 5MB max
            'video' => 'nullable|mimes:mp4,mov,avi,wmv,flv,webm|max:51200', // 50MB max for videos
            'media_type' => 'required|string|in:image,video',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Clear the opposite media type based on selection
        if ($validated['media_type'] === 'image') {
            $validated['video'] = null;
            // Validate image is provided
            if (!$request->hasFile('image')) {
                return back()->withErrors(['image' => 'Image is required when media type is image.']);
            }
        } elseif ($validated['media_type'] === 'video') {
            $validated['image'] = null;
            // Validate video is provided
            if (!$request->hasFile('video')) {
                return back()->withErrors(['video' => 'Video is required when media type is video.']);
            }
        }

        // Handle image upload to Cloudinary
        if ($request->hasFile('image') && $validated['media_type'] === 'image') {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'carousel'
                );
                $validated['image'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                \Log::error('Image upload failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'file' => $request->file('image')?->getClientOriginalName(),
                ]);
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        }

        // Handle video upload to Cloudinary
        if ($request->hasFile('video') && $validated['media_type'] === 'video') {
            try {
                \Log::info('Starting video upload to Cloudinary', [
                    'filename' => $request->file('video')->getClientOriginalName(),
                    'size' => $request->file('video')->getSize(),
                    'mime' => $request->file('video')->getMimeType(),
                ]);
                
                $uploadResult = $this->cloudinaryService->uploadVideo(
                    $request->file('video'),
                    'carousel'
                );
                
                \Log::info('Video upload successful', [
                    'public_id' => $uploadResult['public_id'] ?? null,
                    'secure_url' => $uploadResult['secure_url'] ?? null,
                ]);
                
                $validated['video'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                \Log::error('Video upload failed: ' . $e->getMessage(), [
                    'exception' => $e,
                    'file' => $request->file('video')?->getClientOriginalName(),
                    'trace' => $e->getTraceAsString(),
                ]);
                return back()->withErrors(['video' => 'Failed to upload video: ' . $e->getMessage()]);
            }
        }

        // Set defaults
        $validated['is_active'] = $validated['is_active'] ?? true;
        $validated['sort_order'] = $validated['sort_order'] ?? 0;
        $validated['media_type'] = $validated['media_type'] ?? 'image';

        CarouselSlide::create($validated);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel slide created successfully!');
    }

    public function edit(CarouselSlide $carousel)
    {
        return Inertia::render('Admin/Carousel/Edit', [
            'slide' => $carousel,
        ]);
    }

    public function update(Request $request, CarouselSlide $carousel)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'subtitle' => 'nullable|string',
            'image' => 'nullable|image|max:5120',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv,flv,webm|max:51200', // 50MB max for videos
            'media_type' => 'required|string|in:image,video',
            'link' => 'nullable|string|max:255',
            'button_text' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        // Handle image upload to Cloudinary if new image is provided
        if ($request->hasFile('image')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadImage(
                    $request->file('image'),
                    'carousel'
                );
                $validated['image'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['image' => 'Failed to upload image: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing image if no new image is uploaded and media_type is image
            if ($validated['media_type'] === 'image') {
                $validated['image'] = $carousel->image;
            } else {
                // Clear image if switching to video
                $validated['image'] = null;
            }
        }

        // Handle video upload to Cloudinary if new video is provided
        if ($request->hasFile('video')) {
            try {
                $uploadResult = $this->cloudinaryService->uploadVideo(
                    $request->file('video'),
                    'carousel'
                );
                $validated['video'] = $uploadResult['secure_url'];
            } catch (\Exception $e) {
                return back()->withErrors(['video' => 'Failed to upload video: ' . $e->getMessage()]);
            }
        } else {
            // Keep existing video if no new video is uploaded and media_type is video
            if ($validated['media_type'] === 'video') {
                $validated['video'] = $carousel->video;
            } else {
                // Clear video if switching to image
                $validated['video'] = null;
            }
        }

        // Validate that required media exists based on media_type
        if ($validated['media_type'] === 'image' && empty($validated['image'])) {
            return back()->withErrors(['image' => 'Image is required when media type is image.']);
        }
        if ($validated['media_type'] === 'video' && empty($validated['video'])) {
            return back()->withErrors(['video' => 'Video is required when media type is video.']);
        }

        // Set media_type default
        $validated['media_type'] = $validated['media_type'] ?? 'image';

        $carousel->update($validated);

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel slide updated successfully!');
    }

    public function destroy(CarouselSlide $carousel)
    {
        $carousel->delete();

        return redirect()->route('admin.carousel.index')
            ->with('success', 'Carousel slide deleted successfully!');
    }
}
