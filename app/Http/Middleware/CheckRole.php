<?php

// phpcs:ignoreFile

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            Log::error('User is not authenticated.');
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if (!$user instanceof User) {
            Log::error('User is not an instance of App\Models\User.');
            return response()->json(['message' => 'Internal Server Error'], 500);
        }

        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request); // User has at least one required role
            }
        }

        return response()->json(['message' => 'Forbidden'], 403);

        return $next($request);
    }
}
