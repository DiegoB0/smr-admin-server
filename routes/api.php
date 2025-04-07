<?php

// phpcs:ignoreFile

use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BlogPostController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ProjectController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware(['auth:api'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::middleware(['auth:api', 'checkRole:admin'])->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('/send-notifications', [ContactController::class, 'sendNotifications']);
    Route::get('/messages', [MessageController::class, 'index']);
    Route::patch('/messages/{id}/toggle-read', [MessageController::class, 'toggleReadStatus']);
});

Route::middleware(['auth:api', 'checkRole:admin,admin-web'])->group(function () {
    Route::apiResource('projects', ProjectController::class);
    Route::apiResource('services', ServiceController::class);
    Route::apiResource('testimonials', TestimonialController::class);
});

Route::middleware(['auth:api', 'checkRole:admin,blogger'])->group(function () {
    Route::apiResource('blog-posts', BlogPostController::class);
});

// For the webpage
Route::post('/send-email', [ContactController::class, 'sendEmail']);
