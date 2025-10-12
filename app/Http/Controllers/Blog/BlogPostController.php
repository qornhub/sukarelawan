<?php

namespace App\Http\Controllers\Blog;

use App\Models\Role;
use App\Models\BlogPost;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class BlogPostController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogPost::where('status', 'published');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('blogSummary', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(10);
        $posts->appends($request->only('q'));

        return view($this->viewForRole('blogs.index'), compact('posts'));
    }

    public function show($id)
    {
        $post = BlogPost::where('blogPost_id', $id)->firstOrFail();

        if ($post->status !== 'published') {
            $this->ensureOwnerOnly($post);
        }
        $comments = BlogComment::where('blogPost_id', $post->blogPost_id)
    ->orderBy('created_at', 'asc')
    ->paginate(3, ['*'], 'comments_page')
    ->withQueryString();

        return view($this->viewForRole('blogs.show'), compact('post','comments'));
    }

    protected function ensureOwnerOnly(BlogPost $post)
    {
        if (! Auth::check()) {
            abort(403);
        }

        $user = Auth::user();

        if ($user->id === $post->user_id) {
            return true;
        }

        abort(403);
    }

    /**
     * Determine view prefix based on the current user's role.
     *
     * Uses these fallbacks:
     *  1) $user->role->roleName
     *  2) $user->role_id -> lookup in roles table
     *  3) $user->roleName (string column on users)
     *  4) fallback 'volunteer'
     *
     * Returned view is like 'admin.blogs.index' or 'ngo.blogs.show'
     */
    protected function viewForRole(string $viewPath): string
    {
        $prefix = 'volunteer'; // default fallback

        if (! Auth::check()) {
            return "{$prefix}.{$viewPath}";
        }

        $user = Auth::user();
        $roleName = null;

        // 1) relation: $user->role->roleName
        if (isset($user->role) && is_object($user->role) && isset($user->role->roleName)) {
            $roleName = $user->role->roleName;
        }

        // 2) fallback: users.role_id -> lookup Role model
        if (! $roleName && ! empty($user->role_id)) {
            // Role::find() expects PK, adjust if your Role primary key is 'role_id'
            $role = Role::where('role_id', $user->role_id)->first();
            if ($role && isset($role->roleName)) {
                $roleName = $role->roleName;
            }
        }

        // 3) fallback: direct attribute on users (if you happened to store roleName on user)
        if (! $roleName && ! empty($user->roleName)) {
            $roleName = $user->roleName;
        }

        if ($roleName) {
            $roleName = strtolower(trim($roleName));
            if (in_array($roleName, ['admin', 'administrator'])) {
                $prefix = 'admin';
            } elseif (in_array($roleName, ['ngo', 'organization', 'org'])) {
                $prefix = 'ngo';
            } elseif (in_array($roleName, ['volunteer', 'user'])) {
                $prefix = 'volunteer';
            } else {
                
                $prefix = 'volunteer';
            }
        }

        return "{$prefix}.{$viewPath}";
    }
}
