<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{env('APP_NAME')}} | Login</title>

    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Work Sans font -->
    <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom Login CSS -->
    <link href="{{ asset('assets/css/pages/login.css') }}" rel="stylesheet">
    <style>
        .input-icon-wrapper {
            position: absolute;
            left: 10px;
            height: 100%;
            display: flex;
            align-items: center;
            z-index: 10;
        }
        .input-icon {
            color: #6c757d;
        }
        .fa-envelope.input-icon {
            margin-bottom: 20px;
        }
        .form-control {
            padding-left: 35px;
            position: relative;
        }
        .position-relative {
            margin-top: 5px;
        }
        /* Modal styling */
        .modal-content {
            border-radius: 10px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }
        .modal-footer {
            border-top: none;
        }
        .text-primary {
            color: #001c38 !important;
        }
        #tenantSubdomainModal .alert-info {
            background-color: #f5f9ff;
            border-color: #d9e8ff;
            color: #0055cc;
            border-radius: 8px;
        }
        #tenantSubdomainModal .btn-secondary {
            background-color: #001c38;
            border-color: #001c38;
            color: white;
        }
        #tenantSubdomainModal .btn-secondary:hover {
            background-color: #00274f;
        }
    </style>
</head>

<body>
    <div class="split-layout">
        <!-- Form Side -->
        <div class="form-side">
            <div class="auth-form-light p-5">
                <div class="form-header-logo">
                    <img src="{{ asset('assets/images/logo.png') }}" alt="{{env('APP_NAME')}}">
                </div>

                <h4 class="mb-2">Welcome back!</h4>
                <p class="text-muted mb-4">
                    Log in to continue to your workspace
                    <span id="tenant-display" class="d-none font-weight-bold"></span>
                </p>

                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @error('email')
                    @if(strpos($message, 'You are not authorized to login through the central system') !== false)
                        <div id="unauthorizedAlert" class="alert alert-warning">
                            Please use your tenant subdomain to login. <a href="#" data-toggle="modal" data-target="#tenantSubdomainModal">Learn more</a>
                        </div>
                    @elseif(strpos($message, 'Invalid credentials for this tenant') !== false)
                        <div id="invalidTenantAlert" class="alert alert-warning">
                            Your tenant credentials are invalid. <a href="#" data-toggle="modal" data-target="#invalidTenantModal">Learn more</a>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            {{ $message }}
                        </div>
                    @endif
                @enderror

                <form method="POST" action="{{ url('/login') }}" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper">
                                <i class="fas fa-envelope input-icon" style="margin-bottom: 20px"></i>
                            </div>
                            <input type="email" class="form-control" name="email" 
                                placeholder="Enter your email" required id="emailInput">
                            <small class="form-text text-muted">For students, use your @student.buksu.edu.ph email </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper">
                                <i class="fas fa-lock input-icon"></i>
                            </div>
                            <input type="password" class="form-control" name="password" 
                                placeholder="Enter your password" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="remember-me">
                                <input type="checkbox" name="remember" id="remember">
                                <label for="remember">Remember Me</label>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-block btn-primary btn-lg auth-form-btn" style="color: white;">
                            Login
                        </button>

                        <div class="text-center mt-3">
                            <small>
                                Don't have an account? 
                                <a href="{{ url('/register') }}" class="font-weight-medium login-link">Create one</a>
                            </small>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Image Side -->
        <div class="image-side">
            <div class="image-overlay">
                <h2>Welcome to BukSkwela!</h2>
                <p class="lead">Less paper, less hassle—manage documents with ease, whether you're a student or instructor.</p>
                <p class="subtext">Sign in and take control of your documents today!</p>
            </div>
        </div>
    </div>

    <!-- Include the Tenant Approval Modal -->
    @include('Modals.TenantApproval')

    <!-- Tenant Subdomain Modal -->
    <div class="modal fade" id="tenantSubdomainModal" tabindex="-1" role="dialog" aria-labelledby="tenantSubdomainModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tenantSubdomainModalLabel">Login with Tenant Subdomain</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h4>Tenant-specific Login Required</h4>
                    </div>
                    
                    <p>Your account is associated with a specific tenant (school/department) in BukSkwela.</p>
                    <p>To log in, please use your tenant's specific subdomain, which should be in the format:</p>
                    
                    <div class="alert alert-info my-3">
                        <code><strong>http://yourtenant.localhost:8000/login</strong></code>
                    </div>
                    
                    <p>If you don't know your tenant subdomain, please contact your administrator.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invalid Tenant Credentials Modal -->
    <div class="modal fade" id="invalidTenantModal" tabindex="-1" role="dialog" aria-labelledby="invalidTenantModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invalidTenantModalLabel">Invalid Tenant Credentials</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <h4>Login Failed</h4>
                    </div>
                    
                    <p>We couldn't authenticate you with the provided credentials for this tenant.</p>
                    <p>There might be several reasons for this:</p>
                    
                    <ul class="mt-3">
                        <li>You may have entered an incorrect password</li>
                        <li>Your account may not be activated for this tenant</li>
                        <li>Your account might have been removed from this tenant</li>
                    </ul>
                    
                    <div class="alert alert-info my-3">
                        <p class="mb-0">Please try again with the correct credentials or contact your tenant administrator for assistance.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" style="background-color: #001c38; border-color: #001c38;">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    @if(session('show_approval_modal'))
    <script>
        $(document).ready(function() {
            $('#tenantApprovalModal').modal('show');
        });
    </script>
    @endif

    @error('email')
    @if(strpos($message, 'You are not authorized to login through the central system') !== false)
    <script>
        $(document).ready(function() {
            $('#tenantSubdomainModal').modal('show');
        });
    </script>
    @elseif(strpos($message, 'Invalid credentials for this tenant') !== false)
    <script>
        $(document).ready(function() {
            $('#invalidTenantModal').modal('show');
        });
    </script>
    @endif
    @enderror

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('emailInput');
            const tenantDisplay = document.getElementById('tenant-display');
            
            // Get current hostname to check if we're on a tenant subdomain
            const hostname = window.location.hostname;
            const isTenantSubdomain = hostname !== '127.0.0.1' && hostname !== 'localhost' && !hostname.includes('bukskwela.com');
            
            // If on tenant subdomain, extract and display tenant name
            if (isTenantSubdomain) {
                const tenantName = hostname.split('.')[0];
                tenantDisplay.textContent = ` for ${tenantName.toUpperCase()}`;
                tenantDisplay.classList.remove('d-none');
            }
            
            loginForm.addEventListener('submit', function(e) {
                const email = emailInput.value;
                
                // Get current hostname to check if we're on a tenant subdomain
                const hostname = window.location.hostname;
                const isTenantSubdomain = hostname !== '127.0.0.1' && hostname !== 'localhost' && !hostname.includes('bukskwela.com');
                
                if (email.endsWith('@student.buksu.edu.ph')) {
                    e.preventDefault();
                    // If on a tenant subdomain, use tenant-specific student login
                    if (isTenantSubdomain) {
                        loginForm.action = `${window.location.origin}/student/login`;
                    } else {
                        // Use central domain student login
                        loginForm.action = "{{ url('/student/login') }}";
                    }
                    loginForm.submit();
                }
                // Otherwise, let it submit to the default admin login
            });
        });
    </script>
</body>
</html>