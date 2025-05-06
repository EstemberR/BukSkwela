<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>BukSkwela | Enrollment Repository System</title>
    
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- AOS -->
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <!-- Custom CSS -->
        <link href="{{ asset('assets/css/pages/landing.css') }}" rel="stylesheet">
    </head>
    <body>
        <!-- Header -->
        <header id="header" class="header">
            <div class="container d-flex justify-content-between align-items-center">
                <a href="{{ url('/') }}" class="logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="BukSkwela Logo">
                </a>
                
                <div class="d-flex align-items-center">
                    <nav id="navmenu" class="navmenu me-4">
                        <ul>
                            <li><a href="#hero" class="active">Home</a></li>
                            <li><a href="#features">Features</a></li>
                            <li><a href="#pricing">Pricing</a></li>
                        </ul>
                    </nav>

                    <div class="d-flex">
                        <a href="{{ url('/login') }}" class="btn-primary">Login</a>
                        <a href="{{ url('/register') }}" class="btn-primary">Register</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="hero" class="hero">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-10 text-center">
                        <p class="small-title">ENROLLMENT REPOSITORY SYSTEM</p>
                        <h1>No more missing files <br> and manual tracking</h1>
                        <p class="hero-description">
                        BukSkwela is your all-in-one enrollment repository—securely collect, track, <br> and manage student requirements with ease.
                        </p>
                        <a href="{{ url('/register') }}" class="btn-get-started">Let's Get Started!</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Showcase Section -->
        <section id="showcase" class="showcase">
            <div class="container">
                <div class="showcase-wrapper" data-aos="fade-up" data-aos-duration="1000">
                    <div class="browser-mockup">
                        <div class="browser-header">
                            <div class="browser-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            <div class="browser-address">
                                <span>go.bukskwela.com</span>
                            </div>
                            <div class="browser-actions">
                                <span><i class="bi bi-download"></i></span>
                                <span><i class="bi bi-plus"></i></span>
                                <span><i class="bi bi-grid-3x3-gap"></i></span>
                            </div>
                        </div>
                        <div class="browser-content">
                            <img src="{{ asset('assets/images/showcase.jpg') }}" alt="BukSkwela Dashboard" class="img-fluid">
                        </div>
                    </div> 
                    
                    <div class="feature-callout left-callout" data-aos="fade-right" data-aos-delay="300">
                        <div class="callout-content">
                            <h4>Student Records</h4>
                            <p>Track submission status at a glance</p>
                        </div>
                    </div>
                    
                    <div class="feature-callout right-callout" data-aos="fade-left" data-aos-delay="500">
                        <div class="callout-content">
                            <h4>Customize Your Dashboard</h4>
                            <p>Tailor the system to your department's needs</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section id="features" class="features section">
            <div class="container">
                <div class="section-title text-center" data-aos="fade-up">
                    <h2>What can you do with BukSkwela?</h2>
                </div>

                <div class="row">
                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-people"></i></div>
                            <h4>Instructor & Student Management</h4>
                            <p>Easily manage student and instructor profiles.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-file-earmark-check"></i></div>
                            <h4>Enrollment Requirement Tracking</h4>
                            <p>Monitor document submission status in real-time.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="300">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-folder2-open"></i></div>
                            <h4>Digital Document Storage</h4>
                            <p>Securely store and organize all enrollment documents in one centralized digital repository.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="400">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-clipboard-data"></i></div>
                            <h4>Probationary Status Management</h4>
                            <p>Track and manage students on academic probation.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="500">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-graph-up"></i></div>
                            <h4>Submission Progress Reports</h4>
                            <p>Generate  reports on document submissions.</p>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="600">
                        <div class="feature-box">
                            <div class="icon"><i class="bi bi-palette"></i></div>
                            <h4>Customizable System Branding</h4>
                            <p>Tailor the system's appearance to match your department's branding.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="pricing section">
            <div class="container">
                <div class="section-title text-center" data-aos="fade-up">
                    <h2>Choose the Right Plan for You</h2>
                </div>

                <div class="row justify-content-center gy-4">
                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                        <div class="pricing-card">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4 d-flex flex-column">
                                    <h4 class="text-center mb-3">Basic</h4>
                                    <h2 class="price text-center mb-3" id="basic-price">₱0</h2>
                                    <div class="price-subtitle text-center text-muted mb-3">/ month</div>
                                    
                                    <div class="features mt-3 mb-4">
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Instructor Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Student Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Enrollment Document Upload</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Email Notification</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>View Required Documents</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ url('/register') }}" class="btn btn-order w-100 py-2" style="background-color: #001c38; color: white;">Start for Free</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                        <div class="pricing-card highlighted">
                            <div class="card shadow border-0 h-100" style="background-color: #001c38; color: white;">
                                <div class="card-body p-4 d-flex flex-column" style="background-color: #001c38;">
                                    <h4 class="text-center mb-3">Premium</h4>
                                    <h2 class="price text-center mb-3" id="premium-price">₱5,000</h2>
                                    <div class="price-subtitle text-center text-muted mb-3" style="color: rgba(255, 255, 255, 0.7) !important;">/ month per department</div>
                                    
                                    <div class="features mt-3 mb-4" style="background-color: #001c38;">
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Instructor Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Student Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Enrollment Document Upload</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Email Notification</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>View Required Documents</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>View Student Submission Status</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Probationary Status Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill me-2" style="color: #ffbd59;"></i>
                                            <span>Custom Enrollment Requirements</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ url('/register') }}" class="btn btn-order w-100 py-2" style="background-color: #ffbd59; color: #001c38; font-weight: 600;">Upgrade to Premium</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                        <div class="pricing-card">
                            <div class="card shadow-sm border-0 h-100">
                                <div class="card-body p-4 d-flex flex-column">
                                    <h4 class="text-center mb-3">Ultimate</h4>
                                    <h2 class="price text-center mb-3" id="ultimate-price">₱8,000</h2>
                                    <div class="price-subtitle text-center text-muted mb-3">/ month per department</div>
                                    
                                    <div class="features mt-3 mb-4">
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Instructor Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Student Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Enrollment Document Upload</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Email Notification</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>View Required Documents</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>View Student Submission Status</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Probationary Status Management</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Custom Enrollment Requirements</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>View Uploaded Documents</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Submission Reports</span>
                                        </div>
                                        <div class="feature-item d-flex align-items-center mb-3">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>Branding Customization</span>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-auto">
                                        <a href="{{ url('/register') }}" class="btn btn-order w-100 py-2" style="background-color: #001c38; color: white;">Get Ultimate</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="footer" class="footer">
            <div class="container">
                <div class="row gy-4">
                    <div class="col-lg-5 col-md-6 footer-about">
                        <a href="{{ url('/') }}" class="d-flex align-items-center">
                            <img src="{{ asset('assets/images/logo-white.png') }}" alt="BukSkwela Logo" style="height: 40px;">
                        </a>
                        <div class="footer-contact pt-3">
                            <p>Fortich Street</p>
                            <p>Malaybalay City, Philippines 8700</p>
                            <p class="mt-3"><strong>Phone:</strong> <span>+63 991 848 6198</span></p>
                            <p><strong>Email:</strong> <span>2001103588@student.buksu.edu.ph</span></p>
                        </div>
                    </div>

                    <div class="col-lg-3 col-md-6 footer-links">
                        <h4 class="footer-title">Useful Links</h4>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-chevron-right"></i> <a href="#hero">Home</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#about">About us</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#features">Features</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#pricing">Pricing</a></li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-6 footer-links">
                        <h4 class="footer-title">Our Services</h4>
                        <ul class="list-unstyled">
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Instructor & Student Management</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Enrollment Requirement Tracking</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Digital Document Storage</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Probationary Status Management</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Submission Progress Reports</a></li>
                            <li><i class="bi bi-chevron-right"></i> <a href="#">Customizable System Branding</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="container mt-4 text-center">
                <div class="copyright">
                    &copy; Copyright <strong><span>BukSkwela</span></strong>. All Rights Reserved
                </div>
            </div>
        </footer>

        <!-- Scripts -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize AOS
                AOS.init({
                    duration: 800,
                    easing: 'ease-in-out',
                    once: true,
                    mirror: false
                });

                // Add custom styling for Ultimate plan
                document.head.insertAdjacentHTML('beforeend', `
                    <style>
                        .pricing-item.ultimate {
                            border-color: #FF8C00;
                            background-color: rgba(255, 140, 0, 0.03);
                        }
                        .pricing-item.ultimate .btn-buy {
                            background-color: #FF8C00;
                        }
                        .pricing-item.ultimate .btn-buy:hover {
                            background-color: #E67300;
                        }
                        .pricing-item h4 {
                            display: flex;
                            align-items: baseline;
                            justify-content: center;
                            flex-wrap: nowrap;
                        }
                    </style>
                `);

                // Dropdown menu functionality
                const dropdowns = document.querySelectorAll('.dropdown');
                dropdowns.forEach(dropdown => {
                    dropdown.addEventListener('click', function(e) {
                        this.querySelector('ul').classList.toggle('show');
                        e.stopPropagation();
                    });
                });

                // Close dropdowns when clicking outside
                document.addEventListener('click', function() {
                    dropdowns.forEach(dropdown => {
                        dropdown.querySelector('ul').classList.remove('show');
                    });
                });

                // Smooth scrolling for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function(e) {
                        e.preventDefault();
                        document.querySelector(this.getAttribute('href')).scrollIntoView({
                            behavior: 'smooth'
                        });
                    });
                });
            });
        </script>

        <style>
            .pricing-card.highlighted .card,
            .pricing-card.highlighted .card-body,
            .pricing-card.highlighted .features,
            .pricing-card.highlighted .feature-item {
                background-color: #001c38 !important;
            }
            
            .pricing-card.highlighted h4,
            .pricing-card.highlighted h2,
            .pricing-card.highlighted .feature-item span {
                color: white;
            }
            
            .pricing-card.highlighted .price-subtitle {
                color: rgba(255, 255, 255, 0.7) !important;
            }
            
            .pricing-card.highlighted .bi-check-circle-fill {
                color: #ffbd59 !important;
            }
            
            .pricing-card.highlighted .btn-order {
                background-color: #ffbd59;
                color: #001c38;
                font-weight: 600;
            }
            
            .pricing-card.highlighted .btn-order:hover {
                background-color: #ffa726;
            }
            
            .pricing-card {
                transition: all 0.3s ease;
                height: 100%;
            }
            
            .pricing-card:hover {
                transform: translateY(-10px);
            }
            
            .pricing-card.highlighted {
                position: relative;
            }
            
            .price {
                font-size: 2.5rem;
                font-weight: 700;
                color: #333;
            }
            
            .btn-order {
                background-color: #6c63ff;
                color: white;
                border: none;
                font-weight: 500;
                transition: all 0.3s ease;
            }
            
            .btn-order:hover {
                background-color: #5a52e5;
                color: white;
            }
            
            .features {
                font-size: 0.9rem;
            }
            
            .feature-item i {
                font-size: 1rem;
            }
        </style>
    </body>
</html>