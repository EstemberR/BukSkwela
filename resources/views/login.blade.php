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
                <p class="text-muted mb-4">Log in to continue to your workspace</p>

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
                const email = emailInput.value;
                
                if (email.endsWith('@student.buksu.edu.ph')) {
                    e.preventDefault();
                    // Update form action for student login
                    loginForm.action = "{{ url('/student/login') }}";
                    loginForm.submit();
                }
                // Otherwise, let it submit to the default admin login
            });
        });
    </script>
</body>
</html>