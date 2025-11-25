<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
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
        $qRaw          = trim((string)$request->query('q', ''));
        $roleParamRaw  = trim((string)$request->query('role', ''));
        $dateFrom      = trim((string)$request->query('date_from', ''));
        $dateTo        = trim((string)$request->query('date_to', ''));
        $allowedSorts  = ['name', 'created_at', 'role'];
        $sortBy        = in_array($request->query('sort_by', 'created_at'), $allowedSorts) ? $request->query('sort_by', 'created_at') : 'created_at';
        $sortDir       = strtolower($request->query('sort_dir', 'desc')) === 'asc' ? 'asc' : 'desc';
        $perPage       = intval($request->query('per_page', 15));
        if ($perPage <= 0) $perPage = 15;

        $q = $qRaw !== '' ? $qRaw : null;

        // --- Base query: eager load related profiles & role ---
        $query = User::with(['role', 'volunteerProfile', 'ngoProfile', 'adminProfile']);

        // --- Role filter ---
        $appliedRole = null;
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
                    $appliedRole = $role;
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
                    $appliedRole = $role;
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

        // --- Date filters ---
        if ($dateFrom) {
            try {
                $from = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
                $query->where('users.created_at', '>=', $from->toDateTimeString());
            } catch (\Exception $ex) {}
        }
        if ($dateTo) {
            try {
                $to = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();
                $query->where('users.created_at', '<=', $to->toDateTimeString());
            } catch (\Exception $ex) {}
        }

        // --- Search ---
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

        // --- Sorting ---
        if ($sortBy === 'role') {
            $query->leftJoin('roles', 'users.role_id', '=', 'roles.role_id')
                  ->orderBy('roles.roleName', $sortDir)
                  ->select('users.*');
        } elseif ($sortBy === 'name') {
            $query->orderBy('users.name', $sortDir);
        } else {
            $query->orderBy('users.created_at', $sortDir);
        }

        // --- Pagination ---
        $users = $query->paginate($perPage)->withQueryString();

        // ⭐⭐⭐ NEW BLOCK START — Add idle_days for each user
        foreach ($users as $u) {
            $u->idle_days = $u->last_login_at
                ? Carbon::parse($u->last_login_at)->diffInDays(now())
                : Carbon::parse($u->created_at)->diffInDays(now());
        }
        // ⭐⭐⭐ NEW BLOCK END

        // --- Roles dropdown ---
        $roles = Role::orderBy('roleName')->get()
            ->unique(fn($r) => strtolower(trim($r->roleName)))
            ->values();

        // --- Applied filters ---
        $appliedFilters = [
            'q' => $qRaw ?: null,
            'role' => $selectedRoleValue,
            'role_name' => $appliedRole ? $appliedRole->roleName : null,
            'date_from' => $dateFrom ?: null,
            'date_to' => $dateTo ?: null,
            'sort_by' => $sortBy,
            'sort_dir' => $sortDir,
            'per_page' => $perPage,
        ];

        return view('admin.users.index', compact('users', 'roles', 'appliedFilters'));
    }

    /**
     * Show user details.
     */
    public function show($id)
    {
        $user = User::with([
            'role',
            'volunteerProfile',
            'ngoProfile',
            'adminProfile',
            'events',
            'eventRegistrations',
            'blogPosts',
        ])->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Delete user.
     */
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
