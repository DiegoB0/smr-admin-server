<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class TestimonialController extends Controller
{
    /**
     * Display a listing of testimonials.
     */
    public function index()
    {
        $testimonials = Testimonial::all();
        return response()->json($testimonials);
    }

    /**
     * Store a newly created testimonial in storage.
     */
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        // Validate the incoming request

        $validated = $request->validate([

            'name'     => 'required|string|max:255',
            'company'  => 'required|string|max:255',
            'message'  => 'required|string',
            'rating'   => 'nullable|integer|min:1|max:5',
            'image'    => 'nullable|image|max:5120', // expects a file upload
        ]);

        $imageId = null;
        $imageUrl = null;

        // If an image is provided, upload it to Cloudinary
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('Testimonial image received: ' . $file->getClientOriginalName());


            $upload = $cloudinary->uploadImage($file);

            if ($upload) {
                $imageId = $upload['public_id'];

                $imageUrl = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null for testimonial image.');
            }
        }


        // Create the testimonial with the Cloudinary data if available
        $testimonial = Testimonial::create([
            'name'      => $validated['name'],
            'company'   => $validated['company'],
            'message'   => $validated['message'],
            'rating'    => $validated['rating'] ?? 5,
            'image_id'  => $imageId,
            'image_url' => $imageUrl,
        ]);

        return response()->json($testimonial, 201);
    }

    /**
     * Display the specified testimonial.
     */
    public function show(Testimonial $testimonial)
    {
        return response()->json($testimonial);
    }

    /**
     * Update the specified testimonial in storage.
     */

    public function update(Request $request, Testimonial $testimonial, CloudinaryService $cloudinary)
    {
        $validated = $request->validate([
            'name'     => 'sometimes|required|string|max:255',

            'company'  => 'sometimes|required|string|max:255',
            'message'  => 'sometimes|required|string',
            'rating'   => 'sometimes|integer|min:1|max:5',
            'image'    => 'nullable|image|max:5120', // optional new image file
        ]);

        // Check if there's a new image file in the request
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            Log::info('Updating testimonial image: ' . $file->getClientOriginalName());

            $upload = $cloudinary->uploadImage($file);
            if ($upload) {

                $validated['image_id']  = $upload['public_id'];
                $validated['image_url'] = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null during testimonial update.');
            }
        }


        $testimonial->update($validated);

        return response()->json($testimonial);
    }

    /**
     * Remove the specified testimonial from storage.

     */
    public function destroy(Testimonial $testimonial)
    {

        // Optionally, add code to delete the image from Cloudinary

        $testimonial->delete();
        return response()->json(null, 204);
    }
}
