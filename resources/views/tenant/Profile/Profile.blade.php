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

/* Premium Badge Styles */
.premium-badge {
    display: inline-flex;
    align-items: center;
    color: rgb(193, 163, 98) !important;
    border: 1px solid rgb(193, 163, 98) !important;
    border-radius: 25px;
    background-color: transparent !important;
    padding: 4px 10px;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
    font-size: 0.75rem;
}

.premium-badge::before {
    content: '';
    position: absolute;
    inset: 0;
    margin: auto;
    width: 30px;
    height: 30px;
    border-radius: inherit;
    scale: 0;
    z-index: -1;
    background-color: rgb(193, 163, 98) !important;
    transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
}

.premium-badge i {
    margin-right: 0.3rem;
    font-size: 0.75rem;
    position: relative;
    z-index: 1;
    color: rgb(193, 163, 98) !important;
}

.premium-badge:hover {
    color: #212121 !important;
    scale: 1.1;
    box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
}

.premium-badge:hover i {
    color: #212121 !important;
}

.premium-badge:active {
    scale: 1;
}

.premium-badge:hover::before {
    scale: 3;
}

.premium-badge:hover span {
    color: #212121 !important;
}

/* Premium Button Styles */
.premium-button {
  cursor: pointer;
  position: relative;
  padding: 6px 16px;
  font-size: 14px;
  color: rgb(193, 163, 98) !important;
  border: 2px solid rgb(193, 163, 98) !important;
  border-radius: 25px;
  background-color: transparent !important;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
  overflow: hidden;
  margin: 0.3rem;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.premium-button::before {
  content: '';
  position: absolute;
  inset: 0;
  margin: auto;
  width: 40px;
  height: 40px;
  border-radius: inherit;
  scale: 0;
  z-index: -1;
  background-color: rgb(193, 163, 98) !important;
  transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
}

.premium-button:hover::before {
  scale: 3;
}

.premium-button:hover {
  color: #212121 !important;
  scale: 1.1;
  box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
  text-decoration: none;
}

.premium-button:active {
  scale: 1;
}

.premium-button i {
  margin-right: 6px;
  font-size: 14px;
  color: rgb(193, 163, 98) !important;
  transition: all 0.3s ease;
}

.premium-button:hover i {
  color: #212121 !important;
}

.premium-button.btn-lg {
  padding: 8px 20px;
  font-size: 16px;
}

.premium-button:disabled {
  opacity: 0.7;
  cursor: wait;
}

.premium-button .fa-spinner {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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
  background-color: transparent !important;
  border: 1px solid rgb(193, 163, 98) !important;
  color: rgb(193, 163, 98) !important;
  width: 13rem;
  margin-left: -0.65rem;
  padding: .15rem 1rem;
  border-radius: 5px 0 0 5px;
  transition: all 0.3s ease;
}

.etiquet-price p {
  margin: 0;
  padding-top: .3rem;
  display: flex;
  font-size: 1.6rem;
  font-weight: 500;
  color: rgb(193, 163, 98) !important;
}

.etiquet-price p:before {
  content: "$";
  margin-right: 4px;
  font-size: 13px;
  font-weight: 300;
  color: rgb(193, 163, 98) !important;
}

.etiquet-price p:after {
  content: "/ account";
  margin-left: 4px;
  font-size: 13px;
  font-weight: 300;
  color: rgb(193, 163, 98) !important;
}

.etiquet-price div {
  position: absolute;
  bottom: -23px;
  right: 0px;
  width: 0;
  height: 0;
  border-top: 13px solid rgb(193, 163, 98);
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
  margin-top: 1rem;
}

.button-get-plan a {
  cursor: pointer;
  position: relative;
  padding: 6px 16px;
  font-size: 14px;
  color: rgb(193, 163, 98) !important;
  border: 1px solid rgb(193, 163, 98) !important;
  border-radius: 25px;
  background-color: transparent !important;
  font-weight: 600;
  transition: all 0.3s cubic-bezier(0.23, 1, 0.320, 1);
  overflow: hidden;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.button-get-plan a::before {
  content: '';
  position: absolute;
  inset: 0;
  margin: auto;
  width: 35px;
  height: 35px;
  border-radius: inherit;
  scale: 0;
  z-index: -1;
  background-color: rgb(193, 163, 98) !important;
  transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
}

.button-get-plan .svg-rocket {
  margin-right: 6px;
  width: .7rem;
  fill: rgb(193, 163, 98) !important;
  transition: all 0.3s ease;
}

.button-get-plan a:hover::before {
  scale: 3;
}

.button-get-plan a:hover {
  color: #212121 !important;
  scale: 1.1;
  box-shadow: 0 0px 20px rgba(193, 163, 98, 0.4) !important;
  text-decoration: none;
}

.button-get-plan a:hover .svg-rocket {
  fill: #212121 !important;
}
</style>
@endsection

<!-- Fast Check For Premium Status Before Page Loads -->
<script>
    // Function to get cookie value by name
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }
    
    // Function to enable premium features immediately if cookie is set
    window.applyPremiumStatus = function() {
        // Check for premium status in localStorage or cookies
        if (localStorage.getItem('isPremium') === 'true' || getCookie('is_premium') === 'true') {
            console.log('Premium status detected, applying immediately');
            
            // Add premium class to body for CSS targeting
            document.body.classList.add('premium-account');
            
            // This will be picked up by the DOMContentLoaded event handler
            window.forcePremium = true;
        }
    };
    
    // Apply premium status immediately
    applyPremiumStatus();
</script>

@section('content')
<div class="container-fluid py-4">
    @php
        // Check for premium status from multiple sources
        $tenantData = tenant();
        
        // Try to refresh tenant data to make sure we have the latest information
        if ($tenantData && is_object($tenantData) && method_exists($tenantData, 'refresh')) {
            $tenantData->refresh();
        }
        
        $isPremiumFromTenant = $tenantData && $tenantData->subscription_plan === 'premium';
        
        // Check if we have a session variable indicating premium
        $isPremiumFromSession = session('is_premium') === true;
        
        // Check if there's a cookie indicating premium
        $isPremiumFromCookie = isset($_COOKIE['is_premium']) && $_COOKIE['is_premium'] === 'true';
        
        // Use any source
        $isPremium = $isPremiumFromTenant || $isPremiumFromSession || $isPremiumFromCookie;
        
        // If premium from any source, set session to ensure consistency
        if ($isPremium && !session('is_premium')) {
            session(['is_premium' => true]);
        }
    @endphp
    
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
                                    // Already computed $isPremium at the top of the file
                                @endphp
                                
                                <div class="mt-2">
                                    @if($isPremium)
                                    <div class="premium-badge">
                                        <i class="fas fa-crown"></i>
                                        <span>Premium</span>
                                    </div>
                                    @else
                                    <span class="badge bg-secondary rounded-pill px-3 py-2" data-subscription-status>
                                        Basic
                                    </span>
                                    @endif
                                </div>
                                
                                @if(!$isPremium)
                                <div class="mt-3">
                                    <button type="button" class="premium-button" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                        <i class="fas fa-crown"></i>Upgrade to Premium
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
                                            <button type="button" class="premium-button" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                                <i class="fas fa-crown"></i>Upgrade to Edit
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
                                            <button type="button" class="premium-button" data-bs-toggle="modal" data-bs-target="#subscriptionModal">
                                                <i class="fas fa-crown"></i>Upgrade to Edit
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
                                <button type="button" class="premium-button btn-lg" id="upgradeNowBtn" data-bs-toggle="modal" data-bs-target="#contactSupportModal" data-bs-dismiss="modal">
                                    <i class="fas fa-rocket"></i>Upgrade Now
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
                    <i class="fas fa-crown text-warning me-2"></i><span id="contactModalTitle">Upgrade Subscription</span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contactModalBody">
                <!-- Success message content (initially hidden) -->
                <div id="successMessage" class="text-center py-4" style="display: none;">
                    <i class="fas fa-check-circle text-success fa-5x mb-4"></i>
                    <h4>Upgrade Successful!</h4>
                    <p class="text-muted mb-0">Your account has been upgraded to Premium.</p>
                    <p class="text-muted mb-3">You can now edit your profile information.</p>
                </div>
                
                <!-- Form content (initially shown) -->
                <div id="contactFormContent">
                    <div class="text-center mb-4">
                        <i class="fas fa-crown text-warning fa-3x mb-3"></i>
                        <h4>Confirm Subscription Upgrade</h4>
                        <p class="text-muted">You're about to upgrade to our Premium plan. Click the button below to confirm.</p>
                    </div>
                    
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-success bg-opacity-10 me-3">
                                    <i class="fas fa-check-circle text-success"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Immediate Access</h6>
                                    <p class="mb-0 small text-muted">Your account will be upgraded instantly</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center mb-3">
                                <div class="rounded-circle p-2 bg-primary bg-opacity-10 me-3">
                                    <i class="fas fa-unlock text-primary"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Premium Features</h6>
                                    <p class="mb-0 small text-muted">Edit profile, change password, and more</p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle p-2 bg-info bg-opacity-10 me-3">
                                    <i class="fas fa-headset text-info"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">Premium Support</h6>
                                    <p class="mb-0 small text-muted">Get priority customer service</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form id="contactForm">
                        <input type="hidden" id="fullName" value="{{ $user->name }}">
                        <input type="hidden" id="email" value="{{ $user->email }}">
                        <input type="hidden" id="message" value="Upgrading to Premium plan">
                    </form>
                </div>
            </div>
            <div class="modal-footer" id="contactModalFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="premium-button" id="sendSupportRequest">
                    <i class="fas fa-crown"></i>Upgrade Now
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
        // Check if premium with proper null checks or from localStorage or forced premium
        @php
            $tenantData = tenant();
            $isPremiumJs = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
            $isPremiumJs = $isPremium; // Use our comprehensive check from above
        @endphp
        let isPremium = {{ $isPremiumJs ? 'true' : 'false' }};
        
        // Check if window.forcePremium is set by our early script
        if (window.forcePremium === true) {
            console.log('Forced premium mode detected');
            isPremium = true;
        }
        
        // Check localStorage for premium status - this handles cases where the tenant() function
        // doesn't immediately reflect database changes after a refresh
        if (!isPremium && (localStorage.getItem('isPremium') === 'true' || getCookie('is_premium') === 'true')) {
            console.log('Premium status detected in storage, enabling premium features');
            isPremium = true;
            
            // Update the UI immediately to reflect premium status
            const premiumBadge = document.querySelector('.badge[data-subscription-status]');
            if (premiumBadge) {
                // Create new premium badge with gold styling
                const newBadge = document.createElement('div');
                newBadge.className = 'premium-badge';
                newBadge.innerHTML = '<i class="fas fa-crown"></i><span>Premium</span>';
                
                // Replace old badge with new one
                premiumBadge.parentNode.replaceChild(newBadge, premiumBadge);
            }
            
            // Remove warning alerts
            const warningAlerts = document.querySelectorAll('.alert-warning');
            warningAlerts.forEach(alert => {
                alert.style.display = 'none';
            });
            
            // Remove upgrade buttons
            const upgradeButtons = document.querySelectorAll('button[data-bs-target="#subscriptionModal"]');
            upgradeButtons.forEach(button => {
                button.style.display = 'none';
            });
            
            // Enable form inputs
            enableProfileEditing();
        }
        
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
        
        // Auto-show success on clicking Upgrade Now button
        document.getElementById('upgradeNowBtn')?.addEventListener('click', function(e) {
            // The modal transition will be handled more carefully
            // No immediate actions here - let the modal open naturally
        });
        
        // Handle subscription upgrade confirmation
        document.getElementById('sendSupportRequest')?.addEventListener('click', function() {
            // Start by showing a processing state
            this.disabled = true;
            this.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Processing...';
            
            // Process the upgrade with a slight delay to show the spinner
            setTimeout(function() {
                // Update the subscription plan via AJAX
                updateSubscriptionPlan();
            }, 800);
        });
        
        // Function to update subscription plan via AJAX
        function updateSubscriptionPlan() {
            // Get CSRF token from meta tag
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Disable the upgrade button and show loading state
            const upgradeButton = document.getElementById('sendSupportRequest');
            if (upgradeButton) {
                upgradeButton.disabled = true;
                upgradeButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>Processing...';
            }
            
            // Send AJAX request to update subscription plan
            fetch('{{ route("profile.update-subscription", ["tenant" => tenant("id")]) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    subscription_plan: 'premium',
                    user_id: '{{ $user->id }}',
                    user_name: '{{ $user->name }}',
                    user_email: '{{ $user->email }}',
                    message: document.getElementById('message')?.value || 'Upgraded to Premium plan',
                    set_session: true // Add flag to set session variable
                })
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 404) {
                        throw new Error('Server endpoint not found. Please try refreshing the page.');
                    }
                    throw new Error('Server returned status ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                console.log('Subscription update response:', data);
                
                // If the update was successful, update both database fields and session
                if (data.success) {
                    // Update the local storage to indicate premium status
                    localStorage.setItem('isPremium', 'true');
                    localStorage.setItem('subscriptionPlan', 'premium');
                    
                    // Set a cookie to maintain state across page refreshes
                    document.cookie = "is_premium=true; path=/; max-age=86400";
                    
                    // Enable all form fields immediately
                    enableProfileEditing();
                    
                    // Show success message with a delay to ensure modal transition is complete
                    setTimeout(() => {
                        showSuccessMessage();
                        
                        // Update UI to show premium status
                        const premiumBadge = document.querySelector('.badge[data-subscription-status]');
                        if (premiumBadge) {
                            // Create new premium badge with gold styling
                            const newBadge = document.createElement('div');
                            newBadge.className = 'premium-badge';
                            newBadge.innerHTML = '<i class="fas fa-crown"></i><span>Premium</span>';
                            
                            // Replace old badge with new one
                            premiumBadge.parentNode.replaceChild(newBadge, premiumBadge);
                        }
                        
                        // Remove upgrade buttons
                        const upgradeButtons = document.querySelectorAll('button[data-bs-target="#subscriptionModal"]');
                        upgradeButtons.forEach(button => {
                            button.style.display = 'none';
                        });
                        
                        // Force reload after successful update to reflect changes server-side
                        setTimeout(() => {
                            // Add a query parameter to force a fresh load
                            window.location.href = window.location.pathname + '?upgraded=true&t=' + new Date().getTime();
                        }, 3000);
                    }, 1000);
                } else {
                    showErrorMessage(data.message || 'Failed to upgrade subscription.');
                }
            })
            .catch(error => {
                console.error('Error updating subscription:', error);
                showErrorMessage(error.message || 'Failed to connect to server. Please try again.');
                
                // Still enable editing if we have localStorage premium status despite the error
                if (localStorage.getItem('isPremium') === 'true') {
                    enableProfileEditing();
                    showSuccessMessage();
                }
            });
        }
        
        // Function to enable profile editing
        function enableProfileEditing() {
            // Remove readonly and disabled attributes from all inputs
            const allInputs = document.querySelectorAll('input[readonly], input[disabled], textarea[readonly], textarea[disabled], button[disabled]');
            allInputs.forEach(input => {
                if(input.id !== 'sendSupportRequest') {
                    input.removeAttribute('readonly');
                    input.removeAttribute('disabled');
                }
            });
            
            // Remove all warning alerts about basic plan
            const warningAlerts = document.querySelectorAll('.alert-warning');
            warningAlerts.forEach(alert => {
                alert.style.display = 'none';
            });
        }
        
        // Function to show error message in the modal
        function showErrorMessage(message) {
            // Reset the upgrade button
            const upgradeButton = document.getElementById('sendSupportRequest');
            if (upgradeButton) {
                upgradeButton.disabled = false;
                upgradeButton.innerHTML = '<i class="fas fa-crown"></i>Upgrade Now';
            }
            
            // Create alert element
            const errorAlert = document.createElement('div');
            errorAlert.className = 'alert alert-danger';
            errorAlert.innerHTML = `<i class="fas fa-exclamation-circle me-2"></i>${message}`;
            
            // Find the form content area and add the alert
            const formContent = document.getElementById('contactFormContent');
            if (formContent) {
                // Remove any existing error alerts
                const existingAlerts = formContent.querySelectorAll('.alert-danger');
                existingAlerts.forEach(alert => alert.remove());
                
                // Add the new alert at the top
                formContent.prepend(errorAlert);
                
                // Make sure the form content is visible
                formContent.style.display = 'block';
            }
        }
        
        // Function to show success message
        function showSuccessMessage() {
            // Update modal content
            document.getElementById('contactModalTitle').textContent = 'Upgrade Complete';
            document.getElementById('contactFormContent').style.display = 'none';
            document.getElementById('successMessage').style.display = 'block';
            
            // Update footer with option to close or continue to edit profile
            document.getElementById('contactModalFooter').innerHTML = `
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="premium-button" id="continueToProfile" data-bs-dismiss="modal">
                    <i class="fas fa-user-edit"></i>Edit Profile Now
                </button>
            `;
            
            // Add click handler to focus on profile tab after closing
            setTimeout(() => {
                document.getElementById('continueToProfile')?.addEventListener('click', function() {
                    // Focus on the profile tab
                    document.getElementById('profile-tab').click();
                    // Focus on the name field
                    setTimeout(() => document.getElementById('name').focus(), 300);
                });
            }, 100);
            
            // Force a refresh after 3 minutes to ensure all changes are reflected
            setTimeout(() => {
                if (localStorage.getItem('isPremium') === 'true') {
                    window.location.reload();
                }
            }, 180000); // 3 minutes
        }
    });
</script>
@endsection
