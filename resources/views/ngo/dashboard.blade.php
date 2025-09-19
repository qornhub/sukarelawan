<!DOCTYPE html>
<html>
<head>
    <title>NGO Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-5">
    <h1 class="mb-4">Welcome, {{ Auth::user()->NGOProfile->name ?? Auth::user()->email }}!</h1>

    <div class="card">
        <div class="card-body">
            <p>You are now logged in as a <strong>NGO</strong>.</p>

            <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
            <p><strong>Country:</strong> {{ Auth::user()->NGOProfile->country ?? 'N/A' }}</p>
            <p><strong>Contact:</strong> {{ Auth::user()->NGOProfile->contactNumber ?? 'N/A' }}</p>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger mt-3">Logout</button>
            </form>
        </div>
    </div>
</div>

</body>
</html>