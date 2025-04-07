<?php

// phpcs:ignoreFile

namespace App\Http\Controllers;

use App\Models\User;
// use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // GET all users
    public function index()
    {
        return User::with('roles')->get();
    }

    // POST create user
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string',
            'email'    => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role_ids' => 'array', // e.g. [1, 2]
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }

        return response()->json($user->load('roles'), 201);
    }

    // GET single user
    public function show($id)
    {
        return User::with('roles')->findOrFail($id);
    }

    // PUT update user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $user->update($request->only(['name', 'email']));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        if ($request->has('role_ids')) {
            $user->roles()->sync($request->role_ids);
        }

        return response()->json($user->load('roles'));
    }

    // DELETE user
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}
