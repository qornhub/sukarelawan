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

    // Form validation and submission
    const form = document.getElementById('login-form');
    const errorBox = document.getElementById('form-errors');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Reset previous errors
            if (errorBox) {
                errorBox.classList.add('d-none');
                errorBox.innerHTML = '';
            }
            
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('error');
                input.closest('.input-icon').classList.remove('error');
            });
            
            // Get form values
            const email = form.querySelector('input[name="email"]') ? form.querySelector('input[name="email"]').value.trim() : '';
            const password = form.querySelector('input[name="password"]') ? form.querySelector('input[name="password"]').value : '';
            
            const errors = [];
            
            // Validate email
            if (!email) {
                errors.push('Email address is required');
                highlightError(form.querySelector('input[name="email"]'));
            } else if (!validateEmail(email)) {
                errors.push('Please enter a valid email address');
                highlightError(form.querySelector('input[name="email"]'));
            }
            
            // Validate password
            if (!password) {
                errors.push('Password is required');
                highlightError(form.querySelector('input[name="password"]'));
            } else if (password.length < 6) {
                errors.push('Password must be at least 6 characters');
                highlightError(form.querySelector('input[name="password"]'));
            }
            
            // If there are errors, prevent form submission and show them
            if (errors.length > 0) {
                e.preventDefault();
                
                if (errorBox) {
                    errorBox.classList.remove('d-none');
                    const errorList = document.createElement('ul');
                    
                    errors.forEach(error => {
                        const li = document.createElement('li');
                        li.textContent = error;
                        errorList.appendChild(li);
                    });
                    
                    errorBox.appendChild(errorList);
                    
                    // Scroll to the top to show errors
                    errorBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            } else {
                form.submit();
            }
        });
    }
    
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
    
    function showSuccessMessage(message) {
        // Create success message element
        const successBox = document.createElement('div');
        successBox.className = 'alert alert-success';
        successBox.setAttribute('role', 'alert');
        successBox.innerHTML = `
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <div>${message}</div>
            </div>
        `;
        
        // Insert before the form
        const form = document.getElementById('login-form');
        if (form) {
            form.parentNode.insertBefore(successBox, form);
            
            // Remove after 3 seconds
            setTimeout(() => {
                successBox.remove();
            }, 3000);
        }
    }
});