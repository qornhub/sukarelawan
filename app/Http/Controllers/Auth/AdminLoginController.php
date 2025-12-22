<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.admin_login');
    }

    public function login(Request $request)
    {
        // Validate login input
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // Check if user role = admin
            if ($user->role && $user->role->roleName === 'admin') {

                // ⭐ UPDATE LAST LOGIN TIME (required)
                $user->update([
                    'last_login_at' => now(),
                ]);

                // Redirect admin to dashboard
                return redirect()->route('admin.dashboard.index');
            }

            // If not admin → logout and show error
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Not an admin.']);
        }

        // Invalid credentials
        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session (security best practice)
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
