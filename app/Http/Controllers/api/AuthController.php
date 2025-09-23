<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        
        $user = User::with('role')->find(Auth::id());

        // determine roleName safely
        $roleName = null;
        if (isset($user->role) && is_object($user->role)) {
            $roleName = $user->role->roleName ?? null;
        } elseif (isset($user->role_name)) {
            $roleName = $user->role_name;
        } elseif (isset($user->role) && is_string($user->role)) {
            $roleName = $user->role;
        }
        $roleName = $roleName ? strtolower($roleName) : null;

        if ($roleName !== 'volunteer') {
            // Logout the session and deny access
            Auth::logout();
            return response()->json(['message' => 'Access denied. Only volunteers allowed.'], 403);
        }

        // Create Sanctum token (make sure Laravel Sanctum is configured)
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $roleName
            ]
        ]);
    }

    // optional: logout by revoking current token
    public function logout(Request $request)
    {
        // revoke the token used for the current request
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }
}
