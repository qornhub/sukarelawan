<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | SukaRelawan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2C3E50;
            --primary-hover: #1A252F;
            --text-color: #333;
            --light-gray: #f9f9f9;
            --shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            --error-color: #dc3545;
            --error-bg: #f8d7da;
            --success-color: #28a745;
            --success-bg: #d4edda;
        }

        body {
            background-color: var(--light-gray);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
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

        .status-message {
            font-size: 0.95rem;
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 8px;
        }

        .status-success {
            background-color: var(--success-bg);
            border: 1px solid #c3e6cb;
            color: var(--success-color);
        }

        .status-error {
            background-color: var(--error-bg);
            border: 1px solid #f5c2c7;
            color: var(--error-color);
        }

        .status-message ul {
            margin-bottom: 0;
            padding-left: 1.5rem;
        }

        .status-message li {
            margin-bottom: 0.25rem;
        }

        .status-message li:last-child {
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

        .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        .header-logo img {
            height: 60px;
        }

        .header-logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            color: var(--primary-color);
        }

        .password-toggle {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #adb5bd;
        }

        .password-container {
            position: relative;
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

        @media (max-width: 575.98px) {
            .form-section {
                padding: 1.5rem;
            }

            .form-section h2 {
                font-size: 1.5rem;
            }
        }

        .password-strength {
            margin-top: 5px;
            height: 5px;
            border-radius: 3px;
            overflow: hidden;
            background: #e9ecef;
        }

        .password-strength-meter {
            height: 100%;
            width: 0;
            background: #dc3545;
            transition: width 0.3s, background 0.3s;
        }

        .password-hints {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            color: #6c757d;
        }

        .password-match {
            margin-top: 0.5rem;
            font-size: 0.85rem;
            padding: 0.5rem;
            border-radius: 4px;
            display: none;
        }
    </style>
</head>

<body>
    <div class="form-section">
        <div class="header-logo">
            <img src="{{ asset('assets/sukarelawan_logo.png') }}" alt="Logo">
            <h1>SukaRelawan</h1>
        </div>
        <h2 class="fw-bold mb-3">Create New Password</h2>
        <p class="text-muted">Your new password must be different from your previous password</p>

        <form id="reset-password-form" method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">
            <input type="hidden" name="role" value="{{ request('role') }}">

            <div class="mb-4">
                <label class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ $email ?? old('email') }}" class="form-control"
                        required>
                </div>
                @error('email')
                    <div class="status-message status-error mt-2">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                    </div>
                @enderror
            </div>

            <div class="mb-4">
                <label class="form-label">New Password</label>
                <div class="input-icon password-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" id="new-password" class="form-control"
                        placeholder="Enter new password" required>
                    <span class="password-toggle" id="toggle-new-password"
                        style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>

                </div>
                <div class="password-strength mt-2">
                    <div class="password-strength-meter" id="password-strength-meter"></div>
                </div>
                <div class="password-hints">
                    <div><i class="fas fa-circle" style="font-size: 6px; vertical-align: middle;"></i> Must be at least
                        8 characters</div>
                    <div><i class="fas fa-circle" style="font-size: 6px; vertical-align: middle;"></i> Should contain
                        letters and numbers</div>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Confirm Password</label>
                <div class="input-icon password-container">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password_confirmation" id="confirm-password" class="form-control"
                        placeholder="Confirm your password" required>

                    <span class="password-toggle" id="toggle-confirm-password"
                        style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>
                <div id="password-match" class="password-match"></div>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-sync-alt me-2"></i>Reset Password
            </button>


        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            function setupPasswordToggle(toggleId, inputId) {
                const toggle = document.getElementById(toggleId);
                const input = document.getElementById(inputId);

                if (toggle && input) {
                    toggle.addEventListener('click', function() {
                        const icon = this.querySelector('i');
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.remove('fa-eye');
                            icon.classList.add('fa-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.remove('fa-eye-slash');
                            icon.classList.add('fa-eye');
                        }
                    });

                    // Add keyboard accessibility
                    toggle.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            this.click();
                        }
                    });
                }
            }

            // Setup toggles
            setupPasswordToggle('toggle-new-password', 'new-password');
            setupPasswordToggle('toggle-confirm-password', 'confirm-password');

            // Password strength meter
            const passwordInput = document.getElementById('new-password');
            const strengthMeter = document.getElementById('password-strength-meter');

            if (passwordInput && strengthMeter) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;

                    // Check password length
                    if (password.length >= 8) strength += 25;
                    if (password.length >= 12) strength += 15;

                    // Check for uppercase letters
                    if (/[A-Z]/.test(password)) strength += 20;

                    // Check for lowercase letters
                    if (/[a-z]/.test(password)) strength += 20;

                    // Check for numbers
                    if (/[0-9]/.test(password)) strength += 20;

                    // Check for special characters
                    if (/[^A-Za-z0-9]/.test(password)) strength += 20;

                    // Update strength meter
                    strengthMeter.style.width = strength + '%';

                    // Update color
                    if (strength < 50) {
                        strengthMeter.style.backgroundColor = '#dc3545';
                    } else if (strength < 80) {
                        strengthMeter.style.backgroundColor = '#ffc107';
                    } else {
                        strengthMeter.style.backgroundColor = '#28a745';
                    }
                });
            }

            // Password match validation
            const confirmPasswordInput = document.getElementById('confirm-password');
            const passwordMatchDiv = document.getElementById('password-match');

            if (confirmPasswordInput && passwordMatchDiv) {
                confirmPasswordInput.addEventListener('input', function() {
                    const password = passwordInput.value;
                    const confirmPassword = this.value;

                    if (confirmPassword === '') {
                        passwordMatchDiv.style.display = 'none';
                        return;
                    }

                    passwordMatchDiv.style.display = 'block';

                    if (password === confirmPassword) {
                        passwordMatchDiv.innerHTML =
                            '<i class="fas fa-check-circle me-2"></i>Passwords match';
                        passwordMatchDiv.className = 'password-match status-success';
                    } else {
                        passwordMatchDiv.innerHTML =
                            '<i class="fas fa-exclamation-circle me-2"></i>Passwords do not match';
                        passwordMatchDiv.className = 'password-match status-error';
                    }
                });
            }

            // Form validation
            const form = document.getElementById('reset-password-form');

            if (form) {
                form.addEventListener('submit', function(e) {
                    let isValid = true;

                    // Reset previous errors
                    const inputs = this.querySelectorAll('.form-control');
                    inputs.forEach(input => {
                        input.classList.remove('error');
                        const inputIcon = input.closest('.input-icon');
                        if (inputIcon) inputIcon.classList.remove('error');
                    });

                    // Check required fields
                    const requiredFields = this.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('error');
                            const inputIcon = field.closest('.input-icon');
                            if (inputIcon) inputIcon.classList.add('error');
                        }
                    });

                    // Special validation for password
                    const password = passwordInput.value;
                    const confirmPassword = confirmPasswordInput.value;

                    // Check password strength
                    if (password.length < 8) {
                        isValid = false;
                        passwordInput.classList.add('error');
                        passwordInput.closest('.input-icon').classList.add('error');
                    }

                    // Check password match
                    if (password !== confirmPassword) {
                        isValid = false;
                        confirmPasswordInput.classList.add('error');
                        confirmPasswordInput.closest('.input-icon').classList.add('error');
                    }

                    if (!isValid) {
                        e.preventDefault();

                        // Show error message
                        let errorBox = this.querySelector('.form-errors');
                        if (!errorBox) {
                            errorBox = document.createElement('div');
                            errorBox.className = 'status-message status-error form-errors';
                            errorBox.setAttribute('role', 'alert');
                            this.prepend(errorBox);
                        }

                        errorBox.innerHTML =
                            '<i class="fas fa-exclamation-circle me-2"></i>Please fix the errors in the form';
                        errorBox.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    } else {
                        // Add loading state
                        const submitBtn = this.querySelector('button[type="submit"]');
                        const originalText = submitBtn.innerHTML;
                        submitBtn.innerHTML =
                            '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Resetting password...';
                        submitBtn.disabled = true;
                    }
                });
            }
        });
    </script>
</body>

</html>
