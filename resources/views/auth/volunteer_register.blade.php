<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Volunteer Sign Up</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/volunteer_register.css') }}">
    <style>
        :root {
            --primary-color: #004aad;
            --primary-hover: #003780;
            --text-color: #333;
            --light-gray: #f9f9f9;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --error-color: #dc3545;
            --error-bg: #f8d7da;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        }

        .form-section {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            padding: 2.5rem;
            max-width: 500px;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        .form-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #3498db, #2ecc71, #f39c12);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control {
            padding: 0.75rem 1rem;
            padding-left: 2.5rem;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
        }
        
        .form-control.error {
            border-color: var(--error-color);
            background-color: #fff9fa;
        }
        
        .form-control.error:focus {
            border-color: var(--error-color);
            box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
        }

        #form-errors {
            font-size: 0.95rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
            background-color: var(--error-bg);
            border: 1px solid #f5c2c7;
            color: var(--error-color);
        }

        #form-errors ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        #form-errors li {
            margin-bottom: 0.25rem;
        }

        #form-errors li:last-child {
            margin-bottom: 0;
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            top: 50%;
            left: 1rem;
            transform: translateY(-50%);
            color: #adb5bd;
            transition: color 0.3s;
        }
        
        .input-icon.error i {
            color: var(--error-color);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            font-weight: 600;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .btn-primary:hover {
            background-color: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.2);
        }

        .bg-right {
            background: linear-gradient(45deg, rgba(44, 62, 80, 0.8), rgba(52, 152, 219, 0.8)), url('https://images.unsplash.com/photo-1543269865-cbf427effbad?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1200&q=80') no-repeat center center;
            background-size: cover;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 2rem;
            color: white;
        }

        .bg-right-content {
            max-width: 500px;
            margin: 0 auto;
            text-align: center;
        }

        .bg-right h3 {
            font-weight: 700;
            margin-bottom: 1.5rem;
            font-size: 2.2rem;
        }

        .bg-right p {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 1.5rem;
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #6c757d;
        }

        .login-link a {
            color: var(--primary-color);
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }

        .login-link a:hover {
            text-decoration: underline;
            color: var(--primary-hover);
        }

        .form-check-label a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
        }

        .form-check-label a:hover {
            text-decoration: underline;
        }
        
        .password-strength {
            margin-top: 0.25rem;
            font-size: 0.85rem;
        }
        
        .strength-meter {
            height: 5px;
            background-color: #e9ecef;
            border-radius: 3px;
            margin-top: 0.25rem;
            overflow: hidden;
        }
        
        .strength-meter-fill {
            height: 100%;
            width: 0%;
            background-color: #dc3545;
            transition: width 0.3s, background-color 0.3s;
        }
        
        .strength-text {
            font-size: 0.8rem;
            margin-top: 0.25rem;
            font-weight: 500;
        }
        
        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .header-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        
        .header-logo i {
            font-size: 2rem;
            color: var(--primary-color);
        }
        
        .header-logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: var(--primary-color);
        }

        @media (max-width: 991.98px) {
            .bg-right {
                display: none;
            }

            .form-section {
                padding: 2rem 1.5rem;
                margin-top: 2rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .form-section {
                padding: 1.5rem;
            }
            
            .form-section h3 {
                font-size: 1.5rem;
            }
        }


    </style>
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
                <h3 class="fw-bold mb-3">Create Volunteer Account</h3>
                <p class="text-muted">Join our community of volunteers and start making a difference today</p>

                <form id="signup-form" method="POST" action="{{ url('/register/volunteer') }}">
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
                        <label class="form-label">Full Name</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                           <input type="text" name="name" value="{{ old('name') }}" class="form-control" placeholder="Enter your full name">

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
                        <label class="form-label">Date of Birth</label>
                        <div class="input-icon">
                            <i class="fas fa-calendar"></i>
                            <input type="date" name="dateOfBirth" class="form-control" value="{{ old('dateOfBirth') }}">
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
                        Already have an account? <a href="{{ url('/login/volunteer') }}">Sign In</a>
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

<script src="{{ asset('js/volunteer_register.js') }}"></script>

</body>
</html>