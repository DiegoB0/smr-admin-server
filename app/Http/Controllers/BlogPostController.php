<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Services\CloudinaryService;
use App\Models\User;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BlogPostController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            Log::error('User is not an instance of App\Models\User.');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        if (!$user->hasPermission('read-post')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        if ($user->hasRole('admin')) {
            // Admin sees all posts
            return response()->json(BlogPost::with('user')->get());
        }

        // Bloggers see only their posts
        return response()->json(BlogPost::with('user')->where('user_id', $user->id)->get());
    }

    public function store(Request $request, CloudinaryService $cloudinary)
    {
        $user = Auth::user();

        if (!$user instanceof User) {
            Log::error('User is not an instance of App\Models\User.');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        if (!$user->hasPermission('create-post')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $request->validate([
                'title' => 'required|string|max:255',
                'post_message' => 'required|string',
                'post_image' => 'nullable|image|max:5120',
            ]);

        $imageId = null;
        $imageUrl = null;

        if ($request->hasFile('post_image')) {
            $upload = $cloudinary->uploadImage($request->file('post_image'));
            $imageId = $upload['public_id'];
            $imageUrl = $upload['url'];
        }

        $post = BlogPost::create([
            'title' => $request->title,
            'post_message' => $request->post_message,
            'image_id' => $imageId,
            'image_url' => $imageUrl,
            'user_id' => Auth::id(),
        ]);

        return response()->json($post, 201);
    }

    public function show(BlogPost $blogPost)
    {
        $user = Auth::user();

        if (!$this->canAccessPost($user, $blogPost, 'read-post')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // if (!$this->canAccessPost($user, $blogPost)) {
        //     return response()->json(['message' => 'Forbidden'], 403);
        // }

        return response()->json($blogPost->load('user'));
    }

    public function update(Request $request, BlogPost $blogPost)
    {
        $user = Auth::user();


        if (!$this->canAccessPost($user, $blogPost, 'edit-post')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // if (!$this->canAccessPost($user, $blogPost)) {
        //     return response()->json(['message' => 'Forbidden'], 403);
        // }

        $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'post_message' => 'sometimes|required|string',
            'post_image' => 'nullable|string',
        ]);

        $blogPost->update($request->only('title', 'post_message', 'post_image'));

        return response()->json($blogPost);
    }

    public function destroy(BlogPost $blogPost)
    {
        $user = Auth::user();

        if (!$this->canAccessPost($user, $blogPost, 'delete-post')) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        // if (!$this->canAccessPost($user, $blogPost)) {
        //     return response()->json(['message' => 'Forbidden'], 403);
        // }

        $blogPost->delete();

        return response()->json(null, 204);
    }

    protected function canAccessPost($user, $blogPost)
    {
        return $user->hasRole('admin') || $blogPost->user_id === $user->id;

        // Bloggers need the specific permission and ownership
        return $user->hasPermission($permission) && $blogPost->user_id === $user->id;
    }
}
