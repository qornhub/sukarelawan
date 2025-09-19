document.addEventListener('DOMContentLoaded', function() {
    // Password visibility toggle
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.previousElementSibling;
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
    });

    // Password strength indicator
    const passwordInput = document.getElementById('password');
    const strengthMeter = document.getElementById('strength-meter');
    const strengthText = document.getElementById('strength-text');

    passwordInput.addEventListener('input', function() {
        const password = passwordInput.value;
        let strength = 0;

        if (password.length >= 8) strength += 25;
        if (password.length >= 12) strength += 15;
        if (/[A-Z]/.test(password)) strength += 15;
        if (/[a-z]/.test(password)) strength += 15;
        if (/[0-9]/.test(password)) strength += 15;
        if (/[^A-Za-z0-9]/.test(password)) strength += 15;

        strengthMeter.style.width = strength + '%';

        if (strength < 40) {
            strengthMeter.style.backgroundColor = '#dc3545';
            strengthText.textContent = 'Password strength: weak';
        } else if (strength < 70) {
            strengthMeter.style.backgroundColor = '#ffc107';
            strengthText.textContent = 'Password strength: medium';
        } else if (strength < 100) {
            strengthMeter.style.backgroundColor = '#28a745';
            strengthText.textContent = 'Password strength: strong';
        } else {
            strengthMeter.style.backgroundColor = '#007bff';
            strengthText.textContent = 'Password strength: very strong';
        }
    });

    // Form validation
    const form = document.getElementById('signup-form');
    const errorBox = document.getElementById('form-errors');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        document.querySelectorAll('.form-control').forEach(input => {
            input.classList.remove('error');
            input.closest('.input-icon').classList.remove('error');
        });
        errorBox.classList.add('d-none');
        errorBox.innerHTML = '';

        const orgName = form.querySelector('input[name="organizationName"]').value.trim();
        const regNo = form.querySelector('input[name="registrationNumber"]').value.trim();
        const email = form.querySelector('input[name="email"]').value.trim();
        const phone = form.querySelector('input[name="contactNumber"]').value.trim();
        const country = form.querySelector('select[name="country"]').value;
        const password = form.querySelector('input[name="password"]').value;
        const confirmPassword = form.querySelector('input[name="password_confirmation"]').value;
        const terms = form.querySelector('#terms').checked;

        const errors = [];

        if (!orgName) {
            errors.push('Organization name is required');
            highlightError(form.querySelector('input[name="organizationName"]'));
        }

        if (!regNo) {
            errors.push('Registration number is required');
            highlightError(form.querySelector('input[name="registrationNumber"]'));
        }

        if (!email) {
            errors.push('Email address is required');
            highlightError(form.querySelector('input[name="email"]'));
        } else if (!validateEmail(email)) {
            errors.push('Please enter a valid email address');
            highlightError(form.querySelector('input[name="email"]'));
        }

        if (!phone) {
            errors.push('Phone number is required');
            highlightError(form.querySelector('input[name="contactNumber"]'));
        } else if (!validatePhone(phone)) {
            errors.push('Please enter a valid phone number (digits only, 7–15 digits)');
            highlightError(form.querySelector('input[name="contactNumber"]'));
        }

        if (!country) {
            errors.push('Please select your country');
            highlightError(form.querySelector('select[name="country"]'));
        }

        if (!password) {
            errors.push('Password is required');
            highlightError(form.querySelector('input[name="password"]'));
        } else if (password.length < 8) {
            errors.push('Password must be at least 8 characters');
            highlightError(form.querySelector('input[name="password"]'));
        }

        if (!confirmPassword) {
            errors.push('Please confirm your password');
            highlightError(form.querySelector('input[name="password_confirmation"]'));
        } else if (password !== confirmPassword) {
            errors.push('Passwords do not match');
            highlightError(form.querySelector('input[name="password"]'));
            highlightError(form.querySelector('input[name="password_confirmation"]'));
        }

        if (!terms) {
            errors.push('You must agree to the terms and privacy policy');
        }

        if (errors.length > 0) {
            errorBox.classList.remove('d-none');
            const errorList = document.createElement('ul');
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            errorBox.appendChild(errorList);
            errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
        } else {
            form.submit();
        }
    });

    function highlightError(input) {
        if (input) {
            input.classList.add('error');
            if (input.closest('.input-icon')) {
                input.closest('.input-icon').classList.add('error');
            }
        }
    }

    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }

    function validatePhone(phone) {
        const re = /^\d{7,15}$/;
        return re.test(phone);
    }
});
