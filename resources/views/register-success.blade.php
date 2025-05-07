<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>{{env('APP_NAME')}} | Registration Pending</title>

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

                <h4 class="mb-2 text-center">Registration Successful!</h4>
                <p class="text-muted text-center mb-4">Thank you for registering with {{env('APP_NAME')}}</p>

                <div class="pending-status-container text-center mb-4">
                    <i class="fas fa-paper-plane fa-4x mb-3" style="color: #001c38;"></i>
                    <p class="lead">Your registration is being processed</p>
                </div>

                <div class="review-info-box p-4 mb-4 bg-light rounded">
                    <h5 class="font-weight-medium mb-3">Next Steps:</h5>
                    <div class="feature-list">
                        <div class="d-flex align-items-center mb-3">
                            <i class="fas fa-envelope mr-3" style="color: #001c38;"></i>
                            <span>Check your email for updates</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock mr-3" style="color: #001c38;"></i>
                            <span>Processing time: 24-48 hours</span>
                        </div>
                    </div>
                </div>

                <div class="mt-4">
                    <a href="{{ url('/') }}" class="btn btn-block btn-primary btn-lg auth-form-btn">
                        <i class="fas fa-home mr-2"></i> Return to Landing Page
                    </a>
                </div>

            </div>  
        </div>

        <!-- Image Side -->
        <div class="image-side">
            <div class="image-overlay d-flex flex-column justify-content-center align-items-center text-center" style="height: 100%;">
                <h2>We'll Keep You Updated!</h2>
                <p class="lead">Thank you for taking the first step towards efficient document management.</p>
                <p class="subtext">We'll notify you once your registration is approved!</p>
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