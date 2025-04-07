<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\CloudinaryService;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index()
    {
        $projects = Project::all();
        return response()->json($projects);
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request, CloudinaryService $cloudinary)
    {
        // Validate the incoming request

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'category'  => 'required|string',
            'image'    => 'nullable|image|max:5120', // expects a file upload
        ]);

        $imageId = null;
        $imageUrl = null;

        // If an image is provided, upload it to Cloudinary
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            Log::info('Project image received: ' . $file->getClientOriginalName());


            $upload = $cloudinary->uploadImage($file);

            if ($upload) {
                $imageId = $upload['public_id'];

                $imageUrl = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null for project image.');
            }
        }


        // Create the project with the Cloudinary data if available
        $project = Project::create([
            'title'      => $validated['title'],
            'description'   => $validated['description'],
            'category'   => $validated['category'],
            'image_id'  => $imageId,
            'image_url' => $imageUrl,
        ]);

        return response()->json($project, 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project)
    {
        return response()->json($project);
    }

    /**
     * Update the specified project in storage.
     */

    public function update(Request $request, Project $project, CloudinaryService $cloudinary)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'description'  => 'required|string|max:255',
            'category'  => 'required|string',
            'image'    => 'nullable|image|max:5120', // expects a file upload
        ]);

        // Check if there's a new image file in the request
        if ($request->hasFile('image')) {

            $file = $request->file('image');
            Log::info('Updating project image: ' . $file->getClientOriginalName());

            $upload = $cloudinary->uploadImage($file);
            if ($upload) {

                $validated['image_id']  = $upload['public_id'];
                $validated['image_url'] = $upload['url'];
            } else {
                Log::warning('Cloudinary upload returned null during project update.');
            }
        }


        $project->update($validated);

        return response()->json($project);
    }

    /**
     * Remove the specified project from storage.

     */
    public function destroy(Project $project)
    {

        // Optionally, add code to delete the image from Cloudinary

        $project->delete();
        return response()->json(null, 204);
    }
}
