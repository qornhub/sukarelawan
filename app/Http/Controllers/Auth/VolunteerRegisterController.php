<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\VolunteerProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class VolunteerRegisterController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.volunteer_register'); // create this Blade view
    }

    public function register(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'name' => 'required',
            'contactNumber' => 'required',
            'country' => 'required',
            'dateOfBirth' => 'required'
        ]);

        $volunteerRole = Role::where('roleName', 'volunteer')->first();
        

        if (!$volunteerRole) {
    return back()->withErrors(['role' => 'Volunteer role not found. Please check roles table.']);
}
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $volunteerRole->role_id,
        ]);

        VolunteerProfile::create([
            'volunteer_id' => Str::uuid(),
            'user_id' => $user->id,
            'name' => $request->name,
            'contactNumber' => $request->contactNumber,
            'country' => $request->country,
            'dateOfBirth' => $request->dateOfBirth
        ]);

        // ✅ Redirect to login instead of logging in immediately
       return redirect()->route('login.volunteer')->with('success', 'Registration successful. Please log in.');

    }
}
