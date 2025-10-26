<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>Admin — User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/adminDashboard/users.css') }}">
</head>

<body class="admin-users-container">
    @include('layouts.admin_nav')
    <!-- WRAPPER: push everything right by 40px so admin_nav can expand -->
    <div style="margin-left: 40px; margin-right:20px;">
        <div class="admin-users-wrapper ">
            <!-- Page Header -->
            <div class="admin-users-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h3 class="admin-users-title">User Management</h3>
                        <p class="admin-users-subtitle">Manage registered volunteers, NGOs, and administrators</p>
                    </div>
                    <div class="col-md-4">
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" name="q" value="{{ old('q', $appliedFilters['q'] ?? '') }}"
                                class="form-control search-input" placeholder="Search users, emails, organizations...">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters Card -->
            <div class="filter-card">
                <form method="GET" action="{{ route('admin.users.index') }}" id="filterForm">
                    <div class="row g-3 align-items-end">
                        <!-- Role Filter -->
                        <div class="col-md-2">
                            <label class="filter-label">Role</label>
                            <select name="role" class="form-select filter-control">
                                <option value="">All Roles</option>
                                @foreach ($roles as $r)
                                    @php
                                        $optVal = $r->role_id . '|' . $r->roleName;
                                        $applied = $appliedFilters['role'] ?? '';
                                        $isSelected =
                                            $applied === $optVal ||
                                            $applied === (string) $r->role_id ||
                                            (is_string($applied) && strtolower($applied) === strtolower($r->roleName));
                                    @endphp
                                    <option value="{{ $optVal }}"
                                        @if ($isSelected) selected @endif>
                                        {{ $r->roleName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date Range Filters -->
                        <div class="col-md-2">
                            <label class="filter-label">Joined From</label>
                            <input type="date" name="date_from"
                                value="{{ old('date_from', $appliedFilters['date_from'] ?? '') }}"
                                class="form-control filter-control">
                        </div>

                        <div class="col-md-2">
                            <label class="filter-label">Joined To</label>
                            <input type="date" name="date_to"
                                value="{{ old('date_to', $appliedFilters['date_to'] ?? '') }}"
                                class="form-control filter-control">
                        </div>

                        <!-- Sort Controls -->
                        <div class="col-md-3">
                            <label class="filter-label">Sort By</label>
                            <div class="sort-controls">
                                <select name="sort_by" class="form-select filter-control">
                                    <option value="created_at" @if (($appliedFilters['sort_by'] ?? '') === 'created_at') selected @endif>Joined
                                        Date</option>
                                    <option value="name" @if (($appliedFilters['sort_by'] ?? '') === 'name') selected @endif>Name
                                    </option>
                                    <option value="role" @if (($appliedFilters['sort_by'] ?? '') === 'role') selected @endif>Role
                                    </option>
                                </select>
                                <select name="sort_dir" class="form-select filter-control">
                                    <option value="desc" @if (($appliedFilters['sort_dir'] ?? '') === 'desc') selected @endif>Desc
                                    </option>
                                    <option value="asc" @if (($appliedFilters['sort_dir'] ?? '') === 'asc') selected @endif>Asc
                                    </option>
                                </select>
                            </div>
                        </div>

                        <!-- Per Page -->
                        <div class="col-md-1">
                            <label class="filter-label">Per Page</label>
                            <select name="per_page" class="form-select filter-control">
                                @foreach ([10, 15, 25, 50, 100] as $n)
                                    <option value="{{ $n }}"
                                        @if (intval($appliedFilters['per_page'] ?? 15) === $n) selected @endif>
                                        {{ $n }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Action Buttons -->
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-apply btn-action w-100 mb-1">
                                <i class="bi bi-filter-circle me-1"></i>Apply
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-reset btn-action w-100">
                                <i class="bi bi-arrow-clockwise me-1"></i>Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="table-card">
                <!-- Table Header -->


                <!-- Table Content -->
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 70px;">Avatar</th>
                                <th>User Profile</th>
                                <th>Contact</th>
                                <th>Role</th>
                                <th>Registration</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $user)
                                @php
                                    $displayName = $user->name;
                                    if (optional($user->ngoProfile)->organizationName) {
                                        $displayName = optional($user->ngoProfile)->organizationName;
                                    } elseif (optional($user->volunteerProfile)->name) {
                                        $displayName = optional($user->volunteerProfile)->name;
                                    } elseif (optional($user->adminProfile)->name) {
                                        $displayName = optional($user->adminProfile)->name;
                                    }

                                    $photoFile =
                                        optional($user->ngoProfile)->profilePhoto ??
                                        (optional($user->volunteerProfile)->profilePhoto ??
                                            (optional($user->adminProfile)->profilePhoto ?? null));

                                    $profileImageUrl = asset('images/default-profile.png');
                                    if ($photoFile) {
                                        $basename = trim(basename($photoFile));
                                        if ($basename && file_exists(public_path("images/profiles/{$basename}"))) {
                                            $profileImageUrl = asset("images/profiles/{$basename}");
                                        } elseif ($basename && file_exists(public_path("images/{$basename}"))) {
                                            $profileImageUrl = asset("images/{$basename}");
                                        } elseif (
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists($photoFile)
                                        ) {
                                            $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url(
                                                $photoFile,
                                            );
                                        } elseif (
                                            \Illuminate\Support\Facades\Storage::disk('public')->exists(
                                                "profiles/{$basename}",
                                            )
                                        ) {
                                            $profileImageUrl = \Illuminate\Support\Facades\Storage::disk('public')->url(
                                                "profiles/{$basename}",
                                            );
                                        }
                                    }

                                    $roleName = optional($user->role)->roleName ?? '—';
                                    $roleClass = match (strtolower($roleName)) {
                                        'volunteer' => 'badge-volunteer',
                                        'ngo' => 'badge-ngo',
                                        'admin' => 'badge-admin',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <tr>
                                    <td>
                                        <img src="{{ $profileImageUrl }}" alt="User Avatar" class="user-avatar">
                                    </td>
                                    <td>
                                        <div class="user-name">{{ $displayName }}</div>
                                        <div class="user-username">{{ $user->name }}</div>
                                    </td>
                                    <td class="user-email">{{ $user->email }}</td>
                                    <td>
                                        <span class="badge {{ $roleClass }}">{{ $roleName }}</span>
                                    </td>
                                    <td class="user-joined">
                                        {{ $user->created_at ? $user->created_at->format('M j, Y') : '—' }}
                                    </td>
                                    <td class="text-end">
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                            class="d-inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-delete btn-action"
                                                onclick="return confirm('Are you sure you want to delete this user?')">
                                                <i class="bi bi-trash me-1"></i>Delete
                                            </button>

                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        <div class="empty-state">
                                            <i class="bi bi-people empty-state-icon"></i>
                                            <p class="mb-0">No users found matching your criteria</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="pagination-container d-flex justify-content-between align-items-center">
                    <div class="pagination-info">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of
                        {{ $users->total() }} users
                    </div>
                    <div>
                        {{ $users->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Custom JavaScript -->
    <script src="{{ asset('js/admin-users.js') }}"></script>
</body>

</html>
