<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class ServiceController extends Controller
{
    /**
     * Display a listing of services.
     */
    public function index()
    {
        $services = Service::all();
        return response()->json($services);
    }

    /**
     * Store a newly created services in storage.
     */
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        // Validate the incoming request

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'icon'  => 'required|string',
            'image'    => 'nullable|image|max:5120', // expects a file upload
        ]);

        $imageId = null;
        $imageUrl = null;

        // If an image is provided, upload it to Cloudinary
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('Service image received: ' . $file->getClientOriginalName());


            $upload = $cloudinary->uploadImage($file);

            if ($upload) {
                $imageId = $upload['public_id'];

                $imageUrl = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null for service image.');
            }
        }


        // Create the service with the Cloudinary data if available
        $service = Service::create([
            'title'      => $validated['title'],
            'description'   => $validated['description'],
            'icon'   => $validated['icon'],
            'image_id'  => $imageId,
            'image_url' => $imageUrl,
        ]);

        return response()->json($service, 201);
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        return response()->json($service);
    }

    /**
     * Update the specified service in storage.
     */

    public function update(Request $request, Service $service, CloudinaryService $cloudinary)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'icon'  => 'required|string',
            'image'    => 'nullable|image|max:5120', // expects a file upload
        ]);

        // Check if there's a new image file in the request
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            Log::info('Updating service image: ' . $file->getClientOriginalName());

            $upload = $cloudinary->uploadImage($file);
            if ($upload) {

                $validated['image_id']  = $upload['public_id'];
                $validated['image_url'] = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null during service update.');
            }
        }


        $service->update($validated);

        return response()->json($service);
    }

    /**
     * Remove the specified service from storage.

     */
    public function destroy(Service $service)
    {

        // Optionally, add code to delete the image from Cloudinary

        $service->delete();
        return response()->json(null, 204);
    }
}
