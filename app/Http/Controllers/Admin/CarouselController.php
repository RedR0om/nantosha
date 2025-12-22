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
        // Check PHP upload limits first
        $maxUploadSize = min(
            (int)ini_get('upload_max_filesize') * 1024 * 1024,
            (int)ini_get('post_max_size') * 1024 * 1024
        );
        
        \Log::info('PHP upload limits', [
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
            'calculated_max' => $maxUploadSize,
            'content_length' => $request->header('Content-Length'),
        ]);

        // Check if request is too large
        if ($request->header('Content-Length') && (int)$request->header('Content-Length') > $maxUploadSize) {
            \Log::error('Request exceeds PHP upload limits', [
                'content_length' => $request->header('Content-Length'),
                'max_upload_size' => $maxUploadSize,
            ]);
            return back()->withErrors(['video' => 'File size exceeds server limit. Maximum allowed: ' . ini_get('upload_max_filesize')]);
        }

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
                // Check if file was uploaded but failed validation
                if ($request->has('video')) {
                    \Log::warning('Video field present but not a file', [
                        'video_value' => $request->input('video'),
                    ]);
                    return back()->withErrors(['video' => 'Video file upload failed. Please check file size (max 50MB) and format (MP4, MOV, AVI, WMV, FLV, WebM).']);
                }
                return back()->withErrors(['video' => 'Video is required when media type is video.']);
            }
            
            // Additional validation for video file
            $videoFile = $request->file('video');
            if (!$videoFile->isValid()) {
                $errorMessage = $videoFile->getErrorMessage();
                \Log::error('Video file validation failed', [
                    'error_code' => $videoFile->getError(),
                    'error_message' => $errorMessage,
                    'file_size' => $videoFile->getSize(),
                ]);
                return back()->withErrors(['video' => 'Video file is invalid: ' . $errorMessage]);
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
                $videoFile = $request->file('video');
                
                // Check if file was actually uploaded
                if (!$videoFile->isValid()) {
                    \Log::error('Video file is not valid', [
                        'error' => $videoFile->getError(),
                        'error_message' => $videoFile->getErrorMessage(),
                    ]);
                    return back()->withErrors(['video' => 'Video file is not valid: ' . $videoFile->getErrorMessage()]);
                }
                
                \Log::info('Starting video upload to Cloudinary', [
                    'filename' => $videoFile->getClientOriginalName(),
                    'size' => $videoFile->getSize(),
                    'mime' => $videoFile->getMimeType(),
                    'real_path' => $videoFile->getRealPath(),
                ]);
                
                $uploadResult = $this->cloudinaryService->uploadVideo(
                    $videoFile,
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
                    'file_size' => $request->file('video')?->getSize(),
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
