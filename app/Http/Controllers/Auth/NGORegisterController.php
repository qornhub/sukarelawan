<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\NGOProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;


class NGORegisterController extends Controller
{
    
public function showRegisterForm()
    {
        return view('auth.ngo-register'); // create this Blade view
    }
public function register(Request $request)
{
     
    $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'organizationName' => 'required',
        'registrationNumber' => 'required',
        'contactNumber' => 'required',
        'country' => 'required',
    ]);

    $ngoRole = Role::where('roleName', 'ngo')->first();

    if (!$ngoRole) {
    return back()->withErrors(['role' => 'Volunteer role not found. Please check roles table.']);

    }
    $user = User::create([
        'name' => $request->organizationName,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $ngoRole->role_id,
    ]);

    NGOProfile::create([
        'ngo_id' => Str::uuid(),
        'user_id' => $user->id,
        'organizationName' => $request->organizationName,
        'registrationNumber' => $request->registrationNumber,
        'contactNumber' => $request->contactNumber,
        'country' => $request->country,
    ]);

    return redirect('/login/ngo')->with('success', 'Registration successful. Please log in.');
}

}
