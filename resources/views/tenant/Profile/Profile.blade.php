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
                        @endphp

                        @if($isPremium)
                            <span class="premium-badge">
                                <i class="fas fa-crown"></i>
                                <span>Premium Account</span>
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
                            
                           
                            
                            @if(!$isPremium)
                                <div class="mt-3">
                                    <a href="#" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#upgradePremiumModal">
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
                                        @if(!$isPremium)
                                            <i class="fas fa-crown text-warning ms-1" data-bs-toggle="tooltip" title="Premium Feature"></i>
                                        @endif
                                    </button>
                                </li>
                            </ul>
                            
                            <div class="tab-content p-3 border border-top-0 rounded-bottom" id="profileTabsContent">
                                <!-- Profile Information Tab -->
                                <div class="tab-pane fade show active position-relative" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                                    @if(!$isPremium)
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
                                                value="{{ old('name', $user->name) }}" {{ !$isPremium ? 'disabled' : 'required' }}>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address</label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" 
                                                value="{{ old('email', $user->email) }}" {{ !$isPremium ? 'disabled' : 'required' }}>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="avatar" class="form-label">Profile Picture</label>
                                            <input type="file" class="form-control @error('avatar') is-invalid @enderror" id="avatar" name="avatar" {{ !$isPremium ? 'disabled' : '' }}>
                                            <div class="form-text">Upload a square image for best results. Maximum size: 2MB</div>
                                            @error('avatar')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" id="updateProfileBtn" {{ !$isPremium ? 'disabled' : '' }}>
                                                <i class="fas fa-save me-2"></i>Update Profile
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                
                                <!-- Password Tab -->
                                <div class="tab-pane fade position-relative" id="password-tab-pane" role="tabpanel" aria-labelledby="password-tab" tabindex="0">
                                    @if(!$isPremium)
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
                                                id="current_password" name="current_password" {{ !$isPremium ? 'disabled' : 'required' }}>
                                            @error('current_password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password" class="form-label">New Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                id="password" name="password" {{ !$isPremium ? 'disabled' : 'required' }}>
                                            <div class="form-text">Password must be at least 8 characters</div>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                            <input type="password" class="form-control" id="password_confirmation" 
                                                name="password_confirmation" {{ !$isPremium ? 'disabled' : 'required' }}>
                                        </div>
                                        
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <button type="submit" class="btn btn-primary" id="updatePasswordBtn" {{ !$isPremium ? 'disabled' : '' }}>
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
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title" id="upgradePremiumModalLabel">
                    <i class="fas fa-crown text-warning me-2"></i>Upgrade to Premium
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-2">
                <div class="text-center mb-4">
                    <div class="bg-warning bg-opacity-10 rounded-circle p-3 d-inline-block mb-3">
                        <i class="fas fa-crown text-warning fs-1"></i>
                    </div>
                    <h4>Unlock Premium Features</h4>
                    <p class="text-muted">Upgrade your account to access premium features and enhance your school management capabilities.</p>
                </div>
                
                <div class="card border-warning mb-4">
                    <div class="card-header bg-warning bg-opacity-10 border-warning">
                        <h5 class="mb-0 text-warning"><i class="fas fa-star me-2"></i>Premium Benefits</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Profile customization</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Advanced reporting and analytics</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Unlimited staff accounts</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-0 px-0">
                                <i class="fas fa-check-circle text-success me-3"></i>
                                <span>Priority customer support</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <div class="card border-primary mb-4">
                    <div class="card-header bg-primary bg-opacity-10 border-primary">
                        <h5 class="mb-0 text-primary"><i class="fas fa-money-bill-wave me-2"></i>Subscription Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold">Premium Plan:</span>
                            <span class="badge bg-primary rounded-pill px-3 py-2">Monthly</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Price:</span>
                            <span class="fw-bold fs-4">₱999.00</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Billing:</span>
                            <span>Monthly, auto-renews</span>
                        </div>
                    </div>
                </div>
                
                <form action="{{ route('tenant.subscription.upgrade', ['tenant' => tenant('id')]) }}" method="POST" id="upgradeForm">
                    @csrf
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Payment Method</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="gcash">GCash</option>
                            <option value="paymaya">PayMaya</option>
                        </select>
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
                        <input type="text" class="form-control" id="reference_number" name="reference_number" placeholder="Enter your payment reference number" required>
                        <div class="form-text">Please enter the reference number from your payment transaction.</div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-crown me-2"></i>Upgrade Now
                        </button>
                    </div>
                </form>
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
</script>
@endsection
