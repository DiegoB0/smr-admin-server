<?php

// phpcs:ignoreFile

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Register a new user and return a JWT token.
     */
    public function register(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate a JWT token for the user
        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token]);
    }

    /**
     * Log the user in and return a JWT token.
     */
    public function login(Request $request)
    {
        // Validate the incoming request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Log email for debugging purposes
        Log::info("Login attempt for email: " . $request->email);


        // Attempt to generate a token for the user
        try {
            // Check if user exists in the database
            $user = User::where('email', $request->email)->first();


            if (!$user) {
                Log::error("User not found for email: " . $request->email);
                return response()->json(['error' => 'Invalid credentials'], 401);
            }


            $directPermissions = $user->permissions()->pluck('permissions.slug')->toArray();

            $rolePermissions = $user->roles()
                ->with('permissions')
                ->get()
                ->pluck('permissions')
                ->flatten()
                ->pluck('slug')
                ->toArray();

            $allPermissions = array_unique(array_merge($directPermissions, $rolePermissions));

            // Now attempt to generate token
            if (!$token = JWTAuth::attempt($request->only('email', 'password'))) {
                Log::error("JWT attempt failed for email: " . $request->email);
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return response()->json(
                [
                    'token' => $token,
                    'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'roles' => $user->roles()->pluck('roles.slug')->toArray(),
                    'permissions' => $allPermissions,
                ],
                ]
            );

        } catch (JWTException $e) {
            Log::error("JWT Exception: " . $e->getMessage());
            return response()->json(['error' => 'Could not create token'], 500);
        }

    }

    /**
     * Log the user out and invalidate the token.
     */
    public function logout(Request $request)
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $e) {
            return response()->json(['error' => 'Failed to logout, please try again later.'], 500);
        }

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Get the currently authenticated user.
     */
    public function me()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        return response()->json(compact('user'));
    }
}
