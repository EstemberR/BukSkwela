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
        <!-- Add this style section in the head -->
        <style>
            :root {
                --navy-blue: #003366;
                --gold: #FFD700;
            }
            
            .modal-content {
                border: none;
                border-radius: 15px;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            }

            .modal-header {
                background-color: var(--navy-blue);
                color: var(--gold);
                border-radius: 15px 15px 0 0;
                padding: 1.5rem;
            }

            .modal-header .modal-title {
                font-size: 1.5rem;
                font-weight: 600;
            }

            .modal-header .close {
                color: var(--gold);
                opacity: 1;
            }

            .pricing-card {
                border: none;
                border-radius: 12px;
                transition: all 0.3s ease;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
                overflow: hidden;
            }

            .pricing-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
            }

            .pricing-card.selected {
                border: 2px solid var(--navy-blue);
            }

            .pricing-card .card-header {
                padding: 1.5rem;
                border: none;
            }

            #basic-card .card-header {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            }

            #premium-card .card-header {
                background: linear-gradient(135deg, var(--navy-blue), #004c99);
                position: relative;
                overflow: hidden;
            }

            .badge {
                position: absolute;
                top: 10px;
                right: -25px;
                transform: rotate(45deg);
                padding: 8px 30px;
                background-color: var(--gold);
                color: var(--navy-blue);
                font-weight: 600;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            }

            .pricing-card-title {
                font-size: 1.8rem;
                font-weight: 700;
                color: var(--navy-blue);
                margin-bottom: 0.8rem;
            }

            .pricing-card-title small {
                font-size: 0.9rem;
            }

            .pricing-card ul {
                padding: 0 1.2rem;
                margin-bottom: 1rem;
            }

            .pricing-card ul li {
                padding: 0.6rem 0;
                border-bottom: 1px solid rgba(0,0,0,0.05);
                display: flex;
                align-items: center;
                font-size: 12px;
                color: #555;
            }

            .pricing-card ul li i {
                margin-right: 8px;
                font-size: 12px;
            }

            .pricing-card ul li.na {
                color: #777;
            }

            .btn-outline-primary {
                color: var(--navy-blue);
                border-color: var(--navy-blue);
            }

            .btn-outline-primary:hover {
                background-color: var(--navy-blue);
                border-color: var(--navy-blue);
                color: var(--gold);
            }

            .btn-primary {
                background-color: var(--navy-blue);
                border-color: var(--navy-blue);
                color: var(--gold);
            }

            .btn-primary:hover {
                background-color: var(--gold);
                border-color: var(--gold);
                color: var(--navy-blue);
            }

            .modal-footer {
                border-top: 1px solid rgba(0,0,0,0.05);
                padding: 1.5rem;
            }

            .btn-secondary {
                background-color: #6c757d;
                border-color: #6c757d;
            }

            .select-plan {
                padding: 12px 24px;
                font-weight: 600;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .subscribe-button {
                cursor: pointer;
                position: relative;
                padding: 8px 20px;
                font-size: 14px;
                color: rgb(193, 163, 98);
                border: 2px solid rgb(193, 163, 98);
                border-radius: 34px;
                background-color: transparent;
                font-weight: 600;
                transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
                overflow: hidden;
                display: inline-block;
                text-decoration: none;
                line-height: 1.5;
            }

            .subscribe-button::before {
                content: '';
                position: absolute;
                inset: 0;
                margin: auto;
                width: 50px;
                height: 50px;
                border-radius: inherit;
                scale: 0;
                z-index: -1;
                background-color: rgb(193, 163, 98);
                transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
            }

            .subscribe-button:hover::before {
                scale: 3;
            }

            .subscribe-button:hover {
                color: #212121;
                scale: 1.1;
                box-shadow: 0 0px 20px rgba(193, 163, 98,0.4);
                text-decoration: none;
            }

            .subscribe-button:active {
                scale: 1;
            }

            .form-control {
                height: calc(1.5em + 0.75rem + 2px);
            }

            .pricing-card .card-body {
                font-size: 12px;
                padding: 1.25rem;
            }

            .pricing-card .card-header h5 {
                font-size: 1.25rem;
                margin: 0;
            }

            .input-group {
                height: 100%;
            }

            .input-group .form-control {
                height: 100%;
            }

            .input-icon-wrapper {
                height: 100%;
                display: flex;
                align-items: center;
            }

            /* Adjust the flex layout */
            .subscription-wrapper {
                display: flex;
                align-items: center;
                gap: 15px; /* Increased gap between elements */
            }

            .subscription-wrapper .flex-grow-1 {
                min-width: 0; /* Allow flex item to shrink below content size */
                flex: 1;
            }

            .subscription-wrapper .form-control {
                width: 100%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            /* Make the button width fixed */
            .subscribe-button {
                flex-shrink: 0; /* Prevent button from shrinking */
                white-space: nowrap; /* Keep button text in one line */
                min-width: max-content; /* Ensure button doesn't wrap */
            }

            .pricing-card .text-muted {
                font-size: 12px;
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
                            <div class="subscription-wrapper">
                                <input type="hidden" name="subscription_plan" id="selected_plan" value="basic">
                                <div class="flex-grow-1 position-relative">
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-tag input-icon"></i>
                                    </div>
                                    <input type="text" class="form-control" id="plan_display" value="Basic Plan (Free)" readonly>
                                </div>
                                <button type="button" class="subscribe-button" data-toggle="modal" data-target="#planModal">
                                     Premium <i class="fas fa-crown ml-2"></i>
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
                        <h5 class="modal-title" id="planModalLabel">
                            <i class="fas fa-crown mr-2" style="color: var(--gold);"></i>
                            Choose Your Subscription Plan
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body py-4">
                        <div class="row">
                            <!-- Basic Plan -->
                            <div class="col-md-6 mb-4 mb-md-0">
                                <div class="card h-100 pricing-card" id="basic-card">
                                    <div class="card-header text-center">
                                        <h5 class="my-0 font-weight-bold">Basic</h5>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h1 class="card-title pricing-card-title text-center">
                                            ₱0 <small class="text-muted">/ month</small>
                                        </h1>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><i class="fas fa-check text-success"></i> Instructor Management</li>
                                            <li><i class="fas fa-check text-success"></i> Student Management</li>
                                            <li><i class="fas fa-check text-success"></i> Enrollment Document Upload</li>
                                            <li><i class="fas fa-check text-success"></i> Email Notification</li>
                                            <li><i class="fas fa-check text-success"></i> View Required Documents</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> View Student Submission Status</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> Probationary Status Management</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> Custom Enrollment Requirements</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> View Uploaded Documents</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> Submission Reports</li>
                                            <li class="na"><i class="fas fa-times text-danger"></i> Branding Customization</li>
                                        </ul>
                                        <button type="button" class="btn btn-lg btn-block btn-outline-primary mt-auto select-plan" data-plan="basic" data-display="Basic Plan (Free)">
                                            <i class="fas fa-check-circle mr-2"></i>Select Basic
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Premium Plan -->
                            <div class="col-md-6">
                                <div class="card h-100 pricing-card" id="premium-card">
                                    <div class="card-header text-white text-center">
                                        <h5 class="my-0 font-weight-bold">Premium</h5>
                                        <span class="badge">POPULAR</span>
                                    </div>
                                    <div class="card-body d-flex flex-column">
                                        <h1 class="card-title pricing-card-title text-center">
                                            ₱5,000 <small class="text-muted">/ month</small>
                                        </h1>
                                        <p class="text-center text-muted mb-4">per department</p>
                                        <ul class="list-unstyled mt-3 mb-4">
                                            <li><i class="fas fa-check text-success"></i> Instructor Management</li>
                                            <li><i class="fas fa-check text-success"></i> Student Management</li>
                                            <li><i class="fas fa-check text-success"></i> Enrollment Document Upload</li>
                                            <li><i class="fas fa-check text-success"></i> Email Notification</li>
                                            <li><i class="fas fa-check text-success"></i> View Required Documents</li>
                                            <li><i class="fas fa-check text-success"></i> View Student Submission Status</li>
                                            <li><i class="fas fa-check text-success"></i> Probationary Status Management</li>
                                            <li><i class="fas fa-check text-success"></i> Custom Enrollment Requirements</li>
                                            <li><i class="fas fa-check text-success"></i> View Uploaded Documents</li>
                                            <li><i class="fas fa-check text-success"></i> Submission Reports</li>
                                            <li><i class="fas fa-check text-success"></i> Branding Customization</li>
                                        </ul>
                                        <button type="button" class="btn btn-lg btn-block btn-primary mt-auto select-plan" data-plan="premium" data-display="Premium Plan (₱5,000/month)">
                                            <i class="fas fa-crown mr-2"></i>Select Premium
                                        </button>
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
