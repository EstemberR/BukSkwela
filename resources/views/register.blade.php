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

                        <div class="alert alert-info">
                            <small><i class="fas fa-info-circle"></i> A secure password will be automatically generated and sent to your email once you register.</small>
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

                        <div class="form-group">
                            <label>Select Subscription Plan</label>
                            <div class="d-flex">
                                <input type="hidden" name="subscription_plan" id="selected_plan" value="basic">
                                <div class="flex-grow-1 position-relative">
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-tag input-icon"></i>
                                    </div>
                                    <input type="text" class="form-control" id="plan_display" value="Basic Plan (Free)" readonly>
                                </div>
                                <button type="button" class="btn btn-info ml-2" data-toggle="modal" data-target="#planModal">
                                    <i class="fas fa-list-ul"></i> View Plans
                                </button>
                            </div>
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

        <!-- Subscription Plan Modal -->
        <div class="modal fade" id="planModal" tabindex="-1" role="dialog" aria-labelledby="planModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="planModalLabel">Choose Your Subscription Plan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Basic Plan -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100 pricing-card" id="basic-card">
                                    <div class="card-header bg-light text-center">
                                        <h5 class="my-0 font-weight-bold">Basic</h5>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h1 class="card-title pricing-card-title text-center">$0 <small class="text-muted">/ mo</small></h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><i class="fas fa-check text-success mr-2"></i> Up to 50 students</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> 5GB storage</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> Email support</li>
                                            <li><i class="fas fa-times text-danger mr-2"></i> Advanced reporting</li>
                                            <li><i class="fas fa-times text-danger mr-2"></i> Custom branding</li>
                                        </ul>
                                        <button type="button" class="btn btn-lg btn-block btn-outline-primary mt-auto select-plan" data-plan="basic" data-display="Basic Plan (Free)">Select</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Premium Plan -->
                            <div class="col-md-4 mb-3 mb-md-0">
                                <div class="card h-100 pricing-card" id="premium-card">
                                    <div class="card-header bg-primary text-white text-center">
                                        <h5 class="my-0 font-weight-bold">Premium</h5>
                                        <span class="badge badge-light">POPULAR</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h1 class="card-title pricing-card-title text-center">$29 <small class="text-muted">/ mo</small></h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><i class="fas fa-check text-success mr-2"></i> Up to 500 students</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> 25GB storage</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> Priority support</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> Advanced reporting</li>
                                            <li><i class="fas fa-times text-danger mr-2"></i> Custom branding</li>
                                        </ul>
                                        <button type="button" class="btn btn-lg btn-block btn-primary mt-auto select-plan" data-plan="premium" data-display="Premium Plan ($29/mo)">Select</button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Enterprise Plan -->
                            <div class="col-md-4">
                                <div class="card h-100 pricing-card" id="enterprise-card">
                                    <div class="card-header bg-dark text-white text-center">
                                        <h5 class="my-0 font-weight-bold">Enterprise</h5>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h1 class="card-title pricing-card-title text-center">$49 <small class="text-muted">/ mo</small></h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><i class="fas fa-check text-success mr-2"></i> Unlimited students</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> 100GB storage</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> 24/7 support</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> Advanced reporting</li>
                                            <li><i class="fas fa-check text-success mr-2"></i> Custom branding</li>
                                        </ul>
                                        <button type="button" class="btn btn-lg btn-block btn-dark mt-auto select-plan" data-plan="enterprise" data-display="Enterprise Plan ($49/mo)">Select</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    </div>
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
                
                // Plan selection
                $('.select-plan').click(function() {
                    const plan = $(this).data('plan');
                    const displayText = $(this).data('display');
                    
                    $('#selected_plan').val(plan);
                    $('#plan_display').val(displayText);
                    
                    // Highlight selected card
                    $('.pricing-card').removeClass('border-primary');
                    $(`#${plan}-card`).addClass('border-primary');
                    
                    $('#planModal').modal('hide');
                });
            });
        </script>
    </body>
    </html>
