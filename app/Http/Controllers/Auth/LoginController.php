<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login'); // make sure you create this Blade view
    }

    public function showAdminLoginForm()
{
    return view('auth.admin_login');
}

public function loginAdmin(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
        if (Auth::user()->role->roleName === 'admin') {
            return redirect('/admin/dashboard');
        } else {
            Auth::logout();
            return back()->withErrors(['email' => 'Access denied. Not an admin.']);
        }
    }

    return back()->withErrors(['email' => 'Invalid credentials']);
}

      public function login(Request $request)
    {
        // validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials, $request->filled('remember'))) {
            return back()->withErrors(['email' => 'Invalid email or password'])->withInput();
        }

        // Authentication successful
        $user = Auth::user();

        // Try to get a role name in several safe ways (your app may store role differently)
        $roleName = null;

        // 1) If user->role is an object/relationship: ->roleName
        if (isset($user->role) && is_object($user->role)) {
            $roleName = $user->role->roleName ?? null;
        }

        // 2) If user->role is a string (some apps store role directly on users table)
        if (!$roleName && isset($user->role) && is_string($user->role)) {
            $roleName = $user->role;
        }

        // 3) Another common column name fallback
        if (!$roleName && isset($user->role_name)) {
            $roleName = $user->role_name;
        }

        // Normalize to lowercase for matching
        $roleName = $roleName ? strtolower($roleName) : null;

        // Redirect based on role
        if ($roleName === 'volunteer') {
            // route to your public volunteer index
            return redirect()->route('volunteer.index.public');
        }

        if ($roleName === 'ngo') {
            return redirect()->route('ngo.dashboard');
        }

        

        // If role unknown, logout and show error
        Auth::logout();
        return back()->withErrors(['email' => 'Access denied. Your account role is not recognized.']);
    }

public function logoutNgo(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.ngo');
}

public function logoutVolunteer(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login.volunteer');
}




}
