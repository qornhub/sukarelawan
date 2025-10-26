<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Models from your app
use App\Models\BlogPost;
use App\Models\User;
use App\Models\BlogCategory;
use App\Models\BlogComment;

class AdminDashboardBlogsController extends Controller
{
    /**
     * 1) Posts trend (daily|weekly|monthly)
     */
    public function postsTrend(Request $request)
    {
        $period = $request->get('period', 'monthly');

        $labels = [];
        $counts = [];

        try {
            if ($period === 'daily') {
                $days = 30;
                $now = Carbon::now()->startOfDay();
                for ($i = $days - 1; $i >= 0; $i--) {
                    $d = (clone $now)->subDays($i);
                    $labels[] = $d->format('d M');
                    $counts[] = BlogPost::whereDate('created_at', $d)->count();
                }
            } elseif ($period === 'weekly') {
                $weeks = 12;
                $startOfThisWeek = Carbon::now()->startOfWeek();
                for ($i = $weeks - 1; $i >= 0; $i--) {
                    $start = (clone $startOfThisWeek)->subWeeks($i)->startOfWeek();
                    $end = (clone $start)->endOfWeek();
                    $labels[] = $start->format('d M');
                    $counts[] = BlogPost::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->count();
                }
            } else {
                $months = 12;
                $startMonth = Carbon::now()->startOfMonth();
                for ($i = $months - 1; $i >= 0; $i--) {
                    $start = (clone $startMonth)->subMonths($i)->startOfMonth();
                    $end = (clone $start)->endOfMonth();
                    $labels[] = $start->format('M Y');
                    $counts[] = BlogPost::whereBetween('created_at', [$start->toDateTimeString(), $end->toDateTimeString()])->count();
                }
            }
        } catch (\Throwable $ex) {
            Log::warning('postsTrend (Eloquent) failed: '.$ex->getMessage());
            // Fallback: aggregated DB query on blog_posts
            if ($period === 'daily') {
                $rows = DB::table('blog_posts')
                    ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as total'))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get();
                $labels = $rows->pluck('date')->toArray();
                $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();
            } else {
                $rows = DB::table('blog_posts')
                    ->select(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"), DB::raw('COUNT(*) as total'))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->get();
                $labels = $rows->pluck('month')->toArray();
                $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();
            }
        }

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * 2) Top authors (by blog_posts count)
     */
   public function topAuthors(Request $request)
{
    $limit = intval($request->get('limit', 10));

    try {
        // Eloquent approach: eager-load role and count blogPosts
        $users = User::with('role')
            ->withCount('blogPosts')
            ->orderByDesc('blog_posts_count')
            ->limit($limit)
            ->get();

        $labels = $users->map(function ($u) {
            $roleName = optional($u->role)->roleName ?? 'Unknown';
            return "{$u->name} ({$roleName})";
        })->toArray();

        $counts = $users->pluck('blog_posts_count')->map(fn($v) => (int)$v)->toArray();
        $userIds = $users->pluck('id')->map(fn($v) => $v)->toArray();
        $roles = $users->map(fn($u) => optional($u->role)->roleName)->toArray();

        // If we have results, return them (front-end still uses labels & counts)
        if (!empty($labels)) {
            return response()->json([
                'success' => true,
                'labels'  => $labels,
                'counts'  => $counts,
                'user_ids'=> $userIds,
                'roles'   => $roles,
            ]);
        }
    } catch (\Throwable $ex) {
        Log::warning('topAuthors (Eloquent) failed: '.$ex->getMessage());
    }

    // Fallback: DB join (works even if model relationships differ)
    $rows = DB::table('blog_posts as b')
        ->join('users as u', 'b.user_id', '=', 'u.id')
        ->leftJoin('roles as r', 'u.role_id', '=', 'r.role_id')
        ->select('u.id as user_id', 'u.name as author', 'r.roleName as role', DB::raw('COUNT(b.blogPost_id) as total'))
        ->groupBy('u.id', 'u.name', 'r.roleName')
        ->orderByDesc('total')
        ->limit($limit)
        ->get();

    $labels = $rows->map(function ($r) { return "{$r->author} (" . ($r->role ?? 'Unknown') . ")"; })->toArray();
    $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();
    $userIds = $rows->pluck('user_id')->toArray();
    $roles = $rows->pluck('role')->map(fn($v) => $v ?? 'Unknown')->toArray();

    return response()->json([
        'success' => true,
        'labels'  => $labels,
        'counts'  => $counts,
        'user_ids'=> $userIds,
        'roles'   => $roles,
    ]);
}


    /**
     * 3) Category distribution (uses BlogCategory withCount('blogPosts') or fallback)
     */
    public function categoryDistribution()
    {
        try {
            // Prefer Eloquent withCount on BlogCategory
            if (class_exists(BlogCategory::class)) {
                $cats = BlogCategory::withCount('blogPosts')
                    ->orderByDesc('blog_posts_count')
                    ->get();
                if ($cats->isNotEmpty()) {
                    $labels = $cats->pluck('categoryName')->map(fn($v) => $v ?? 'Uncategorized')->toArray();
                    $counts = $cats->pluck('blog_posts_count')->map(fn($v) => (int)$v)->toArray();

                    if (!empty($labels)) {
                        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
                    }
                }
            }
        } catch (\Throwable $ex) {
            Log::warning('categoryDistribution (Eloquent) failed: '.$ex->getMessage());
        }

        // Fallback: aggregated DB query on blog_posts + blog_categories
        $rows = DB::table('blog_posts as b')
            ->leftJoin('blog_categories as c', 'b.category_id', '=', 'c.blogCategory_id')
            ->select(DB::raw("COALESCE(c.categoryName, 'Uncategorized') as category"), DB::raw('COUNT(b.blogPost_id) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->get();

        $labels = $rows->pluck('category')->toArray();
        $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * 4) Comments per blog (top N)
     */
    public function commentsPerBlog(Request $request)
    {
        $limit = intval($request->get('limit', 10));

        try {
            // Eloquent: BlogPost::withCount('comments')
            $posts = BlogPost::withCount('comments')
                ->orderByDesc('comments_count')
                ->limit($limit)
                ->get();

            if ($posts->isNotEmpty()) {
                $labels = $posts->pluck('title')->map(fn($t) => mb_strimwidth($t, 0, 60, '...'))->toArray();
                $counts = $posts->pluck('comments_count')->map(fn($v) => (int)$v)->toArray();
                return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
            }
        } catch (\Throwable $ex) {
            Log::warning('commentsPerBlog (Eloquent) failed: '.$ex->getMessage());
        }

        // Fallback DB: join blog_comments -> blog_posts
        $rows = DB::table('blog_comments as c')
            ->join('blog_posts as b', 'c.blogPost_id', '=', 'b.blogPost_id')
            ->select('b.title', DB::raw('COUNT(c.blogComment_id) as total'))
            ->groupBy('b.blogPost_id', 'b.title')
            ->orderByDesc('total')
            ->limit($limit)
            ->get();

        $labels = $rows->pluck('title')->map(fn($t) => mb_strimwidth($t, 0, 60, '...'))->toArray();
        $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * 5) Post status summary
     */
    public function statusSummary()
    {
        try {
            $rows = DB::table('blog_posts')
                ->select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->get();

            $labels = $rows->pluck('status')->toArray();
            $counts = $rows->pluck('total')->map(fn($v) => (int)$v)->toArray();
        } catch (\Throwable $ex) {
            Log::error('statusSummary error: '.$ex->getMessage());
            $labels = [];
            $counts = [];
        }

        return response()->json(['success' => true, 'labels' => $labels, 'counts' => $counts]);
    }

    /**
     * 6) Average comments per post
     */
    public function avgCommentsPerPost()
    {
        try {
            $totalComments = DB::table('blog_comments')->count();
            $totalPosts = DB::table('blog_posts')->count();
            $avg = $totalPosts > 0 ? round($totalComments / $totalPosts, 2) : 0.0;
        } catch (\Throwable $ex) {
            Log::error('avgCommentsPerPost error: '.$ex->getMessage());
            $totalComments = 0;
            $totalPosts = 0;
            $avg = 0.0;
        }

        return response()->json([
            'success' => true,
            'average_comments_per_post' => $avg,
            'total_comments' => (int)$totalComments,
            'total_posts' => (int)$totalPosts,
        ]);
    }
}
