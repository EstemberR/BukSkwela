@extends('tenant.layouts.app')

@section('title', 'Settings')

@section('content')
<div class="settings-container">
    <div class="settings-header">
        <h4 class="fs-5"><i class="fas fa-cog me-2 small"></i>User Settings</h4>
        <p class="text-muted small">Customize your BukSkwela experience</p>
    </div>
    
    @if(Auth::guard('admin')->check() || Auth::guard('staff')->check())
    <!-- User identification card -->
  
    
    <form id="settingsForm">
        @csrf
        <!-- Add hidden fields for user identification -->
        <input type="hidden" name="user_id" value="{{ Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::guard('staff')->id() }}">
        <input type="hidden" name="user_type" value="{{ Auth::guard('admin')->check() ? get_class(Auth::guard('admin')->user()) : get_class(Auth::guard('staff')->user()) }}">
        <input type="hidden" name="tenant_id" value="{{ tenant('id') }}">
        
        <div class="row g-2">
            <!-- Dark Mode and Cards in same row -->
            <div class="col-lg-4 col-md-12">
                <div class="settings-card">
                    <h5 class="card-title"><i class="fas fa-moon mr-2"></i>Dark Mode</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0 small">Enable system-wide dark theme</p>
                        @php
                            // Get the tenant's subscription plan
                            $tenantData = tenant();
                            $isPremium = $tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium';
                            
                            // Also check session
                            $isPremiumFromSession = session('is_premium') === true;
                            
                            // Use either source
                            $isPremium = $isPremium || $isPremiumFromSession;
                        @endphp
                        
                        @if($isPremium)
                        <label class="switch">
                            <input type="checkbox" id="darkModeToggle" name="dark_mode" value="1" {{ $settings->dark_mode ? 'checked' : '' }}>
                            <span class="slider">
                                <i class="fas fa-sun slider-icon light-icon"></i>
                                <i class="fas fa-moon slider-icon dark-icon"></i>
                            </span>
                        </label>
                        @else
                        <div class="premium-feature-badge">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-crown me-1"></i> Premium
                            </span>
                        </div>
                        @endif
                    </div>
                    @if($isPremium)
                    <p class="dark-mode-info small text-muted mt-2">Changes apply to all pages and will be saved with your user preferences.</p>
                    @else
                    <p class="premium-feature-info small text-muted mt-2">
                        <i class="fas fa-lock me-1"></i> Dark Mode is a premium feature. 
                        <a href="javascript:void(0)" onclick="openSubscriptionModal()" class="text-warning">Upgrade now</a>
                    </p>
                    @endif
                </div>
            </div>
            
            <!-- Card Styles -->
            <div class="col-lg-8 col-md-12">
                <div class="settings-card">
                    <h5 class="card-title mb-2"><i class="fas fa-credit-card mr-2"></i>Card Styles</h5>
                    <div class="card-style-container py-1 card-examples-wrapper">
                        <div class="row g-1">
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-square {{ $settings->card_style == 'square' ? 'active' : '' }}" data-card-style="square">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Square</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-rounded {{ $settings->card_style == 'rounded' ? 'active' : '' }}" data-card-style="rounded">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Rounded</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-glass {{ $settings->card_style == 'glass' ? 'active' : '' }}" data-card-style="glass">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Glassy</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="card_style" id="cardStyleInput" value="{{ $settings->card_style ?? 'square' }}">
                    </div>
                </div>
            </div>
            
            <!-- Font Settings -->
            <div class="col-lg-6 col-md-6">
                <div class="settings-card">
                    <h5 class="card-title mb-2"><i class="fas fa-font mr-2"></i>Font Style</h5>
                    <div class="form-group mb-2">
                        <select class="form-control form-control-sm font-select" id="fontSelect" name="font_family">
                            <option value="Work Sans, sans-serif" {{ $settings->font_family == "Work Sans, sans-serif" ? 'selected' : '' }}>Work Sans (Default)</option>
                            <option value="Open Sans, sans-serif" {{ $settings->font_family == "Open Sans, sans-serif" ? 'selected' : '' }}>Open Sans</option>
                            <option value="Roboto, sans-serif" {{ $settings->font_family == "Roboto, sans-serif" ? 'selected' : '' }}>Roboto</option>
                            <option value="Montserrat, sans-serif" {{ $settings->font_family == "Montserrat, sans-serif" ? 'selected' : '' }}>Montserrat</option>
                        </select>
                    </div>
                    <div class="font-size-preview p-2 border rounded" id="fontPreview" style="font-family: {{ $settings->font_family ?? 'Work Sans, sans-serif' }};">
                        <p class="mb-0 small">The quick brown fox jumps over the lazy dog.</p>
                    </div>
                </div>
            </div>
            
            <!-- Font Size -->
            <div class="col-lg-6 col-md-6">
                <div class="settings-card">
                    <h5 class="card-title mb-2"><i class="fas fa-text-height mr-2"></i>Font Size</h5>
                    <div class="form-group mb-2">
                        <select class="form-control form-control-sm font-size-select" id="fontSizeSelect" name="font_size">
                            <option value="12px" {{ $settings->font_size == '12px' ? 'selected' : '' }}>Small (12px)</option>
                            <option value="14px" {{ $settings->font_size == '14px' || !$settings->font_size ? 'selected' : '' }}>Normal (14px)</option>
                            <option value="16px" {{ $settings->font_size == '16px' ? 'selected' : '' }}>Medium (16px)</option>
                            <option value="18px" {{ $settings->font_size == '18px' ? 'selected' : '' }}>Large (18px)</option>
                        </select>
                    </div>
                    <div class="font-size-preview p-2 border rounded" id="sizePreview" style="font-size: {{ $settings->font_size ?? '14px' }};">
                        <p class="mb-0 small">Text will change size based on selection.</p>
                    </div>
                </div>
            </div>
            
            <!-- Save Button -->
            <div class="col-12 mt-2">
                <div class="d-flex justify-content-end">
                    <button type="button" class="btn btn-secondary btn-sm me-2" id="resetBtn">Reset</button>
                    <button type="submit" class="btn btn-primary btn-sm" id="saveBtn">Save Changes</button>
                </div>
            </div>
        </div>
    </form>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle mr-2"></i>
        You must be logged in to access your settings. 
        <a href="{{ route('tenant.login') }}" class="alert-link">Click here to login</a>.
    </div>
    @endif
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
    :root {
        --primary-color: #003366;
        --secondary-color: #FFD700;
        --background-color: #f0f7ff;
        --text-color: #001c38;
        --border-color: #d0d7de;
    }
    
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 1rem;
    }
    
    .settings-header {
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--border-color);
    }
    
    /* Compact settings header */
    .settings-header h4 {
        margin-bottom: 0.25rem;
        font-weight: 600;
    }
    
    .settings-header p {
        margin-bottom: 0.25rem;
        font-size: 0.8rem;
    }
    
    .settings-header i.fa-cog {
        font-size: 0.9rem;
    }
    
    .settings-card {
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        height: 100%;
    }
    
    .settings-card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .settings-card .card-title {
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: var(--primary-color);
        font-size: 1rem;
    }
    
    /* Toggle switch styling */
    .switch {
        position: relative;
        display: inline-block;
        width: 60px;
        height: 30px;
    }

    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 5px;
    }
    
    .slider-icon {
        z-index: 1;
        font-size: 12px;
        transition: color 0.3s ease;
    }
    
    .light-icon {
        color: #FFD700;
        margin-left: 3px;
    }
    
    .dark-icon {
        color: #6c757d;
        margin-right: 3px;
    }
    
    input:checked + .slider .light-icon {
        color: #6c757d;
    }
    
    input:checked + .slider .dark-icon {
        color: #ffffff;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 24px;
        width: 24px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: var(--primary-color, #003366);
    }

    input:focus + .slider {
        box-shadow: 0 0 1px var(--primary-color, #003366);
    }

    input:checked + .slider:before {
        transform: translateX(29px);
    }
    
    /* Dark mode specific card styling */
    body.dark-mode .settings-card {
        background-color: #1F2937;
        color: #f3f4f6;
        border-color: #374151;
    }
    
    body.dark-mode .settings-card .card-title {
        color: #60A5FA;
    }
    
    body.dark-mode .card-example {
        background-color: #ffffff !important;
        color: #111827 !important;
        border-color: #e5e7eb !important;
    }
    
    body.dark-mode .card-example i,
    body.dark-mode .card-example h5 {
        color: #111827 !important;
    }
    
    body.dark-mode .card-example.active {
        border-color: rgb(59, 130, 246) !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.5) !important;
    }
    
    body.dark-mode .card-example-glass {
        background-color: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(10px) !important;
    }
    
    body.dark-mode .card-style-container {
        background-color: #ffffff !important;
        border-radius: 0.5rem;
        padding: 1rem !important;
        margin: 1rem 0 !important;
    }
    
    body.dark-mode .font-size-preview,
    body.dark-mode .card-style-container {
        background-color: #374151;
        color: #f3f4f6;
        border-color: #4B5563;
    }
    
    .dark-mode-info {
        font-style: italic;
        opacity: 0.8;
    }
    
    /* Card examples */
    .card-example {
        height: 120px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
        background-color: white;
        color: var(--text-color);
    }
    
    .card-example.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 51, 102, 0.2);
    }
    
    .card-example:hover {
        transform: scale(1.02);
    }
    
    .card-example i {
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }
    
    .card-example h5 {
        font-size: 0.875rem;
        margin-bottom: 0;
    }
    
    /* Square card style */
    .card-example-square {
        border-radius: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        background: linear-gradient(to bottom, #ffffff, var(--background-color));
    }
    
    /* Rounded card style */
    .card-example-rounded {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
        background: linear-gradient(to bottom right, var(--background-color), #e6f6fe);
    }
    
    /* Glass card style */
    .card-example-glass {
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    
    /* Card style container */
    .card-style-container {
        background: linear-gradient(135deg, var(--background-color) 0%, #c3cfe2 100%);
        padding: 0.5rem;
        border-radius: 6px;
        margin-bottom: 0;
    }
    
    /* Font size preview */
    .font-size-preview {
        margin-top: 0.5rem;
        transition: all 0.3s ease;
        background-color: white;
        border: 1px solid var(--border-color) !important;
    }
    
    /* Buttons */
    .btn-primary {
        background-color: var(--primary-color, #003366);
        border-color: var(--primary-color, #003366);
    }
    
    .btn-primary:hover {
        background-color: var(--primary-hover, #002244);
        border-color: var(--primary-hover, #002244);
    }
    
    .btn-secondary {
        background-color: var(--secondary-color, #FFD700);
        border-color: var(--secondary-color, #FFD700);
        color: var(--text-color, #001c38);
    }
    
    .btn-secondary:hover {
        background-color: #e6c200;
        border-color: #e6c200;
        color: var(--text-color, #001c38);
    }
    
    /* Dark mode button overrides */
    body.dark-mode .btn-primary {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
    
    body.dark-mode .btn-primary:hover {
        background-color: #2563EB;
        border-color: #2563EB;
    }
    
    body.dark-mode .btn-secondary {
        background-color: #4B5563;
        border-color: #4B5563;
        color: #F3F4F6;
    }
    
    body.dark-mode .btn-secondary:hover {
        background-color: #6B7280;
        border-color: #6B7280;
        color: #F3F4F6;
    }
    
    /* Toast messages */
    .settings-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        padding: 12px 20px;
        max-width: 300px;
        border-left: 4px solid var(--primary-color);
        opacity: 0;
        transform: translateY(-20px);
        transition: all 0.3s ease;
    }
    
    .settings-toast.show {
        opacity: 1;
        transform: translateY(0);
    }
    
    .settings-toast.success {
        border-left-color: #28a745;
    }
    
    .settings-toast.error {
        border-left-color: #dc3545;
    }
    
    /* User info card styling */
    .user-info-card .card {
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .user-info-card .card:hover {
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
    }
    
    .user-info-card .badge {
        font-weight: 500;
        padding: 0.5em 0.75em;
        margin-left: 0.5rem;
    }
    
    /* Dark mode form elements */
    body.dark-mode input.form-control,
    body.dark-mode select.form-control,
    body.dark-mode textarea.form-control {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode input.form-control:focus,
    body.dark-mode select.form-control:focus,
    body.dark-mode textarea.form-control:focus {
        background-color: #2d3748;
        border-color: #63b3ed;
        box-shadow: 0 0 0 0.2rem rgba(66, 153, 225, 0.25);
        color: #e2e8f0;
    }
    
    body.dark-mode input.form-control::placeholder {
        color: #a0aec0;
    }
    
    body.dark-mode .form-label {
        color: #e2e8f0;
    }
    
    body.dark-mode .form-text,
    body.dark-mode .text-muted {
        color: #a0aec0 !important;
    }
    
    body.dark-mode .nav-tabs {
        border-bottom-color: #4a5568;
    }
    
    body.dark-mode .nav-tabs .nav-link {
        color: #a0aec0;
    }
    
    body.dark-mode .nav-tabs .nav-link:hover {
        border-color: #4a5568 #4a5568 #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode .nav-tabs .nav-link.active {
        background-color: #2d3748;
        border-color: #4a5568 #4a5568 #2d3748;
        color: #e2e8f0;
    }
    
    body.dark-mode hr {
        border-top-color: #4a5568;
    }
    
    body.dark-mode .table {
        color: #e2e8f0;
    }
    
    body.dark-mode .table thead th {
        border-bottom-color: #4a5568;
        color: #a0aec0;
    }
    
    body.dark-mode .table td,
    body.dark-mode .table th {
        border-top-color: #4a5568;
    }
    
    /* Dark mode alert styles */
    body.dark-mode .alert {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode .alert-success {
        background-color: rgba(56, 161, 105, 0.1);
        border-color: rgba(56, 161, 105, 0.4);
        color: #9ae6b4;
    }
    
    body.dark-mode .alert-danger {
        background-color: rgba(245, 101, 101, 0.1);
        border-color: rgba(245, 101, 101, 0.4);
        color: #feb2b2;
    }
    
    body.dark-mode .alert-warning {
        background-color: rgba(236, 201, 75, 0.1);
        border-color: rgba(236, 201, 75, 0.4);
        color: #faf089;
    }
    
    body.dark-mode .alert-info {
        background-color: rgba(66, 153, 225, 0.1);
        border-color: rgba(66, 153, 225, 0.4);
        color: #bee3f8;
    }
    
    /* Dark mode button styles */
    body.dark-mode .btn-light {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode .btn-light:hover {
        background-color: #4a5568;
        border-color: #718096;
        color: #e2e8f0;
    }
    
    body.dark-mode .btn-outline-secondary {
        border-color: #4a5568;
        color: #a0aec0;
    }
    
    body.dark-mode .btn-outline-secondary:hover {
        background-color: #4a5568;
        color: #e2e8f0;
    }
    
    /* Dark mode card styles */
    body.dark-mode .card {
        background-color: #2d3748;
        border-color: #4a5568;
    }
    
    body.dark-mode .card-header {
        background-color: rgba(45, 55, 72, 0.7);
        border-bottom-color: #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode .card-footer {
        background-color: rgba(45, 55, 72, 0.7);
        border-top-color: #4a5568;
    }
    
    body.dark-mode .list-group-item {
        background-color: #2d3748;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    /* Specific card styles for the settings page */
    .card-examples-wrapper {
        background-color: #ffffff;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        margin: 1rem 0;
    }
    
    body.dark-mode .card-examples-wrapper {
        background-color: #ffffff !important;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3) !important;
        border: 1px solid #e5e7eb !important;
    }
    
    /* Force specific card example styles to remain the same in dark mode */
    body.dark-mode .card-example-square {
        background-color: #ffffff !important;
        border-radius: 0.25rem !important;
        border: 1px solid #e5e7eb !important;
    }
    
    body.dark-mode .card-example-rounded {
        background-color: #ffffff !important;
        border-radius: 1rem !important;
        border: 1px solid #e5e7eb !important;
    }
    
    body.dark-mode .card-example-glass {
        background-color: rgba(255, 255, 255, 0.8) !important;
        border-radius: 0.5rem !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
    }
    
    /* Card style indicators */
    body.dark-mode .card-example i {
        color: #4B5563 !important;
        font-size: 1.5rem !important;
    }
    
    body.dark-mode .card-example h5 {
        color: #111827 !important;
        font-weight: 500 !important;
        margin-top: 0.5rem !important;
    }
    
    /* Premium feature styles */
    .premium-feature-badge .badge {
        background-color: rgb(251, 191, 36) !important;
        color: rgb(3, 1, 43) !important;
        font-weight: 600;
        padding: 0.5rem 0.75rem;
        border-radius: 30px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .premium-feature-badge .badge:hover {
        background-color: rgb(245, 158, 11) !important;
        transform: scale(1.05);
    }
    
    .premium-feature-info {
        color: #6c757d;
        font-style: italic;
    }
    
    .premium-feature-info a {
        color: rgb(245, 158, 11);
        text-decoration: none;
        font-weight: 600;
        transition: all 0.2s ease;
    }
    
    .premium-feature-info a:hover {
        color: rgb(251, 191, 36);
        text-decoration: underline;
    }
    
    body.dark-mode .premium-feature-info {
        color: #a0aec0;
    }
    
    body.dark-mode .premium-feature-badge .badge {
        background-color: rgb(251, 191, 36) !important;
        color: rgb(17, 24, 39) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize select2 for dropdowns with search
        $('.font-select, .font-size-select').select2({
            width: '100%',
            minimumResultsForSearch: 0
        });
        
        // Card style selection
        $('.card-example').on('click', function() {
            const cardStyle = $(this).data('card-style');
            $('.card-example').removeClass('active');
            $(this).addClass('active');
            $('#cardStyleInput').val(cardStyle);
        });
        
        // Font family change
        $('#fontSelect').change(function() {
            let fontFamily = $(this).val();
            $('#fontPreview').css('font-family', fontFamily);
        });
        
        // Font size change
        $('#fontSizeSelect').change(function() {
            let fontSize = $(this).val();
            $('#sizePreview').css('font-size', fontSize);
        });
        
        // Check premium status helper function
    function isPremiumUser() {
            // Check for premium badge in DOM as a reliable indicator
            const hasPremiumBadge = $('.navbar-premium-indicator').length > 0 || $('.premium-indicator').length > 0;
            
            // Check localStorage
            const isPremiumLocal = localStorage.getItem('isPremium') === 'true';
            
            // Check cookie
            const isPremiumCookie = document.cookie.split('; ').find(row => row.startsWith('is_premium='));
            
            // PHP session value passed to JavaScript
            const isPremiumPHP = {{ $isPremium ? 'true' : 'false' }};
            
            return hasPremiumBadge || isPremiumLocal || isPremiumCookie || isPremiumPHP;
        }

        // Dark mode toggle
        $('#darkModeToggle').change(function() {
            if (!isPremiumUser()) {
                // If not premium, reset toggle and show upgrade message
                $(this).prop('checked', false);
                showToast('Dark Mode is a premium feature. Please upgrade to unlock it.', 'error');
                
                // Try to open subscription modal if it exists
        if (typeof openSubscriptionModal === 'function') {
            openSubscriptionModal();
                }
                return;
            }
            
            toggleDarkMode($(this).is(':checked'));
        });
        
        // Function to toggle dark mode
        function toggleDarkMode(isDark) {
            // If trying to enable dark mode, verify premium status again
            if (isDark && !isPremiumUser()) {
                showToast('Dark Mode is a premium feature. Please upgrade to unlock it.', 'error');
                $('#darkModeToggle').prop('checked', false);
                return;
            }
            
            // Apply to body
            if(isDark) {
                $('body').addClass('dark-mode');
            localStorage.setItem('darkMode', 'enabled');
        } else {
                $('body').removeClass('dark-mode');
            localStorage.setItem('darkMode', 'disabled');
        }
            
            // Update any iframe elements that might exist
            $('iframe').each(function() {
                try {
                    if (isDark) {
                        $(this).contents().find('body').addClass('dark-mode');
                    } else {
                        $(this).contents().find('body').removeClass('dark-mode');
                    }
                } catch(e) {
                    // Handle cross-origin issues silently
                }
            });
            
            // Dispatch a custom event for other scripts to listen for
            document.dispatchEvent(new CustomEvent('darkModeToggled', { 
                detail: { isDarkMode: isDark } 
            }));
        }
        
        // Toast message function
        function showToast(message, type = 'success') {
            const toast = $('<div class="settings-toast ' + type + '">' + message + '</div>');
            $('body').append(toast);
            
            setTimeout(function() {
                toast.addClass('show');
            }, 100);
            
            setTimeout(function() {
                toast.removeClass('show');
                
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 3000);
        }
        
        // Reset button
        $('#resetBtn').click(function() {
            if(confirm('Are you sure you want to reset all settings to default?')) {
                $('#darkModeToggle').prop('checked', false);
                $('.card-example').removeClass('active');
                $('.card-example-square').addClass('active');
                $('#cardStyleInput').val('square');
                $('#fontSelect').val('Work Sans, sans-serif').trigger('change');
                $('#fontSizeSelect').val('14px').trigger('change');
                $('#fontPreview').css('font-family', 'Work Sans, sans-serif');
                $('#sizePreview').css('font-size', '14px');
                $('body').removeClass('dark-mode');
            }
        });
        
        // Form submission
        $('#settingsForm').submit(function(e) {
            e.preventDefault();
            
            // Show loading state
            $('#saveBtn').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-2"></i> Saving...');
            
            // Collect form data
            const formData = $(this).serialize();
            
            // Send AJAX request with CSRF token in header
            $.ajax({
                url: "{{ route('tenant.settings.save') }}",
                type: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if(response.success) {
                        showToast(response.message, 'success');
                    } else {
                        showToast('An error occurred while saving settings.', 'error');
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                    showToast('Could not save settings. Please try again later.', 'error');
                },
                complete: function() {
                    // Reset button state
                    $('#saveBtn').prop('disabled', false).html('Save Changes');
                }
            });
        });
        
        // Apply current settings on page load
        $(function() {
            // First check if user is premium before applying dark mode
            const userIsPremium = isPremiumUser();
            
            // If premium, handle dark mode settings
            if (userIsPremium) {
                // Check for dark mode in localStorage and apply it
                const savedDarkMode = localStorage.getItem('darkMode');
                if (savedDarkMode === 'enabled') {
                    $('#darkModeToggle').prop('checked', true);
                    toggleDarkMode(true);
                } else if (savedDarkMode === 'disabled') {
                    $('#darkModeToggle').prop('checked', false);
                    toggleDarkMode(false);
                } else {
                    // If no saved preference, check the current checkbox state from database
                    toggleDarkMode($('#darkModeToggle').is(':checked'));
                }
            } else {
                // If not premium, ensure dark mode is disabled
                $('#darkModeToggle').prop('checked', false);
                $('body').removeClass('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
            
            // Log authentication info for debugging (remove in production)
            console.log('Current tenant: {{ tenant('id') }}');
            
            @if(Auth::guard('admin')->check())
                console.log('Admin authenticated: {{ Auth::guard('admin')->user()->name }}');
            @elseif(Auth::guard('staff')->check())
                console.log('Staff authenticated: {{ Auth::guard('staff')->user()->name }}');
            @else
                console.log('No authenticated user');
            @endif
        });
        
        // Ensure card examples maintain their styles in dark mode
        function preserveCardExamples() {
            if (document.body.classList.contains('dark-mode')) {
                $('.card-examples-wrapper').css({
                    'background-color': '#ffffff',
                    'box-shadow': '0 2px 8px rgba(0, 0, 0, 0.3)',
                    'border': '1px solid #e5e7eb'
                });
                
                $('.card-example').css({
                    'background-color': '#ffffff',
                    'color': '#111827',
                    'border-color': '#e5e7eb'
                });
                
                $('.card-example i, .card-example h5').css('color', '#111827');
                
                $('.card-example-glass').css({
                    'background-color': 'rgba(255, 255, 255, 0.8)',
                    'backdrop-filter': 'blur(10px)'
                });
            }
        }
        
        // Run initially
        preserveCardExamples();
        
        // Listen for dark mode toggle events
        document.addEventListener('darkModeToggled', function(e) {
            // Apply the styles after a short delay to ensure they take effect after dark mode changes
            setTimeout(preserveCardExamples, 50);
        });
        
        // Premium badge click - open subscription modal
        $('.premium-feature-badge').on('click', function() {
            if (typeof openSubscriptionModal === 'function') {
                openSubscriptionModal();
        }
    });
});
</script>
@endpush
@endsection
