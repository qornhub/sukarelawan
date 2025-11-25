<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
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

    /** @var \App\Models\User $admin */
    $admin = Auth::user();
    $admin->update([
        'last_login_at' => now(),
    ]);


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
/** @var \App\Models\User $user */
$user = Auth::user();

$user->update([
    'last_login_at' => now(),
]);
        // Try to get a role name in several safe ways
        $roleName = null;

        if (isset($user->role) && is_object($user->role)) {
            $roleName = $user->role->roleName ?? null;
        }

        if (!$roleName && isset($user->role) && is_string($user->role)) {
            $roleName = $user->role;
        }

        if (!$roleName && isset($user->role_name)) {
            $roleName = $user->role_name;
        }

        $roleName = $roleName ? strtolower($roleName) : null;

        if ($roleName === 'volunteer') {
            return redirect()->route('volunteer.index.public');
        }

        if ($roleName === 'ngo') {
            return redirect()->route('ngo.dashboard');
        }

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
