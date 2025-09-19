<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminProfile;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
class AdminRegisterController extends Controller
{
   
public function register(Request $request)
{
    $request->validate([
        'email' => 'required|email|unique:users',
        'password' => 'required|min:6|confirmed',
        'name' => 'required'
    ]);

    $adminRole = Role::where('roleName', 'admin')->first();

    $user = User::create([
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'role_id' => $adminRole->role_id,
    ]);

    AdminProfile::create([
        'admin_id' => Str::uuid(),
        'user_id' => $user->id,
        'name' => $request->name
    ]);

    Auth::login($user);

    return redirect('/admin/dashboard');
}

}
