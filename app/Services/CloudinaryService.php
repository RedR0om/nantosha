<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    private $cloudinary;
    private $uploadApi;

    public function __construct()
    {
        // Try to get CLOUDINARY_URL from config first, then env
        // This allows for better caching support
        $cloudinaryUrl = config('services.cloudinary.url') ?? env('CLOUDINARY_URL');
        
        // Trim any whitespace that might be in the .env file
        if ($cloudinaryUrl) {
            $cloudinaryUrl = trim($cloudinaryUrl);
        }
        
        if (empty($cloudinaryUrl)) {
            throw new \Exception(
                'CLOUDINARY_URL is not set in .env file. ' .
                'Please add: CLOUDINARY_URL=cloudinary://api_key:api_secret@cloud_name ' .
                'to your .env file and run: php artisan config:clear'
            );
        }

        // Parse the URL: cloudinary://api_key:api_secret@cloud_name
        // Example: cloudinary://723299834825121:avs3HoeDhsGRRhbzoIa2yl-V02s@dkcjftn5c
        if (!preg_match('/cloudinary:\/\/([^:]+):([^@]+)@(.+)/', $cloudinaryUrl, $matches)) {
            throw new \Exception(
                'Invalid CLOUDINARY_URL format. ' .
                'Expected: cloudinary://api_key:api_secret@cloud_name ' .
                'Got: ' . substr($cloudinaryUrl, 0, 50) . '...'
            );
        }

        if (count($matches) !== 4) {
            throw new \Exception('Invalid CLOUDINARY_URL format - failed to parse components');
        }

        // Initialize Cloudinary directly with the URL string
        // This is the simplest and most reliable method
        $this->cloudinary = new Cloudinary($cloudinaryUrl);
        
        // Get the UploadApi from the Cloudinary instance
        $this->uploadApi = $this->cloudinary->uploadApi();
    }

    /**
     * Upload an image file to Cloudinary
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadImage($file, string $folder = 'products', array $options = []): array
    {
        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => 'image',
            'overwrite' => true,
            'invalidate' => true,
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        if ($file instanceof UploadedFile) {
            $result = $this->uploadApi->upload($file->getRealPath(), $uploadOptions);
        } else {
            $result = $this->uploadApi->upload($file, $uploadOptions);
        }

        return [
            'public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
            'url' => $result['url'],
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
            'format' => $result['format'] ?? null,
        ];
    }

    /**
     * Upload multiple images
     *
     * @param array $files
     * @param string $folder
     * @return array
     */
    public function uploadImages(array $files, string $folder = 'products'): array
    {
        $uploaded = [];
        
        foreach ($files as $file) {
            $uploaded[] = $this->uploadImage($file, $folder);
        }

        return $uploaded;
    }

    /**
     * Upload a video file to Cloudinary
     *
     * @param UploadedFile|string $file
     * @param string $folder
     * @param array $options
     * @return array
     */
    public function uploadVideo($file, string $folder = 'carousel', array $options = []): array
    {
        $defaultOptions = [
            'folder' => $folder,
            'resource_type' => 'video',
            'overwrite' => true,
            'invalidate' => true,
        ];

        $uploadOptions = array_merge($defaultOptions, $options);

        if ($file instanceof UploadedFile) {
            // Ensure the file exists and is readable
            $filePath = $file->getRealPath();
            if (!$filePath || !file_exists($filePath)) {
                throw new \Exception('Video file does not exist or cannot be read: ' . $file->getClientOriginalName());
            }
            
            // Log file info for debugging
            \Log::info('Uploading video to Cloudinary', [
                'filename' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'path' => $filePath,
            ]);
            
            $result = $this->uploadApi->upload($filePath, $uploadOptions);
        } else {
            if (!file_exists($file)) {
                throw new \Exception('Video file does not exist: ' . $file);
            }
            $result = $this->uploadApi->upload($file, $uploadOptions);
        }

        return [
            'public_id' => $result['public_id'],
            'secure_url' => $result['secure_url'],
            'url' => $result['url'],
            'width' => $result['width'] ?? null,
            'height' => $result['height'] ?? null,
            'format' => $result['format'] ?? null,
            'duration' => $result['duration'] ?? null,
        ];
    }

    /**
     * Delete an image from Cloudinary
     *
     * @param string $publicId
     * @return array
     */
    public function deleteImage(string $publicId): array
    {
        return $this->uploadApi->destroy($publicId);
    }
}

