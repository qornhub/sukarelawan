<?php

namespace App\Http\Controllers\Admin;

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use App\Models\Event;
use App\Models\Attendance;
use App\Models\BlogPost;
use App\Models\UserBadge;
// use App\Models\EventRegistration; // only if you actually use it
// use App\Models\UserPoint;         // optional
use App\Models\NGOProfile;              // 👈 ADD THIS
use Illuminate\Support\Facades\Schema; 

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class AdminUsersController extends Controller
{
    /**
     * Display a paginated listing of users with advanced filters.
     */
    public function index(Request $request)
    {
        // --- Inputs / safe defaults ---
        $qRaw         = trim((string)$request->query('q', ''));
        $roleParamRaw = trim((string)$request->query('role', ''));
        $dateFrom     = trim((string)$request->query('date_from', ''));
        $dateTo       = trim((string)$request->query('date_to', ''));
        
        // NEW: activity filter param: 'active' | 'inactive' | null
        $activityParam = trim((string)$request->query('activity', ''));
        $activity      = in_array($activityParam, ['active', 'inactive']) ? $activityParam : null;

        // sort_by now also allows "activity"
        $allowedSorts  = ['name', 'created_at', 'role', 'activity'];
        $sortByParam   = $request->query('sort_by', 'created_at');
        $sortBy        = in_array($sortByParam, $allowedSorts) ? $sortByParam : 'created_at';

        // sort_dir stays normal asc / desc
        $sortDirParam  = strtolower($request->query('sort_dir', 'desc'));
        $sortDir       = $sortDirParam === 'asc' ? 'asc' : 'desc';

        $perPage       = intval($request->query('per_page', 15));
        if ($perPage <= 0) $perPage = 15;

        $q = $qRaw !== '' ? $qRaw : null;

        // --- Base query: eager load related profiles & role ---
        $query = User::with(['role', 'volunteerProfile', 'ngoProfile', 'adminProfile']);

        // --- Role filter ---
        $appliedRole       = null;
        $selectedRoleValue = $roleParamRaw;

        if ($roleParamRaw !== '') {
            if (strpos($roleParamRaw, '|') !== false) {
                [$idPart, $namePart] = array_map('trim', explode('|', $roleParamRaw, 2));
                $role = null;
                if ($idPart !== '') {
                    $role = Role::where('role_id', $idPart)->first();
                }
                if (!$role && $namePart !== '') {
                    $role = Role::whereRaw('LOWER(roleName) = ?', [strtolower($namePart)])->first();
                }

                if ($role) {
                    $appliedRole       = $role;
                    $query->where('users.role_id', $role->role_id);
                    $selectedRoleValue = "{$role->role_id}|{$role->roleName}";
                } else {
                    $selectedRoleValue = $roleParamRaw;
                }
            } else {
                $role = Role::where('role_id', $roleParamRaw)
                            ->orWhereRaw('LOWER(roleName) = ?', [strtolower($roleParamRaw)])
                            ->first();
                if ($role) {
                    $appliedRole       = $role;
                    $query->where('users.role_id', $role->role_id);
                    $selectedRoleValue = "{$role->role_id}|{$role->roleName}";
                } else {
                    $query->whereHas('role', function ($qRole) use ($roleParamRaw) {
                        $qRole->whereRaw('LOWER(roleName) LIKE ?', ['%' . strtolower($roleParamRaw) . '%']);
                    });
                    $selectedRoleValue = $roleParamRaw;
                }
            }
        }

        // --- Date filters (user created_at) ---
        if ($dateFrom) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
                $query->where('users.created_at', '>=', $from->toDateTimeString());
            } catch (\Exception $ex) {
                // ignore invalid date
            }
        }
        if ($dateTo) {
            try {
                $to = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();
                $query->where('users.created_at', '<=', $to->toDateTimeString());
            } catch (\Exception $ex) {
                // ignore invalid date
            }
        }

        // --- Search: name, email, roleName, and profile names/org ---
        if ($q) {
            $query->where(function ($sub) use ($q) {
                $sub->where('users.name', 'LIKE', "%{$q}%")
                    ->orWhere('users.email', 'LIKE', "%{$q}%")
                    ->orWhereHas('role', function ($qr) use ($q) {
                        $qr->where('roleName', 'LIKE', "%{$q}%");
                    })
                    ->orWhereHas('volunteerProfile', function ($qp) use ($q) {
                        $qp->where('name', 'LIKE', "%{$q}%");
                    })
                    ->orWhereHas('ngoProfile', function ($qp) use ($q) {
                        $qp->where('organizationName', 'LIKE', "%{$q}%");
                    })
                    ->orWhereHas('adminProfile', function ($qp) use ($q) {
                        $qp->where('name', 'LIKE', "%{$q}%");
                    });
            });
        }

        // --- Sorting / Activity filter ---
        if ($sortBy === 'role') {

            $query->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')
                  ->orderBy('roles.roleName', $sortDir)
                  ->select('users.*', 'users.last_login_at');

        } elseif ($sortBy === 'name') {

            $query->orderBy('users.name', $sortDir);

        } elseif ($sortBy === 'activity') {

            $daysIdleExpr = "DATEDIFF(NOW(), COALESCE(last_login_at, created_at))";

            if ($activity === 'active') {
                // Active = idle < 20 days
                $query->whereRaw("$daysIdleExpr < 20");
            } elseif ($activity === 'inactive') {
                // Inactive = idle >= 20 days
                $query->whereRaw("$daysIdleExpr >= 20");
            }

            // Sorting inside that group: by idle days
            if ($sortDir === 'asc') {
                $query->orderByRaw("$daysIdleExpr ASC");
            } else {
                $query->orderByRaw("$daysIdleExpr DESC");
            }

        } else {
            // Default sorting by created_at
            $query->orderBy('users.created_at', $sortDir);
        }

        // --- Pagination (with query string preserved) ---
        $users = $query->paginate($perPage)->withQueryString();

        // --- Compute idle_days for UI (Active/Inactive text) ---
        foreach ($users as $u) {
            $baseDate = $u->last_login_at ?: $u->created_at;

            if ($baseDate) {
                $u->idle_days = Carbon::parse($baseDate)
                    ->startOfDay()
                    ->diffInDays(now()->startOfDay());
            } else {
                $u->idle_days = 0;
            }
        }

        // --- Roles for dropdown ---
        $roles = Role::orderBy('roleName')->get()
            ->unique(function ($r) {
                return strtolower(trim($r->roleName));
            })->values();

        // --- Applied filters for UI ---
        $appliedFilters = [
            'q'         => $qRaw ?: null,
            'role'      => $selectedRoleValue,
            'role_name' => $appliedRole ? $appliedRole->roleName : null,
            'date_from' => $dateFrom ?: null,
            'date_to'   => $dateTo ?: null,
            'sort_by'   => $sortBy,
            'sort_dir'  => $sortDir,
            'activity'  => $activity, // ⭐ new
            'per_page'  => $perPage,
        ];

        return view('admin.users.index', compact('users', 'roles', 'appliedFilters'));
    }

        /**
     * Show a single user's details (profile + relations).
     */
    public function show($id)
    {
        // Load user + role + profiles
        $user = User::with(['role', 'volunteerProfile', 'ngoProfile', 'adminProfile'])
            ->findOrFail($id);

        $roleName = strtolower(optional($user->role)->roleName ?? '');

        /**
         * 1) VOLUNTEER → use admin volunteer profile view
         */
        if ($roleName === 'volunteer') {

            $profile = $user->volunteerProfile;

            if (! $profile) {
                abort(404, 'Volunteer profile not found for this user.');
            }

            $today = now()->toDateString();

            // Upcoming events (same logic as VolunteerProfileController)
            $upcomingEvents = Event::whereHas('registrations', function ($q) use ($user) {
                    $q->where('user_id', $user->id)
                      ->where('status', 'approved');
                })
                ->whereDate('eventEnd', '>=', $today)
                ->whereDoesntHave('attendances', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderByDesc('eventStart')
                ->paginate(3, ['*'], 'upcoming_page');

            // Past events
            $pastEvents = Event::whereHas('attendances', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->with(['attendances' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }])
                ->orderByDesc('eventStart')
                ->paginate(3, ['*'], 'past_page');

            // Total points
            $totalPoints = Attendance::where('user_id', $user->id)->sum('pointEarned');

            // Earned badges
            $userBadges = UserBadge::with('badge')
                ->where('user_id', $user->id)
                ->orderByDesc('created_at')
                ->paginate(5, ['*'], 'earned_page');

            // Admin is NOT treated as owner, so drafts stay hidden
            $isOwner = false;

            $blogQuery = BlogPost::where('user_id', $user->id)
                ->with(['category', 'user'])
                ->orderByDesc('created_at')
                ->where('status', 'published'); // admin sees only published

            $blogPosts = $blogQuery->paginate(3, ['*'], 'blog_page');

            return view('admin.users.volunteer_profile', compact(
                'user',
                'profile',
                'upcomingEvents',
                'pastEvents',
                'blogPosts',
                'totalPoints',
                'userBadges',
                'isOwner'
            ));
        }

        /**
         * 2) NGO → new admin NGO profile view
         */
        if ($roleName === 'ngo') {

            $profile = $user->ngoProfile;

            if (! $profile) {
                abort(404, 'NGO profile not found for this user.');
            }

            // ---------- Event ownership detection (similar to NGOProfileController) ----------
            $eventsTable      = 'events';
            $ngoReferenceCols = ['ngo_profile_id', 'ngo_id', 'organization_id'];
            $userReferenceCols = ['user_id', 'created_by', 'creator_id', 'owner_id'];

            $ownerColumn = null;
            $ownerValue  = null;

            // Prefer NGO columns
            foreach ($ngoReferenceCols as $col) {
                if (Schema::hasColumn($eventsTable, $col)) {
                    $ownerColumn = $col;
                    $ownerValue  = $profile->ngo_id ?? $profile->id ?? $profile->user_id ?? null;
                    break;
                }
            }

            // Fallback to user-based columns
            if (! $ownerColumn) {
                foreach ($userReferenceCols as $col) {
                    if (Schema::hasColumn($eventsTable, $col)) {
                        $ownerColumn = $col;
                        $ownerValue  = $profile->user_id ?? $profile->id ?? null;
                        break;
                    }
                }
            }

            // Safe defaults if nothing can be linked
            if (! $ownerColumn || is_null($ownerValue)) {
                $ongoingEvents = collect();
                $pastEvents    = collect();
                $blogPosts     = collect();
                $totalEvents   = 0;

                return view('admin.users.ngo_profile', compact(
                    'user',
                    'profile',
                    'ongoingEvents',
                    'pastEvents',
                    'blogPosts',
                    'totalEvents'
                ));
            }

            $baseQuery = Event::where($ownerColumn, $ownerValue);
            $now = now();

            // Ongoing events (future / not finished)
            $ongoingQuery = (clone $baseQuery)
                ->where(function ($q) use ($now) {
                    $q->where(function ($q2) use ($now) {
                        $q2->whereNotNull('eventEnd')->where('eventEnd', '>=', $now);
                    })
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereNull('eventEnd')->where('eventStart', '>=', $now);
                    });
                })
                ->orderBy('eventStart', 'asc');

            $ongoingEvents = $ongoingQuery
                ->paginate(3, ['*'], 'ongoing_page')
                ->withQueryString();

            // Past events
            $pastQuery = (clone $baseQuery)
                ->where(function ($q) use ($now) {
                    $q->where(function ($q2) use ($now) {
                        $q2->whereNotNull('eventEnd')->where('eventEnd', '<', $now);
                    })
                    ->orWhere(function ($q2) use ($now) {
                        $q2->whereNull('eventEnd')->where('eventStart', '<', $now);
                    });
                })
                ->orderBy('eventEnd', 'desc');

            $pastEvents = $pastQuery
                ->paginate(3, ['*'], 'past_page')
                ->withQueryString();

            $totalEvents = (clone $baseQuery)->count();

            // Blog posts – admin view sees only published
            $blogQuery = null;
            if (Schema::hasColumn('blog_posts', 'user_id')) {
                $blogQuery = BlogPost::where('user_id', $profile->user_id);
            } elseif (Schema::hasColumn('blog_posts', 'ngo_profile_id')) {
                $blogQuery = BlogPost::where('ngo_profile_id', $profile->ngo_id ?? $profile->id);
            }

            if (! $blogQuery) {
                $blogPosts = collect();
            } else {
                $blogQuery = $blogQuery
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->orderBy('created_at', 'desc');

                $blogPosts = $blogQuery
                    ->paginate(3, ['*'], 'blog_page')
                    ->withQueryString();
            }

            return view('admin.users.ngo_profile', compact(
                'user',
                'profile',
                'ongoingEvents',
                'pastEvents',
                'blogPosts',
                'totalEvents'
            ));
        }

        /**
         * 3) Other roles → fallback view
         */
        return view('admin.users.show', compact('user'));
    }



    public function destroy($id)
    {
        $user = User::findOrFail($id);

        try {
            $user->delete();
            return redirect()->back()->with('success', 'User deleted.');
        } catch (\Throwable $ex) {
            Log::error('AdminUsersController@destroy error: ' . $ex->getMessage());
            return redirect()->back()->with('error', 'Unable to delete user.');
        }
    }
}
