<?php

namespace App\Http\Controllers;

use App\Models\Badge;

class HomeController extends Controller
{
    public function index()
    {
        $badges = Badge::orderBy('created_at', 'asc')
            ->take(6) // limit for landing page
            ->get();

       return view('landing.home', compact('badges'));
    }
}
