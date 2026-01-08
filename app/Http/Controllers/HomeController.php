<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\BlogPost;

class HomeController extends Controller
{
    public function index()
    {
        $badges = Badge::orderBy('created_at', 'asc')
            ->take(6) // limit for landing page
            ->get();

            
    $blogs = BlogPost::with('user')
        ->where('status', 'published')
        ->orderBy('published_at', 'desc')
        ->take(3)
        ->get();
        return view('landing.home', compact('badges', 'blogs'));
    }
}
