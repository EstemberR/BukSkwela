    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <title>{{env('APP_NAME')}} | Create Account</title>

        <!-- Bootstrap 4 -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
        
        <!-- Add these new stylesheets -->
        <link rel="stylesheet" href="../../vendors/feather/feather.css">
        <link rel="stylesheet" href="../../vendors/ti-icons/css/themify-icons.css">
        <link rel="stylesheet" href="../../vendors/css/vendor.bundle.base.css">
        <link rel="stylesheet" href="../../css/vertical-layout-light/style.css">
        
        <!-- Add Google Fonts - Work Sans -->
        <link href="https://fonts.googleapis.com/css2?family=Work+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <!-- Custom Register CSS -->
        <link href="{{ asset('assets/css/pages/register.css') }}" rel="stylesheet">
    </head>

    <body>
        <div class="split-layout">
            <!-- Form Side -->
            <div class="form-side">
                <div class="auth-form-light p-5">
                    <div class="form-header-logo">
                        <img src="{{ asset('assets/images/logo.png') }}" alt="{{env('APP_NAME')}}">
                    </div>

                    <h4 class="mb-2">Create your account</h4>
                    <p class="text-muted mb-4">Start managing student enrollment documents effortlessly</p>

                    @if(session('success'))
                        <div class="alert alert-success">
                            {!! session('success') !!}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger">
                            {!! session('error') !!}
                        </div>
                    @endif

                    <form method="post" action="{{ route('register.save') }}">
                        @csrf

                        <div class="form-group">
                            <label>Department Name</label>
                            <div class="position-relative">
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-building input-icon"></i>
                                </div>
                                <input type="text" class="form-control" name="name" 
                                    placeholder="Enter department name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Admin Name</label>
                            <div class="position-relative">
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-user input-icon"></i>
                                </div>
                                <input type="text" class="form-control" name="admin_name" 
                                    placeholder="Enter admin name" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Admin Email</label>
                            <div class="position-relative">
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-envelope input-icon"></i>
                                </div>
                                <input type="email" class="form-control" name="admin_email" 
                                    placeholder="Enter admin email" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Password</label>
                            <div class="position-relative">
                                <div class="input-icon-wrapper">
                                    <i class="fas fa-lock input-icon"></i>
                                </div>
                                <input type="password" class="form-control" name="password" 
                                    placeholder="Enter password" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Choose your subdomain</label>
                            <div class="input-group">
                                <div class="position-relative flex-grow-1">
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-globe input-icon"></i>
                                    </div>
                                    <input type="text" class="form-control" name="subdomain" 
                                        placeholder="Enter subdomain name" required>
                                </div>
                                <div class="input-group-append">
                                    <span class="input-group-text">.{{ env('CENTRAL_DOMAIN') }}</span>
                                </div>
                            </div>
                            <small class="text-muted">Your unique URL: subdomain.{{ env('CENTRAL_DOMAIN') }}</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-block btn-primary btn-lg auth-form-btn">
                                Create My Department
                            </button>

                            <div class="text-center mt-2">
                                <small>
                                    Already have an account? <a href="{{ route('login') }}" class="login-link">Login</a>
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
                    <p class="lead">Transform your department's document management with our efficient platform.</p>
                    <ul class="feature-list">
                        <li><i class="fas fa-check-circle"></i> Real-time submission tracking</li>
                        <li><i class="fas fa-check-circle"></i> Easy instructor and student management</li>
                        <li><i class="fas fa-check-circle"></i> Customizable department branding</li>
                        <li><i class="fas fa-check-circle"></i> Automated report generation</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        <script>
            $(function () {
                $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    </body>
    </html>
