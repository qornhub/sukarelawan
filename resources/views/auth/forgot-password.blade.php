<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | SukaRelawan</title>
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
    </style>
</head>
<body>
    <div class="form-section">
        <div class="header-logo">
            <img src="{{ asset('images/sukarelawan_logo.png') }}" alt="Logo">
            <h1>SukaRelawan</h1>
        </div>
        <h2 class="fw-bold mb-3">Reset Your Password</h2>
        <p class="text-muted">Enter your email to receive a password reset link</p>

        @if (session('status'))
        <div class="status-message status-success">
            <i class="fas fa-check-circle me-2"></i>{{ session('status') }}
        </div>
        @endif

        <form id="forgot-password-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            <input type="hidden" name="role" value="{{ request('role') }}">

            <div class="mb-4">
                <label class="form-label">Email Address</label>
                <div class="input-icon">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" placeholder="Enter your email" required>
                </div>
                @error('email')
                <div class="status-message status-error mt-2">
                    <i class="fas fa-exclamation-circle me-2"></i>{{ $message }}
                </div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane me-2"></i>Send Reset Link
            </button>

            
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('forgot-password-form');
            
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
                    const emailField = this.querySelector('input[name="email"]');
                    if (!emailField.value.trim()) {
                        isValid = false;
                        emailField.classList.add('error');
                        const inputIcon = emailField.closest('.input-icon');
                        if (inputIcon) inputIcon.classList.add('error');
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
                        
                        errorBox.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>Please enter your email address';
                        errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            }
        });
    </script>
</body>
</html>
