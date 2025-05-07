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
        /* Custom style to adjust icon position */
        .input-icon-wrapper {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 4;
        }
        
        /* Adjust padding for input fields to account for icon */
        .form-control {
            padding-left: 40px;
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
                <p class="text-muted mb-4"><span class="text-white">Log in</span> to continue to your workspace</p>

                <!-- Remove the inline alert and only use modal -->
                @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif

                @error('email')
                    <div class="alert alert-danger">
                        {{ $message }}
                    </div>
                @enderror

                <form method="POST" action="{{ url('/login') }}" id="loginForm">
                    @csrf
                    <div class="form-group">
                        <label>Email</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper" style="margin-top: -8px; left: 12px;">
                                <i class="fas fa-envelope input-icon"></i>
                            </div>
                            <input type="email" class="form-control" name="email" 
                                placeholder="Enter your email" required id="emailInput">
                            <small class="form-text text-muted">For students, use your @student.buksu.edu.ph email </small>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Password</label>
                        <div class="position-relative">
                            <div class="input-icon-wrapper" style="margin-top: 2px; left: 12px;">
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
                        <button type="submit" class="btn btn-block btn-primary btn-lg auth-form-btn">
                            <span class="text-white">Login</span>
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

    <!-- Change the modal to not show by default -->
    <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center">
                    <div style="font-size: 50px; color: #dc3545; margin-bottom: 20px;">
                        <i class="fas fa-exclamation-circle"></i>
                    </div>
                    <div style="font-size: 24px; font-weight: 600; color: #001c38; margin-bottom: 15px;">Authorization Error</div>
                    <div style="font-size: 18px; color: #6c757d; margin-bottom: 20px;">
                        You are not authorized to login through the central system. Please use your tenant subdomain.
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <a href="{{ url('/') }}" class="btn btn-primary" style="color: white;">Return to Home</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Include the Tenant Approval Modal -->
    @include('Modals.TenantApproval')

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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const loginForm = document.getElementById('loginForm');
            const emailInput = document.getElementById('emailInput');
            
            loginForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const email = emailInput.value;
                
                // Determine login type based on email domain
                if (email.endsWith('@student.buksu.edu.ph')) {
                    // Student login
                    loginForm.action = "{{ url('/student/login') }}";
                } else {
                    // For instructors and other staff, use the standard login endpoint
                    // The backend will determine the appropriate role and redirect accordingly
                    loginForm.action = "{{ url('/login') }}";
                }
                
                // Show loading state
                const submitBtn = loginForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Logging in...';
                
                // Submit the form
                loginForm.submit();
            });
            
            // Configure the error modal to be static but only show when validation fails
            $('#errorModal').modal({
                backdrop: 'static',
                keyboard: false,
                show: false // Don't show by default
            });
            
            // Show the modal only if there's an unauthorized error flag in the session
            @if(session('unauthorized_tenant_error'))
                $('#errorModal').modal('show');
            @endif

            @if(session('invalid_credentials'))
                $(document).ready(function() {
                    $('#invalidCredentialsModal').modal({
                        backdrop: 'static',
                        keyboard: false
                    });
                    $('#invalidCredentialsModal').modal('show');
                });
            @endif
        });
    </script>
</body>
</html>