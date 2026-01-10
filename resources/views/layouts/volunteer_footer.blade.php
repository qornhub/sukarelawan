<div class="volunteer-footer-component">
    <footer class="volunteer-footer">
        <div class="volunteer-footer-wave"></div>
        
        <div class="volunteer-footer-content">
            <div class="volunteer-footer-row">
                <div class="volunteer-footer-col">
                    <h6 class="volunteer-footer-heading">Navigation</h6>
                    <ul class="volunteer-footer-links">
                        <li><a href="#"><i class="fas fa-home"></i> Home</a></li>
                        <li><a href="#"><i class="fas fa-calendar-alt"></i> Events</a></li>
                        <li><a href="#"><i class="fas fa-blog"></i> Blog</a></li>
                        <li><a href="#"><i class="fas fa-award"></i> Rewards</a></li>
                    </ul>
                </div>
                
                <div class="volunteer-footer-col">
                    <h6 class="volunteer-footer-heading">Contact</h6>
                    <ul class="volunteer-footer-links">
                        <li>
                            <a href="mailto:sukarelwan@gmail.com">
                                <i class="fas fa-envelope"></i> sukarelwan@gmail.com
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fab fa-instagram"></i> Instagram
                            </a>
                        </li>
                        <li>
                            <a href="#">
                                <i class="fab fa-linkedin-in"></i> LinkedIn
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="volunteer-footer-col">
                   <a href="{{ route('register.ngo') }}" class="volunteer-cta-button">
                        Become an Event Organizer 
                        <strong>Sign Up</strong>
                    </a>
                </div>
            </div>
            
            <div class="volunteer-footer-bottom">
                <img src="{{ asset('assets/sukarelawan_logo.png') }}" alt="Logo" class="volunteer-footer-logo">
                <span class="volunteer-copyright">&copy; 2026 SukaRelawan. All rights reserved.</span>
            </div>
        </div>
    </footer>
</div>

<style>
    .volunteer-footer-component {
        --vol-footer-primary-color: #004AAD;
        --vol-footer-primary-light: #3a7bff;
        --vol-footer-text-light: #f8f9fa;
        --vol-footer-text-gray: #6c757d;
        --vol-footer-border-color: #e0e0e0;
        --vol-footer-transition: all 0.3s ease;
        --vol-footer-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    /* Footer Styles */
    .volunteer-footer-component .volunteer-footer {
        background: white;
        border-top: 1px solid var(--vol-footer-border-color);
        padding: 3rem 1.5rem 1.5rem;
        position: relative;
        overflow: hidden;
        margin-top: 0;
    }
    
    .volunteer-footer-component .volunteer-footer-wave {
        position: absolute;
        top: -2px;
        left: 0;
        right: 0;
        height: 10px;
        background: linear-gradient(90deg, var(--vol-footer-primary-color), var(--vol-footer-primary-light));
    }
    
    .volunteer-footer-component .volunteer-footer-content {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .volunteer-footer-component .volunteer-footer-row {
        display: grid;
        /* Mobile Default: Auto fit columns */
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2rem;
        margin-bottom: 2rem;
    }
    
    .volunteer-footer-component .volunteer-footer-col {
        margin-bottom: 1.5rem;
    }
    
    .volunteer-footer-component .volunteer-footer-heading {
        position: relative;
        font-weight: 700;
        color: var(--vol-footer-primary-color);
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        font-size: 1.1rem;
    }
    
    .volunteer-footer-component .volunteer-footer-heading::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 2px;
        background: var(--vol-footer-primary-light);
    }
    
    .volunteer-footer-component .volunteer-footer-links {
        list-style: none;
        padding: 0;
    }
    
    .volunteer-footer-component .volunteer-footer-links li {
        margin-bottom: 0.8rem;
    }
    
    .volunteer-footer-component .volunteer-footer-links a {
        color: #555;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: var(--vol-footer-transition);
    }
    
    .volunteer-footer-component .volunteer-footer-links a:hover {
        color: var(--vol-footer-primary-color);
        transform: translateX(5px);
    }
    
    .volunteer-footer-component .volunteer-footer-links a i {
        font-size: 0.9rem;
        color: var(--vol-footer-primary-color);
        width: 20px;
        text-align: center;
    }
    
    .volunteer-footer-component .volunteer-cta-button {
        background: var(--vol-footer-primary-color);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 1.2rem;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        display: block;
        transition: var(--vol-footer-transition);
        box-shadow: 0 4px 15px rgba(0, 74, 173, 0.2);
    }
    
    .volunteer-footer-component .volunteer-cta-button:hover {
        background: var(--vol-footer-primary-light);
        transform: translateY(-5px);
        box-shadow: 0 6px 20px rgba(0, 74, 173, 0.3);
    }
    
    .volunteer-footer-component .volunteer-cta-button strong {
        display: block;
        font-size: 1.1rem;
        margin-top: 0.5rem;
    }
    
    .volunteer-footer-component .volunteer-footer-bottom {
        max-width: 1200px;
        margin: 0 auto;
        padding-top: 2rem;
        border-top: 1px solid rgba(0,0,0,0.05);
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    
    .volunteer-footer-component .volunteer-footer-logo {
        height: 40px;
        margin-bottom: 1rem;
        transition: var(--vol-footer-transition);
    }
    
    .volunteer-footer-component .volunteer-footer-logo:hover {
        transform: scale(1.05);
    }
    
    .volunteer-footer-component .volunteer-copyright {
        color: var(--vol-footer-text-gray);
        font-size: 0.9rem;
    }

    /* Responsive styles */
    @media (min-width: 768px) {
        /* CHANGED FROM 4 to 3 COLUMNS HERE */
        .volunteer-footer-component .volunteer-footer-row {
            grid-template-columns: repeat(3, 1fr); 
        }
        
        .volunteer-footer-component .volunteer-footer-col:last-child {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
    }
    
    @media (max-width: 767px) {
        .volunteer-footer-component .volunteer-cta-button {
            margin-top: 1rem;
        }
        
        .volunteer-footer-component .volunteer-footer-row {
            grid-template-columns: 1fr;
        }
        
        .volunteer-footer-component .volunteer-footer-col:last-child {
            text-align: center;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctaButton = document.querySelector('.volunteer-footer-component .volunteer-cta-button');
        setTimeout(() => {
            if (ctaButton) {
                ctaButton.style.transform = 'translateY(0)';
            }
        }, 500);
    });
</script>