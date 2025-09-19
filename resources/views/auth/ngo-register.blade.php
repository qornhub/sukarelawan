<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>NGO Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/volunteer_register.css') }}">
</head>
<body>

<div class="container-fluid min-vh-100">
    <div class="row min-vh-100">
        <!-- Left: Form -->
        <div class="col-lg-6 d-flex align-items-center justify-content-center p-3">
            <div class="form-section">
                <div class="header-logo d-flex align-items-center gap-2">
    <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="Logo" style="height: 60px;">
    <h1 class="mb-0">SukaRelawan</h1>
</div>
                <h3 class="fw-bold mb-3">Create NGO Account</h3>
                <p class="text-muted">Register your NGO to connect with volunteers and manage your initiatives</p>

                <form id="signup-form" method="POST" action="{{ url('/register/ngo') }}">
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


                    <div class="mb-3">
                        <label class="form-label">Organization Name</label>
                        <div class="input-icon">
                            <i class="fas fa-building"></i>
                            <input type="text" name="organizationName" value="{{ old('organizationName') }}" class="form-control" placeholder="Enter your NGO name">
                        </div>
                    </div>


                    <div class="mb-3">
    <label class="form-label">Registration Number</label>
    <div class="input-icon">
        <i class="fas fa-id-card"></i>
        <input type="text" name="registrationNumber" value="{{ old('registrationNumber') }}" class="form-control" placeholder="Enter your registration number">
    </div>
</div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-envelope"></i>
                           <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Enter your email">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <div class="input-icon">
                            <i class="fas fa-phone"></i>
                            <input type="text" name="contactNumber" value="{{ old('contactNumber') }}" class="form-control" placeholder="Enter your phone number">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Country/Region</label>
                        <div class="input-icon">
                            <i class="fas fa-globe"></i>
                           <select name="country" class="form-select form-control">
                           <option value="" disabled {{ old('country') ? '' : 'selected' }}>Select your country</option>
                            <option value="Malaysia" {{ old('country') == 'Malaysia' ? 'selected' : '' }}>Malaysia</option>
                           <option value="Singapore" {{ old('country') == 'Singapore' ? 'selected' : '' }}>Singapore</option>
                           <option value="Philippines" {{ old('country') == 'Philippines' ? 'selected' : '' }}>Philippines</option>
                           <option value="Indonesia" {{ old('country') == 'Indonesia' ? 'selected' : '' }}>Indonesia</option>
                           <option value="Thailand" {{ old('country') == 'Thailand' ? 'selected' : '' }}>Thailand</option>
                           <option value="Vietnam" {{ old('country') == 'Vietnam' ? 'selected' : '' }}>Vietnam</option>
                           </select>
                        </div>
                    </div>


                    <div class="mb-3">
                         <label class="form-label">Password</label>
                         <div class="input-icon position-relative">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password" id="password" class="form-control" placeholder="Create a password">
                            <span class="toggle-password" style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                         </div>
                         <div class="password-strength">
                            <div class="strength-meter">
                                <div class="strength-meter-fill" id="strength-meter"></div>
                            </div>
                            <div class="strength-text" id="strength-text">Password strength: very weak</div>
                         </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <div class="input-icon position-relative">
                            <i class="fas fa-lock"></i>
                            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirm your password">
                            <span class="toggle-password" style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <div class="form-check mb-4 mt-4">
                        <input class="form-check-input" type="checkbox" id="terms">
                        <label class="form-check-label" for="terms">
                            I agree to all the <a href="#">Terms</a> and <a href="#">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-user-plus me-2"></i>Create Account
                    </button>

                    <p class="login-link mt-4">
                
                         Already have an account? <a href="{{ route('login.ngo') }}">Sign In</a>
                    </p>
                </form>
            </div>
        </div>

        <!-- Right: Image -->
        <div class="col-lg-6 d-none d-lg-flex bg-right">
            <div class="bg-right-content">
                <h3>Join Our Community of Volunteers</h3>
                <p>Make a difference in your community by volunteering your time and skills. Help those in need and be part of something meaningful.</p>
                <div class="mt-4">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <span>Connect with local organizations</span>
                    </div>
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <span>Make a positive impact</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle fa-2x me-3"></i>
                        <span>Earn badges</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/ngo_register.js') }}"></script>

</body>
</html>