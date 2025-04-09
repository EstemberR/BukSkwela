@extends('tenant.layouts.app')

@section('title', 'My Profile')

@section('styles')
<style>
/* Subscription Modal Styles */
.hover-shadow {
    transition: all 0.3s ease;
}

.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Match the dashboard modal styles */
.modal-dialog {
    max-width: 80%;
}

.nav-tabs .nav-link {
    color: #4b5563;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 0.75rem 1rem;
}

.nav-tabs .nav-link.active {
    color: rgb(3, 1, 43);
    border-bottom: 2px solid rgb(3, 1, 43);
    font-weight: 600;
}

.list-group-item {
    transition: all 0.2s ease;
}

.list-group-item:hover {
    background-color: rgba(3, 1, 43, 0.05);
}

/* Contact Support Modal Styles */
.rounded-circle {
    transition: all 0.3s ease;
}

.card:hover .rounded-circle {
    transform: scale(1.1);
}

@media (max-width: 768px) {
    .modal-dialog {
        max-width: 95%;
        margin: 1rem auto;
    }
}

/* Subscription Plan Card Styles */
.plan-card {
  background: #fff;
  width: 15rem;
  padding-left: 2rem;
  padding-right: 2rem;
  padding-top: 10px;
  padding-bottom: 20px;
  border-radius: 10px;
  border-bottom: 4px solid #000446;
  box-shadow: 0 6px 30px rgba(207, 212, 222, 0.3);
  font-family: "Poppins", sans-serif;
  transition: transform 0.3s ease;
}

.plan-card:hover {
  transform: translateY(-10px);
}

.plan-card h2 {
  margin-bottom: 15px;
  font-size: 27px;
  font-weight: 600;
}

.plan-card h2 span {
  display: block;
  margin-top: -4px;
  color: #4d4d4d;
  font-size: 12px;
  font-weight: 400;
}

.etiquet-price {
  position: relative;
  background: #fdbd4a;
  width: 14.46rem;
  margin-left: -0.65rem;
  padding: .2rem 1.2rem;
  border-radius: 5px 0 0 5px;
}

.etiquet-price p {
  margin: 0;
  padding-top: .4rem;
  display: flex;
  font-size: 1.9rem;
  font-weight: 500;
}

.etiquet-price p:before {
  content: "$";
  margin-right: 5px;
  font-size: 15px;
  font-weight: 300;
}

.etiquet-price p:after {
  content: "/ account";
  margin-left: 5px;
  font-size: 15px;
  font-weight: 300;
}

.etiquet-price div {
  position: absolute;
  bottom: -23px;
  right: 0px;
  width: 0;
  height: 0;
  border-top: 13px solid #c58102;
  border-bottom: 10px solid transparent;
  border-right: 13px solid transparent;
  z-index: -6;
}

.benefits-list {
  margin-top: 16px;
}

.benefits-list ul {
  padding: 0;
  font-size: 14px;
}

.benefits-list ul li {
  color: #4d4d4d;
  list-style: none;
  margin-bottom: .2rem;
  display: flex;
  align-items: center;
  gap: .5rem;
}

.benefits-list ul li svg {
  width: .9rem;
  fill: currentColor;
}

.benefits-list ul li span {
  font-weight: 300;
}

.button-get-plan {
  display: flex;
  justify-content: center;
  margin-top: 1.2rem;
}

.button-get-plan a {
  display: flex;
  justify-content: center;
  align-items: center;
  background: #000446;
  color: #fff;
  padding: 10px 15px;
  border-radius: 5px;
  text-decoration: none;
  font-size: .8rem;
  letter-spacing: .05rem;
  font-weight: 500;
  transition: all 0.3s ease;
}

.button-get-plan a:hover {
  transform: translateY(-3%);
  box-shadow: 0 3px 10px rgba(207, 212, 222, 0.9);
}

.button-get-plan .svg-rocket {
  margin-right: 10px;
  width: .9rem;
  fill: currentColor;
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>My Profile</h5>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <!-- Left side - Profile Image -->
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <div class="profile-img-container mb-3">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" class="img-fluid rounded-circle profile-img" style="width: 150px; height: 150px; object-fit: cover;">
                                @else
                                    <div class="avatar-placeholder rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 150px; height: 150px;">
                                        <span class="text-white fs-1">{{ substr($user->name, 0, 1) }}</span>
                                    </div>
                                @endif
                            </div>
                            
                            <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                            <p class="text-muted">{{ $user->email }}</p>
                            
                            <div class="mt-3">
                                <span class="badge bg-{{ $user->status == 'active' ? 'success' : 'warning' }} rounded-pill px-3 py-2">
                                    {{ ucfirst($user->status) }}
                                </span>
                                
                                <!-- Check if premium -->
                                @php
                                    // Get the tenant's subscription plan - basic users can't edit profile
                                    // Add null check to prevent "Attempt to read property on null" error
                                    $tenantData = tenant();
                                    $isPremium = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
                                @endphp
                                
                                <div class="mt-2">
                                    <span class="badge bg-{{ $isPremium ? 'info' : 'secondary' }} rounded-pill px-3 py-2">
                                        {{ $isPremium ? 'Premium' : 'Basic' }}
                                    </span>
                                </div>
                                
                                @if(!$isPremium)
                                <div class="mt-3">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                        <i class="fas fa-crown me-1"></i> Upgrade to Premium
                                    </button>
                                </div>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Right side - Profile Form -->
                        <div class="col-md-9">
                            <ul class="nav nav-tabs" id="profileTabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="true">
                                        <i class="fas fa-user me-2"></i>Profile Information
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="password-tab" data-bs-toggle="tab" data-bs-target="#password-tab-pane" type="button" role="tab" aria-controls="password-tab-pane" aria-selected="false">
                                        <i class="fas fa-key me-2"></i>Change Password
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="profileTabsContent">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane fade show active" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    @php
                                        // Use the same isPremium variable from above, no need to recompute
                                    @endphp
                                    
                                    @if(!$isPremium)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Your account is on the basic plan. <strong>Upgrade to premium</strong> to edit your profile information.
                                    </div>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('profile.update', ['tenant' => tenant('id')]) }}" enctype="multipart/form-data" id="profileForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                                value="{{ old('name', $user->name) }}" required {{ !$isPremium ? 'readonly' : '' }}>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                                value="{{ old('email', $user->email) }}" required {{ !$isPremium ? 'readonly' : '' }}>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" 
                                                {{ !$isPremium ? 'disabled' : '' }}>
                                            <div class="form-text">Upload a square image for best results. Maximum size: 2MB</div>
                                            @error('avatar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" {{ !$isPremium ? 'disabled' : '' }} id="updateProfileBtn">
                                                <i class="fas fa-save me-2"></i>Update Profile
                                            </button>
                                            
                                            @if(!$isPremium)
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                                <i class="fas fa-crown me-2"></i>Upgrade to Edit
                                            </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                    @if(!$isPremium)
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Your account is on the basic plan. <strong>Upgrade to premium</strong> to change your password.
                                    </div>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('profile.password', ['tenant' => tenant('id')]) }}" id="passwordForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                id="current_password" name="current_password" required {{ !$isPremium ? 'readonly' : '' }}>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" required {{ !$isPremium ? 'readonly' : '' }}>
                                            <div class="form-text">Password must be at least 8 characters</div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" 
                                                name="password_confirmation" required {{ !$isPremium ? 'readonly' : '' }}>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" {{ !$isPremium ? 'disabled' : '' }} id="updatePasswordBtn">
                                                <i class="fas fa-key me-2"></i>Update Password
                                            </button>
                                            
                                            @if(!$isPremium)
                                            <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                                <i class="fas fa-crown me-2"></i>Upgrade to Edit
                                            </button>
                                            @endif
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Subscription Modal -->
<div class="modal fade" id="subscriptionModal" tabindex="-1" aria-labelledby="subscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="subscriptionModalLabel">
                    <i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row justify-content-center">
                    <div class="col-md-10 text-center mb-4">
                        <h4 class="fw-bold">Unlock Premium Features</h4>
                        <p class="text-muted">Upgrade your account to access all premium features including profile editing and password management.</p>
                    </div>
                </div>
                
                <div class="row gx-4">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-4 hover-shadow">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-primary"><i class="fas fa-check-circle me-2"></i>Basic Plan</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="badge bg-secondary px-3 py-2 mb-3">Current Plan</div>
                                    <h2 class="display-6 fw-bold mb-0">Free</h2>
                                    <p class="text-muted">Limited access</p>
                                </div>
                                
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> View profile information
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> Access dashboard
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-times text-danger me-2"></i> Edit profile information
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-times text-danger me-2"></i> Change password
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-times text-danger me-2"></i> Premium support
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm mb-4 hover-shadow h-100" style="border-left: 4px solid #fdbd4a !important;">
                            <div class="card-header bg-white py-3">
                                <h5 class="mb-0 text-warning"><i class="fas fa-crown me-2"></i>Premium Plan</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-4">
                                    <div class="badge bg-warning text-dark px-3 py-2 mb-3">Recommended</div>
                                    <h2 class="display-6 fw-bold mb-0">$254.99</h2>
                                    <p class="text-muted">Full access</p>
                                </div>
                                
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> View profile information
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> Access dashboard
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> Edit profile information
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> Change password
                                    </li>
                                    <li class="list-group-item border-0 px-0">
                                        <i class="fas fa-check text-success me-2"></i> Premium support
                                    </li>
                                </ul>
                            </div>
                            <div class="card-footer bg-white text-center py-3 border-0">
                                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#contactSupportModal" data-bs-dismiss="modal">
                                    <i class="fas fa-rocket me-2"></i>Upgrade Now
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Contact Support Modal -->
<div class="modal fade" id="contactSupportModal" tabindex="-1" aria-labelledby="contactSupportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="contactSupportModalLabel">
                    <i class="fas fa-headset me-2"></i>Contact Support
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-4">
                    <i class="fas fa-crown text-warning fa-3x mb-3"></i>
                    <h4>Ready to Upgrade?</h4>
                    <p class="text-muted">Contact our support team to upgrade your account to Premium.</p>
                </div>
                
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3">
                                <i class="fas fa-envelope text-primary"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Email Support</h6>
                                <p class="mb-0 small text-muted">support@bukskwela.com</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center mb-3">
                            <div class="rounded-circle p-2 bg-success bg-opacity-10 me-3">
                                <i class="fas fa-phone-alt text-success"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Phone Support</h6>
                                <p class="mb-0 small text-muted">+1 (234) 567-8900</p>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle p-2 bg-info bg-opacity-10 me-3">
                                <i class="fas fa-comment text-info"></i>
                            </div>
                            <div>
                                <h6 class="mb-1">Live Chat</h6>
                                <p class="mb-0 small text-muted">Available 24/7</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <form>
                    <div class="mb-3">
                        <label for="fullName" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullName" value="{{ $user->name }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" value="{{ $user->email }}" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="3" placeholder="I'd like to upgrade to the Premium plan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="sendSupportRequest">
                    <i class="fas fa-paper-plane me-2"></i>Send Request
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Preview uploaded image
    document.getElementById('avatar')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const profileImg = document.querySelector('.profile-img');
                const avatarPlaceholder = document.querySelector('.avatar-placeholder');
                
                if (profileImg) {
                    profileImg.src = e.target.result;
                } else if (avatarPlaceholder) {
                    // Replace placeholder with actual image
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-fluid rounded-circle profile-img';
                    img.style = 'width: 150px; height: 150px; object-fit: cover;';
                    
                    avatarPlaceholder.parentNode.replaceChild(img, avatarPlaceholder);
                }
            };
            reader.readAsDataURL(file);
        }
    });
    
    // For non-premium users, show subscription modal when trying to interact with disabled fields
    document.addEventListener('DOMContentLoaded', function() {
        // Check if premium with proper null checks
        @php
            $tenantData = tenant();
            $isPremiumJs = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
        @endphp
        const isPremium = {{ $isPremiumJs ? 'true' : 'false' }};
        
        if (!isPremium) {
            // Add listeners to form fields in profile tab
            const profileInputs = document.querySelectorAll('#profile-tab-pane input');
            profileInputs.forEach(input => {
                input.addEventListener('click', function(e) {
                    if (this.hasAttribute('readonly') || this.hasAttribute('disabled')) {
                        e.preventDefault();
                        $('#subscriptionModal').modal('show');
                    }
                });
            });
            
            // Add listeners to form fields in password tab
            const passwordInputs = document.querySelectorAll('#password-tab-pane input');
            passwordInputs.forEach(input => {
                input.addEventListener('click', function(e) {
                    if (this.hasAttribute('readonly') || this.hasAttribute('disabled')) {
                        e.preventDefault();
                        $('#subscriptionModal').modal('show');
                    }
                });
            });
            
            // Add listeners to disabled buttons
            document.getElementById('updateProfileBtn')?.addEventListener('click', function(e) {
                if (this.hasAttribute('disabled')) {
                    e.preventDefault();
                    $('#subscriptionModal').modal('show');
                }
            });
            
            document.getElementById('updatePasswordBtn')?.addEventListener('click', function(e) {
                if (this.hasAttribute('disabled')) {
                    e.preventDefault();
                    $('#subscriptionModal').modal('show');
                }
            });
            
            // Prevent form submissions for non-premium users
            document.getElementById('profileForm')?.addEventListener('submit', function(e) {
                if (!isPremium) {
                    e.preventDefault();
                    $('#subscriptionModal').modal('show');
                }
            });
            
            document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
                if (!isPremium) {
                    e.preventDefault();
                    $('#subscriptionModal').modal('show');
                }
            });
        }
    });
    
    // Handle support form submission
    document.getElementById('sendSupportRequest')?.addEventListener('click', function() {
        // Get the message
        const message = document.getElementById('message').value.trim();
        
        if (!message) {
            alert('Please enter a message for the support team.');
            return;
        }
        
        // Show success message within the modal
        const modal = document.getElementById('contactSupportModal');
        const modalBody = modal.querySelector('.modal-body');
        const modalFooter = modal.querySelector('.modal-footer');
        
        modalBody.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                <h4>Request Sent Successfully!</h4>
                <p class="text-muted mb-0">Our team will contact you shortly about upgrading your account.</p>
            </div>
        `;
        
        modalFooter.innerHTML = `
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        `;
        
        // You would typically send an AJAX request to the server here
    });
</script>
@endsection
