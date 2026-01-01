<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>NGO Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/volunteer_login.css') }}">
</head>

<body>

    <div class="container-fluid min-vh-100">
        <div class="row min-vh-100">
            <!-- Left: Form -->
            <div class="col-lg-6 d-flex align-items-center justify-content-center p-3">
                <div class="form-section">
                    <div class="header-logo d-flex align-items-center gap-2">
                        <img src="{{ asset('assets/sukarelawan_logo.png') }}" alt="Logo" style="height: 60px;">
                        <h1 class="mb-0">SukaRelawan</h1>
                    </div>
                    <h3 class="fw-bold mb-3">Welcome Back!</h3>
                    <p class="text-muted">Sign in to your NGO account to continue managing your organization and
                        activities</p>

                    <form id="login-form" method="POST" action="{{ url('/login') }}">
                        @csrf
                        <div id="form-errors" class="d-none" role="alert"></div>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="mb-4">
                            <label class="form-label">Email Address</label>
                            <div class="input-icon">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control"
                                    placeholder="Enter your email">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Password</label>
                            <div class="input-icon password-container">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="password" id="login-password" class="form-control"
                                    placeholder="Enter your password">
                                <span class="toggle-password"
                                    style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                    <i class="fas fa-eye"></i>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="remember">
                                <label class="form-check-label" for="remember">Remember me</label>
                            </div>
                            <div>

                                <a href="{{ route('password.request', ['role' => 'ngo']) }}"
                                    class="text-decoration-none"> Forgot password?</a>

                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>

                        <p class="login-link mt-4">
                            Don't have an account? <a href="{{ url('/register/ngo') }}">Register your NGO</a>
                        </p>
                    </form>
                </div>
            </div>

            <!-- Right: Image -->
            <div class="col-lg-6 d-none d-lg-flex bg-right">
                <div class="bg-right-content">
                    <p>Sign in to manage your NGO profile, events, and volunteer recruitment.</p>
                    <div class="mt-4">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <span>Manage volunteer applications</span>
                        </div>
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <span>Create and publish events</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle fa-2x me-3"></i>
                            <span>Track impact and engagement</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/volunteer_login.js') }}"></script>

</body>

</html>
