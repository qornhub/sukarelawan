<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    public function show()
    {
        $profile = Auth::user()->AdminProfile;
        return view('admin.profile.show', compact('profile'));
    }

    public function edit()
    {
        $profile = Auth::user()->AdminProfile;
        return view('admin.profile.edit', compact('profile'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'profilePhoto' => 'nullable|image|mimes:jpg,jpeg,png',
        ]);

        $profile = Auth::user()->adminProfile;

        if ($request->hasFile('profilePhoto')) {
            $profilePath = $request->file('profilePhoto')->store('profiles', 'public');
            $profile->profilePhoto = $profilePath;
        }

        $profile->update($request->only([
            'name'
            
        ]));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}
