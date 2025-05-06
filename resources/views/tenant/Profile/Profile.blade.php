@extends('tenant.layouts.app')

@section('title', 'My Profile')

@section('styles')
<style>
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

@media (max-width: 768px) {
    .modal-dialog {
        max-width: 95%;
        margin: 1rem auto;
    }
}

.premium-badge {
    display: inline-flex;
    align-items: center;
    background-color: #fff3cd;
    color: #856404;
    padding: 0.35rem 0.75rem;
    border-radius: 2rem;
    font-size: 0.85rem;
    font-weight: 500;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.premium-badge i {
    margin-right: 0.5rem;
    color: #ffc107;
}

.premium-lock {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(247, 247, 247, 0.9);
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    border-radius: 0.5rem;
    z-index: 10;
    padding: 2rem;
}

.premium-lock i {
    font-size: 3rem;
    color: #ffc107;
    margin-bottom: 1rem;
}

.premium-lock h4 {
    color: #495057;
    margin-bottom: 1rem;
}

.premium-lock p {
    color: #6c757d;
    margin-bottom: 1.5rem;
    max-width: 80%;
}

.premium-feature-tag {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    padding: 0.25rem 0.75rem;
    background-color: #ffeeba;
    color: #856404;
    border-radius: 2rem;
    font-size: 0.75rem;
    font-weight: 500;
    z-index: 5;
}

/* Plan selection styles */
.plan-option {
    cursor: pointer;
    transition: all 0.2s ease;
    border-width: 2px !important;
}

.plan-option:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.plan-option .form-check-input {
    margin-right: 10px;
}

.form-check-input:checked + .form-check-label .badge {
    transform: scale(1.1);
}

.plan-option .badge {
    transition: all 0.2s ease;
}

/* Premium plan specific */
.form-check-input[value="premium"]:checked ~ .plan-option {
    border-color: #ffc107 !important;
    background-color: rgba(255, 193, 7, 0.05);
}

/* Ultimate plan specific */
.form-check-input[value="ultimate"]:checked ~ .plan-option {
    border-color: #4361ee !important;
    background-color: rgba(67, 97, 238, 0.05);
}

/* Split Layout for Premium Modal - From register.blade.php */
.split-layout {
    display: flex;
    min-height: 100%;
}

.form-side {
    flex: 1;
    padding: 1rem;
}

.image-side {
    flex: 1;
    background-color: #f8f9fa;
    display: none;
    position: relative;
}

.image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(3, 1, 43, 0.85), rgba(3, 1, 43, 0.7));
    color: white;
    padding: 3rem;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.feature-list {
    list-style: none;
    padding: 0;
    margin-top: 2rem;
}

.feature-list li {
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
}

.feature-list li i {
    margin-right: 1rem;
    color: #41d7a7;
}

.auth-form-light {
    background-color: #fff;
    border-radius: 5px;
}

.input-icon-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    height: 100%;
    display: flex;
    align-items: center;
    padding-left: 1rem;
    color: #6c757d;
}

.input-icon {
    color: #6c757d;
}

/* Plan Card Styles - From Subscription.blade.php */
.plan-cards-container {
    display: flex;
    justify-content: center;
    gap: 2rem;
    flex-wrap: wrap;
    margin: 2rem 0;
}

.plan-card {
    background: #fff;
    width: 16rem;
    padding: 1.5rem;
    border-radius: 10px;
    border-bottom: 4px solid rgb(3, 1, 43);
    box-shadow: 0 6px 30px rgba(207, 212, 222, 0.3);
    font-family: "Poppins", sans-serif;
    transition: all 0.3s ease;
}

.plan-card:hover {
    transform: translateY(-10px);
}

.plan-card h2 {
    margin-bottom: 15px;
    font-size: 24px;
    font-weight: 600;
    color: rgb(3, 1, 43);
}

.plan-card h2 span {
    display: block;
    margin-top: 4px;
    color: #4d4d4d;
    font-size: 12px;
    font-weight: 400;
}

.etiquet-price {
    position: relative;
    background: #fdbd4a;
    width: 110%;
    margin-left: -5%;
    padding: .4rem 1.2rem;
    border-radius: 5px 0 0 5px;
    margin-bottom: 1.5rem;
}

.etiquet-price p {
    margin: 0;
    padding-top: .4rem;
    display: flex;
    font-size: 1.9rem;
    font-weight: 500;
}

.etiquet-price p:before {
    content: "₱";
    margin-right: 5px;
    font-size: 15px;
    font-weight: 300;
}

.etiquet-price p:after {
    content: "/ year";
    margin-left: 5px;
    font-size: 15px;
    font-weight: 300;
}

.etiquet-price div {
    position: absolute;
    bottom: -13px;
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
    margin-bottom: .8rem;
    display: flex;
    align-items: center;
    gap: .5rem;
}

.benefits-list ul li svg {
    width: .9rem;
    fill: currentColor;
}

.benefits-list ul li i {
    color: rgb(3, 1, 43);
}

.benefits-list ul li span {
    font-weight: 300;
}

.button-get-plan {
    display: flex;
    justify-content: center;
    margin-top: 1.5rem;
}

.button-get-plan a {
    display: flex;
    justify-content: center;
    align-items: center;
    background: rgb(3, 1, 43);
    color: #fff;
    padding: 10px 15px;
    border-radius: 5px;
    text-decoration: none;
    font-size: .9rem;
    letter-spacing: .05rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.button-get-plan a:hover {
    transform: translateY(-3%);
    box-shadow: 0 3px 10px rgba(207, 212, 222, 0.9);
}

.button-get-plan .svg-rocket,
.button-get-plan i {
    margin-right: 10px;
    width: .9rem;
    fill: currentColor;
}

@media (min-width: 768px) {
    .image-side {
        display: block;
    }
    
    .modal-dialog.premium-modal {
        max-width: 900px;
    }
}

@media (max-width: 768px) {
    .modal-dialog {
        max-width: 95%;
        margin: 1rem auto;
    }
    
    .premium-lock p {
        max-width: 95%;
    }
}
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-user-circle me-2"></i>My Profile</h5>
                        @php
                            // Get current URL to extract tenant ID
                            $url = request()->url();
                            preg_match('/^https?:\/\/([^\.]+)\./', $url, $matches);
                            $tenantDomain = $matches[1] ?? null;
                            
                            // Get tenant from domain or tenant helper
                            if ($tenantDomain) {
                                $currentTenant = \App\Models\Tenant::where('id', $tenantDomain)->first();
                            } else {
                                $tenantId = tenant('id') ?? null;
                                $currentTenant = $tenantId ? \App\Models\Tenant::find($tenantId) : null;
                            }
                            
                            $isPremium = $currentTenant && $currentTenant->subscription_plan === 'premium';
                            $isUltimate = $currentTenant && $currentTenant->subscription_plan === 'ultimate';
                        @endphp

                        @if($isPremium)
                            <span class="premium-badge" style="background-color: #ffeccc; color: #FF8C00;">
                                <i class="fas fa-crown" style="color: #FF8C00;"></i>
                                <span>Premium Account</span>
                            </span>
                        @elseif($isUltimate)
                            <span class="premium-badge" style="background-color: #e6eaff; color: #4361ee;">
                                <i class="fas fa-star" style="color: #4361ee;"></i>
                                <span>Ultimate Account</span>
                            </span>
                        @endif
                    </div>
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
                            
                           
                            
                            @if(!$isPremium && !$isUltimate)
                                <div class="mt-3">
                                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#premiumFeaturesModal">
                                        <i class="fas fa-crown me-1"></i> Upgrade to Premium
                                    </a>
                                </div>
                            @endif
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
                                        @if(!$isPremium && !$isUltimate)
                                            <i class="fas fa-crown text-warning ms-1" data-bs-toggle="tooltip" title="Premium Feature"></i>
                                        @endif
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="profileTabsContent">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane fade show active position-relative" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    @if(!$isPremium && !$isUltimate)
                                        <span class="premium-feature-tag">
                                            <i class="fas fa-crown text-warning ms-1"></i> Premium Feature
                                        </span>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('profile.update', ['tenant' => tenant('id')]) }}" enctype="multipart/form-data" id="profileForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Full Name</label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" 
                                                value="{{ old('name', $user->name) }}" {{ !$isPremium && !$isUltimate ? 'disabled' : 'required' }}>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                                value="{{ old('email', $user->email) }}" {{ !$isPremium && !$isUltimate ? 'disabled' : 'required' }}>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" {{ !$isPremium && !$isUltimate ? 'disabled' : '' }}>
                                            <div class="form-text">Upload a square image for best results. Maximum size: 2MB</div>
                                            @error('avatar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" id="updateProfileBtn" {{ !$isPremium && !$isUltimate ? 'disabled' : '' }} style="color: white;">
                                                <i class="fas fa-save me-2"></i>Update Profile
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade position-relative" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                    @if(!$isPremium && !$isUltimate)
                                        <span class="premium-feature-tag">
                                            <i class="fas fa-crown text-warning ms-1"></i> Premium Feature
                                        </span>
                                    @endif
                                    
                                    <form method="POST" action="{{ route('profile.password', ['tenant' => tenant('id')]) }}" id="passwordForm">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                                id="current_password" name="current_password" {{ !$isPremium && !$isUltimate ? 'disabled' : 'required' }}>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" {{ !$isPremium && !$isUltimate ? 'disabled' : 'required' }}>
                                            <div class="form-text">Password must be at least 8 characters</div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" 
                                                name="password_confirmation" {{ !$isPremium && !$isUltimate ? 'disabled' : 'required' }}>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" id="updatePasswordBtn" {{ !$isPremium && !$isUltimate ? 'disabled' : '' }}>
                                                <i class="fas fa-key me-2"></i>Update Password
                                            </button>
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

<!-- Premium Upgrade Modal -->
<div class="modal fade" id="upgradePremiumModal" tabindex="-1" aria-labelledby="upgradePremiumModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered premium-modal">
        <div class="modal-content p-0 overflow-hidden">
            <div class="split-layout">
                <!-- Form Side -->
                <div class="form-side">
                    <div class="auth-form-light p-4">
                    <div class="mb-4">
                        <button type="button" class="btn-close float-end" data-bs-dismiss="modal" aria-label="Close"></button>
                        <h4 class="mb-2"><i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium</h4>
                        <p class="text-muted">Access advanced features to enhance your department management</p>
                    </div>
                    
                    <form action="{{ route('tenant.subscription.upgrade', ['tenant' => tenant('id')]) }}" method="POST" id="upgradeForm">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Select Plan</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check plan-option p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="plan" id="plan_premium" value="premium" checked>
                                        <label class="form-check-label w-100" for="plan_premium">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>Premium</strong>
                                                <span class="badge bg-warning">₱999</span>
                                            </div>
                                            <small class="text-muted">Enhanced department features</small>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check plan-option p-3 border rounded">
                                        <input class="form-check-input" type="radio" name="plan" id="plan_ultimate" value="ultimate">
                                        <label class="form-check-label w-100" for="plan_ultimate">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <strong>Ultimate</strong>
                                                <span class="badge" style="background-color: #4361ee;">₱1999</span>
                                            </div>
                                            <small class="text-muted">Access to all features including Reports</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <div class="position-relative">
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-credit-card input-icon"></i>
                                </div>
                                <select class="form-select ps-4" id="payment_method" name="payment_method" required>
                                    <option value="">Select payment method</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                            </div>
                        </div>
                        
                        <div id="bankTransferDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Bank Transfer Instructions</h6>
                                <p class="mb-0">Please transfer ₱999.00 to the following account:</p>
                                <hr>
                                <p class="mb-1"><strong>Bank:</strong> BDO</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-1"><strong>Account Number:</strong> 1234-5678-9012</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div id="gcashDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>GCash Instructions</h6>
                                <p class="mb-0">Please send ₱999.00 to the following GCash number:</p>
                                <hr>
                                <p class="mb-1"><strong>GCash Number:</strong> 0917-123-4567</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div id="paymayaDetails" class="payment-details mb-3 d-none">
                            <div class="alert alert-info">
                                <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>PayMaya Instructions</h6>
                                <p class="mb-0">Please send ₱999.00 to the following PayMaya number:</p>
                                <hr>
                                <p class="mb-1"><strong>PayMaya Number:</strong> 0918-765-4321</p>
                                <p class="mb-1"><strong>Account Name:</strong> BukSkwela Inc.</p>
                                <p class="mb-0"><strong>Reference:</strong> Premium-{{ tenant('id') }}</p>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reference_number" class="form-label">Reference Number</label>
                            <div class="position-relative">
                                    <div class="input-icon-wrapper">
                                        <i class="fas fa-hashtag input-icon"></i>
                                </div>
                                <input type="text" class="form-control ps-4" id="reference_number" name="reference_number" placeholder="Enter your payment reference number" required>
                            </div>
                            <div class="form-text">Please enter the reference number from your payment transaction.</div>
                        </div>
                        
                        <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-warning btn-lg auth-form-btn">
                                    <i class="fas fa-crown me-2"></i>Complete Upgrade
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Premium Features Modal -->
<div class="modal fade" id="premiumFeaturesModal" tabindex="-1" aria-labelledby="premiumFeaturesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="modal-title" id="premiumFeaturesModalLabel">Premium Plans</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
            <div class="plan-cards-container">
                <div class="plan-card">
                    <h2>Premium<span>Enhanced department features</span></h2>
                    <div class="etiquet-price">
                        <p>999</p>
                        <div></div>
                    </div>
                    <div class="benefits-list">
                        <ul>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Instructor Management</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Student Management</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>View Student Submission Status</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Probationary Status Management</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Custom Enrollment Requirements</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>View Uploaded Documents</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Submission Reports</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Branding Customization</span>
                            </li>
                        </ul>
                    </div>
                    <div class="button-get-plan">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#upgradePremiumModal" data-bs-dismiss="modal">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-rocket">
                                <path d="M156.6 384.9L125.7 353.1C117.2 345.5 114.2 333.1 117.1 321.8C120.1 312.9 124.1 301.3 129.8 288H24C15.38 288 7.414 283.4 3.146 275.9C-1.123 268.4-1.042 259.2 3.357 251.8L55.83 163.3C68.79 141.4 92.33 127.1 117.8 127.1H200C202.4 124 204.8 120.3 207.2 116.7C289.1-4.07 411.1-8.142 483.9 5.275C495.6 7.414 504.6 16.43 506.7 28.06C520.1 100.9 516.1 222.9 395.3 304.8C391.8 307.2 387.1 309.6 384 311.1V394.2C384 419.7 370.6 443.2 348.7 456.2L260.2 508.6C252.8 513 243.6 513.1 236.1 508.9C228.6 504.6 224 496.6 224 488V380.8C209.9 385.6 197.6 389.7 188.3 392.7C177.1 396.3 164.9 393.2 156.6 384.9V384.9zM384 167.1C406.1 167.1 424 150.1 424 127.1C424 105.9 406.1 87.1 384 87.1C361.9 87.1 344 105.9 344 127.1C344 150.1 361.9 167.1 384 167.1z"></path>
                            </svg>
                            <span>UPGRADE NOW</span>
                        </a>
                    </div>
                </div>

                <div class="plan-card" style="border-bottom: 4px solid #4361ee;">
                    <h2>Ultimate<span>Complete access to all features</span></h2>
                    <div class="etiquet-price" style="background: #4361ee;">
                        <p>1999</p>
                        <div></div>
                    </div>
                    <div class="benefits-list">
                        <ul>
                            <!-- Include all Premium features -->
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>All Premium Features</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Advanced Analytics Reports</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Student Performance Tracking</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Staff Performance Reports</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Course Completion Reports</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Requirements Analysis Tools</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M243.8 339.8C232.9 350.7 215.1 350.7 204.2 339.8L140.2 275.8C129.3 264.9 129.3 247.1 140.2 236.2C151.1 225.3 168.9 225.3 179.8 236.2L224 280.4L332.2 172.2C343.1 161.3 360.9 161.3 371.8 172.2C382.7 183.1 382.7 200.9 371.8 211.8L243.8 339.8zM512 256C512 397.4 397.4 512 256 512C114.6 512 0 397.4 0 256C0 114.6 114.6 0 256 0C397.4 0 512 114.6 512 256zM256 48C141.1 48 48 141.1 48 256C48 370.9 141.1 464 256 464C370.9 464 464 370.9 464 256C464 141.1 370.9 48 256 48z"></path>
                                </svg>
                                <span>Priority Support</span>
                            </li>
                        </ul>
                    </div>
                    <div class="button-get-plan">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#upgradePremiumModal" data-bs-dismiss="modal" style="background: #4361ee;">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" class="svg-rocket">
                                <path d="M156.6 384.9L125.7 353.1C117.2 345.5 114.2 333.1 117.1 321.8C120.1 312.9 124.1 301.3 129.8 288H24C15.38 288 7.414 283.4 3.146 275.9C-1.123 268.4-1.042 259.2 3.357 251.8L55.83 163.3C68.79 141.4 92.33 127.1 117.8 127.1H200C202.4 124 204.8 120.3 207.2 116.7C289.1-4.07 411.1-8.142 483.9 5.275C495.6 7.414 504.6 16.43 506.7 28.06C520.1 100.9 516.1 222.9 395.3 304.8C391.8 307.2 387.1 309.6 384 311.1V394.2C384 419.7 370.6 443.2 348.7 456.2L260.2 508.6C252.8 513 243.6 513.1 236.1 508.9C228.6 504.6 224 496.6 224 488V380.8C209.9 385.6 197.6 389.7 188.3 392.7C177.1 396.3 164.9 393.2 156.6 384.9V384.9zM384 167.1C406.1 167.1 424 150.1 424 127.1C424 105.9 406.1 87.1 384 87.1C361.9 87.1 344 105.9 344 127.1C344 150.1 361.9 167.1 384 167.1z"></path>
                            </svg>
                            <span>UPGRADE NOW</span>
                        </a>
                    </div>
                </div>
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
    
    // Handle payment method change to show appropriate instructions
    document.getElementById('payment_method')?.addEventListener('change', function() {
        // Hide all payment details
        document.querySelectorAll('.payment-details').forEach(el => {
            el.classList.add('d-none');
        });
        
        // Show selected payment method details
        const method = this.value;
        if (method) {
            document.getElementById(method + 'Details')?.classList.remove('d-none');
        }
    });
    
    // Handle plan selection to update payment amount in instructions
    document.querySelectorAll('input[name="plan"]')?.forEach(radio => {
        radio.addEventListener('change', function() {
            const planValue = this.value;
            const amount = planValue === 'premium' ? '999.00' : '1999.00';
            
            // Update amount in all payment detail sections
            document.querySelectorAll('.payment-details p.mb-0').forEach(p => {
                const text = p.textContent;
                if (text && text.includes('₱')) {
                    p.textContent = text.replace(/₱[0-9,\.]+/, '₱' + amount);
                }
            });
        });
    });

    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Add back button to payment modal
        const upgradePremiumModal = document.getElementById('upgradePremiumModal');
        if (upgradePremiumModal) {
            const modalHeader = upgradePremiumModal.querySelector('.mb-4');
            
            if (modalHeader) {
                // Create back button
                const backBtn = document.createElement('button');
                backBtn.type = 'button';
                backBtn.className = 'btn btn-link text-muted p-0 mb-2';
                backBtn.innerHTML = '<i class="fas fa-arrow-left me-1"></i> Back to plans';
                backBtn.setAttribute('data-bs-dismiss', 'modal');
                backBtn.setAttribute('data-bs-toggle', 'modal');
                backBtn.setAttribute('data-bs-target', '#premiumFeaturesModal');
                
                // Insert at beginning of header
                modalHeader.insertBefore(backBtn, modalHeader.firstChild);
            }
        }
        
        // Make plan options clickable (label and div)
        document.querySelectorAll('.plan-option').forEach(option => {
            option.addEventListener('click', function() {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    radio.dispatchEvent(new Event('change'));
                }
            });
        });
        
        // Make plan card hover effect work on touch devices
        const planCards = document.querySelectorAll('.plan-card');
        planCards.forEach(card => {
            card.addEventListener('touchstart', function() {
                this.style.transform = 'translateY(-10px)';
            });
            card.addEventListener('touchend', function() {
                this.style.transform = '';
            });
        });
    });
</script>
@endsection
