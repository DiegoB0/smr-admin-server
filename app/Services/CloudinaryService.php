<?php

// phpcs:ignoreFile

namespace App\Services;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;

class CloudinaryService
{
    public function uploadImage($file)
    {
        try {
            if (!$file->isValid()) {
                Log::warning('Uploaded file is not valid.');

                return null;
            }


            // Attempt to upload to Cloudinary
            $uploadedFile = Cloudinary::uploadApi()->upload($file->getRealPath());

            Log::info('Cloudinary upload result:', (array) $uploadedFile);

            return [

                'url' => $uploadedFile['secure_url'] ?? null,
                'public_id' => $uploadedFile['public_id'] ?? null,
            ];
        } catch (\Exception $e) {
            Log::error('Error uploading to Cloudinary: ' . $e->getMessage());
            return null;
        }
    }
}
