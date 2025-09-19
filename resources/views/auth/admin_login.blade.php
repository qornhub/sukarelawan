<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Portal | Secure Access</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --admin-blue: #004AAD;
            --admin-blue-dark: #003780;
            --admin-gray: #f8f9fa;
            --admin-gray-dark: #e9ecef;
            --admin-danger: #dc3545;
            --admin-success: #28a745;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        
        .admin-login-container {
            width: 100%;
            max-width: 420px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            position: relative;
        }
        
        .admin-header {
            background: var(--admin-blue);
            padding: 25px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        .admin-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2ecc71, #f39c12);
        }
        
        .admin-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            color: #fff;
        }
        
        .admin-title {
            font-size: 1.6rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .admin-subtitle {
            font-size: 0.9rem;
            opacity: 0.85;
        }
        
        .admin-form {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--admin-blue);
            font-size: 0.9rem;
        }
        
        .input-icon {
            position: relative;
        }
        
        .input-icon i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        
        .form-control {
            width: 100%;
            padding: 13px 15px 13px 45px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background: #f9f9f9;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--admin-blue);
            background: white;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        .password-container {
            position: relative;
        }
        
        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            background: var(--admin-blue);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        
        .btn-login:hover {
            background: var(--admin-blue-dark);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(44, 62, 80, 0.2);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .error-box {
            background: #f8d7da;
            color: var(--admin-danger);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 22px;
            font-size: 0.9rem;
            border-left: 4px solid var(--admin-danger);
        }
        
        .error-box ul {
            padding-left: 20px;
            margin-top: 8px;
        }
        
        .error-box li {
            margin-bottom: 5px;
        }
        
        .security-tips {
            margin-top: 25px;
            padding: 15px;
            background: #f0f8ff;
            border-radius: 8px;
            font-size: 0.85rem;
            color: #4a6fa5;
        }
        
        .security-tips h4 {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--admin-blue);
        }
        
        .security-tips ul {
            padding-left: 20px;
        }
        
        .security-tips li {
            margin-bottom: 7px;
        }
        
        .footer {
            text-align: center;
            padding: 20px 0 0;
            color: #6c757d;
            font-size: 0.85rem;
        }
        
        @media (max-width: 480px) {
            .admin-login-container {
                max-width: 100%;
            }
            
            .admin-header {
                padding: 20px 15px;
            }
            
            .admin-form {
                padding: 25px 20px;
            }
        }
        
        .password-strength {
            height: 4px;
            background: #e9ecef;
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }
        
        .strength-meter {
            height: 100%;
            width: 0;
            background: #dc3545;
            transition: width 0.3s;
        }
        
        .password-hints {
            font-size: 0.75rem;
            color: #6c757d;
            margin-top: 5px;
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    
    <div class="admin-login-container">
        <div class="admin-header">
            <div class="admin-icon">
                <i class="fas fa-lock"></i>
            </div>
            <h1 class="admin-title">Administrator Portal</h1>
            <p class="admin-subtitle">Secure access to system controls</p>
        </div>
        
        <div class="admin-form">
            @if($errors->any())
                <div class="error-box">
                    <strong>Authentication Failed</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <form method="POST" action="{{ url('/admin/login') }}">
                @csrf
                
                <div class="form-group">
                    <label class="form-label">Email Address</label>
                    <div class="input-icon">
                        <i class="fas fa-envelope"></i>
                        <input type="email" name="email" class="form-control" placeholder="admin@example.com" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <div class="input-icon password-container">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>

                        <span class="toggle-password" id="togglePassword" style="position: absolute; right: 3rem; top: 50%; transform: translateY(-50%); cursor: pointer;">
                                <i class="fas fa-eye"></i>
                            </span>
                    </div>
                    <div class="password-strength">
                        <div class="strength-meter" id="strengthMeter"></div>
                    </div>
                    <div class="password-hints">
                        <span><i class="fas fa-circle" style="font-size: 6px;"></i> Minimum 8 characters</span>
                        <span><i class="fas fa-circle" style="font-size: 6px;"></i> Letters & numbers</span>
                    </div>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i> Login to Dashboard
                </button>
            </form>
            
            <div class="security-tips">
                <h4><i class="fas fa-shield-alt"></i> Security Best Practices</h4>
                <ul>
                    <li>Never share your credentials with anyone</li>
                    <li>Always log out after completing your tasks</li>
                    <li>Enable two-factor authentication when available</li>
                    <li>Regularly update your password</li>
                </ul>
            </div>
            
            <div class="footer">
                <p>&copy; 2026 sukarelawan. Secure Access Only.</p>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const togglePassword = document.getElementById('togglePassword');
            const passwordInput = document.getElementById('password');
            
            if (togglePassword && passwordInput) {
                togglePassword.addEventListener('click', function() {
                    const icon = this.querySelector('i');
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('fa-eye');
                        icon.classList.add('fa-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('fa-eye-slash');
                        icon.classList.add('fa-eye');
                    }
                });
            }
            
            // Password strength indicator
            const strengthMeter = document.getElementById('strengthMeter');
            
            if (passwordInput && strengthMeter) {
                passwordInput.addEventListener('input', function() {
                    const password = this.value;
                    let strength = 0;
                    
                    // Length check
                    if (password.length >= 8) strength += 25;
                    if (password.length >= 12) strength += 15;
                    
                    // Character diversity
                    if (/[A-Z]/.test(password)) strength += 20;
                    if (/[a-z]/.test(password)) strength += 20;
                    if (/[0-9]/.test(password)) strength += 20;
                    if (/[^A-Za-z0-9]/.test(password)) strength += 20;
                    
                    // Cap at 100
                    strength = Math.min(strength, 100);
                    strengthMeter.style.width = strength + '%';
                    
                    // Update color
                    if (strength < 40) {
                        strengthMeter.style.backgroundColor = '#dc3545';
                    } else if (strength < 80) {
                        strengthMeter.style.backgroundColor = '#ffc107';
                    } else {
                        strengthMeter.style.backgroundColor = '#28a745';
                    }
                });
            }
            
            // Form submission feedback
            const form = document.querySelector('form');
            
            if (form) {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    
                    // Show loading state
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Authenticating...';
                    submitBtn.disabled = true;
                    
                    // Reset after 5 seconds (in case of error)
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 5000);
                });
            }
        });
    </script>
</body>
</html>