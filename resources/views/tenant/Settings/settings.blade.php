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
  
    
    <div id="saveStatus" class="alert mt-3" style="display: none;"></div>

    <form id="settingsForm">
        @csrf
        <!-- Add hidden fields for user identification -->
        <input type="hidden" name="user_id" value="{{ Auth::guard('admin')->check() ? Auth::guard('admin')->id() : Auth::guard('staff')->id() }}">
        <input type="hidden" name="user_type" value="{{ Auth::guard('admin')->check() ? get_class(Auth::guard('admin')->user()) : get_class(Auth::guard('staff')->user()) }}">
        
        <!-- Ensure tenant ID is set correctly with fallbacks -->
        <input type="hidden" name="tenant_id" value="{{ tenant('id') ?: request()->segment(1) ?: (request()->query('tenant') ?: session('tenant_id')) }}">
        
        <div class="row g-2">
            <!-- Dark Mode and Cards in same row -->
            <div class="col-lg-4 col-md-12">
                <div class="settings-card">
                    <h5 class="card-title"><i class="fas fa-moon mr-2"></i>Dark Mode</h5>
                    <div class="d-flex justify-content-between align-items-center">
                        <p class="mb-0 small">Switch between light and dark themes</p>
                        @php
                            // Get the tenant's subscription plan
                            $tenantData = tenant();
                            
                            // Get subscription plan from tenant data
                            $isPremium = false;
                            
                            // Check tenant data
                            if ($tenantData && isset($tenantData->subscription_plan) && $tenantData->subscription_plan === 'premium') {
                                $isPremium = true;
                            }
                            
                            // Check session
                            if (session('is_premium') === true) {
                                $isPremium = true;
                            }
                            
                            // Check user's role/permissions - assuming administrators are premium
                            if (Auth::guard('admin')->check()) {
                                $isPremium = true;
                            }
                            
                            // Force premium to true for debugging/fixing
                            $isPremium = true;
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
                    <p class="dark-mode-info small text-muted mt-2">Dark mode reduces eye strain in low-light environments and helps conserve battery life on mobile devices.</p>
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
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0"><i class="fas fa-credit-card mr-2"></i>Card Styles</h5>
                        @if(!$isPremium)
                        <div class="premium-feature-badge">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-crown me-1"></i> Premium
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    @if($isPremium)
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
                                    <div class="go-corner">
                                        <div class="go-arrow">→</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="card_style" id="cardStyleInput" value="{{ $settings->card_style ?? 'square' }}">
                    </div>
                    <p class="small text-muted mt-2">Choose a card style for your dashboard cards and UI components.</p>
                    @else
                    <div class="card-style-container py-1 card-examples-wrapper disabled" style="pointer-events: none; opacity: 0.7;">
                        <div class="row g-1">
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-square active">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Square</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-rounded">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Rounded</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 col-sm-4 col-4">
                                <div class="card-example card-example-glass">
                                    <div class="text-center">
                                        <i class="fas fa-book mb-1"></i>
                                        <h5 class="small">Glassy</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="card_style" id="cardStyleInput" value="square">
                    </div>
                    <p class="premium-feature-info small text-muted mt-2">
                        <i class="fas fa-lock me-1"></i> Card style customization is a premium feature. 
                        <a href="javascript:void(0)" onclick="openSubscriptionModal()" class="text-warning">Upgrade now</a>
                    </p>
                    @endif
                </div>
            </div>
            
        
            
            <!-- Dashboard Layout Settings -->
            <div class="col-lg-12 col-md-12 mt-2">
                <div class="settings-card">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h5 class="card-title mb-0"><i class="fas fa-columns mr-2"></i>Dashboard Layout</h5>
                        @if($isPremium)
                        <a href="#" class="btn btn-sm btn-outline-primary" id="editLayoutBtn">
                            <i class="fas fa-edit me-1"></i>Edit Layout
                        </a>
                        @else
                        <div class="premium-feature-badge">
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-crown me-1"></i> Premium
                            </span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="layout-options">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="layout-option {{ $settings->dashboard_layout == 'standard' || !$settings->dashboard_layout ? 'active' : '' }}" data-layout="standard">
                                    <div class="layout-preview standard-layout">
                                        <div class="mini-cards">
                                            <div class="mini-card"></div>
                                            <div class="mini-card"></div>
                                            <div class="mini-card"></div>
                                            <div class="mini-card"></div>
                                        </div>
                                        <div class="mini-content">
                                            <div class="mini-section"></div>
                                            <div class="mini-section"></div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <p class="mb-0 small">Standard</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="layout-option {{ $settings->dashboard_layout == 'compact' ? 'active' : '' }}" data-layout="compact">
                                    <div class="layout-preview compact-layout">
                                        <div class="mini-sidebar"></div>
                                        <div class="mini-content-area">
                                            <div class="mini-cards-compact">
                                                <div class="mini-card-sm"></div>
                                                <div class="mini-card-sm"></div>
                                                <div class="mini-card-sm"></div>
                                            </div>
                                            <div class="mini-content-compact"></div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <p class="mb-0 small">Compact</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="layout-option {{ $settings->dashboard_layout == 'modern' ? 'active' : '' }}" data-layout="modern">
                                    <div class="layout-preview modern-layout">
                                        <div class="mini-sidebar"></div>
                                        <div class="mini-content-area">
                                            <div class="mini-header"></div>
                                            <div class="mini-modern-cards">
                                                <div class="mini-card-md"></div>
                                                <div class="mini-card-md"></div>
                                            </div>
                                            <div class="mini-modern-content"></div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-2">
                                        <p class="mb-0 small">Modern</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="dashboard_layout" id="dashboardLayoutInput" value="{{ $settings->dashboard_layout ?? 'standard' }}">
                    
                    @if(!$isPremium)
                    <p class="premium-feature-info small text-muted mt-2">
                        <i class="fas fa-lock me-1"></i> Dashboard layout customization is a premium feature. 
                        <a href="javascript:void(0)" onclick="openSubscriptionModal()" class="text-warning">Upgrade now</a>
                    </p>
                    @endif
                </div>
            </div>
            
            <!-- Save Button -->
            <div class="col-12 mt-3">
                <div class="d-flex justify-content-end">
                    <button type="button" id="resetBtn" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-undo me-1"></i>Reset to Defaults
                    </button>
                    <button type="submit" id="saveSettingsBtn" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Save Changes
                    </button>
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

<!-- Layout Editor Modal -->
<div class="modal fade" id="layoutEditorModal" tabindex="-1" aria-labelledby="layoutEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="layoutEditorModalLabel">
                    <i class="fas fa-columns me-2"></i>Dashboard Layout Editor
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row layout-editor-container">
                    <div class="col-md-3 layout-editor-sidebar">
                        <h6 class="mb-3">Available Components</h6>
                        <div class="layout-components-list">
                            <!-- Statistics Components -->
                            <h6 class="text-muted small mb-2">Statistics</h6>
                            @forelse($availableComponents['statistics'] ?? [] as $component)
                            <div class="layout-editor-item" draggable="true" data-component="{{ $component['type'] }}">
                                <i class="fas fa-{{ $component['icon'] ?? 'chart-bar' }} me-2"></i>{{ $component['title'] }}
                            </div>
                            @empty
                            <div class="text-muted small mb-2">No statistics components available</div>
                            @endforelse
                            
                            <!-- Content Components -->
                            <h6 class="text-muted small mb-2 mt-3">Content</h6>
                            @forelse($availableComponents['content'] ?? [] as $component)
                            <div class="layout-editor-item" draggable="true" data-component="{{ $component['type'] }}">
                                <i class="fas fa-{{ $component['icon'] ?? 'box' }} me-2"></i>{{ $component['title'] }}
                            </div>
                            @empty
                            <div class="text-muted small mb-2">No content components available</div>
                            @endforelse
                            
                            <!-- Bottom Section Components -->
                            @if(!empty($availableComponents['bottom']))
                            <h6 class="text-muted small mb-2 mt-3">Additional</h6>
                            @foreach($availableComponents['bottom'] as $component)
                            <div class="layout-editor-item" draggable="true" data-component="{{ $component['type'] }}">
                                <i class="fas fa-{{ $component['icon'] ?? 'cog' }} me-2"></i>{{ $component['title'] }}
                            </div>
                            @endforeach
                            @endif
                        </div>
                        <hr>
                        <h6 class="mb-3">Layout Settings</h6>
                        <div class="mb-3">
                            <label class="form-label">Sidebar Position</label>
                            <select class="form-select form-select-sm" id="sidebarPosition">
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Card Style</label>
                            <select class="form-select form-select-sm" id="cardStyle">
                                <option value="rounded">Rounded</option>
                                <option value="square">Square</option>
                                <option value="glass">Glass</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-9 layout-editor-main">
                        <div class="alert alert-info small">
                            <i class="fas fa-info-circle me-2"></i>Drag items from the left sidebar and drop them into the sections below to customize your dashboard layout.
                        </div>
                        <div class="layout-editor-preview">
                            <h6 class="mb-2">Statistics Section</h6>
                            <div class="dropzone" id="statistics-zone" data-zone="statistics">
                                <div class="text-muted text-center p-3 small" id="statistics-placeholder">
                                    <i class="fas fa-plus-circle me-2"></i>Drag statistic cards here
                                </div>
                                <div id="statistics-items" class="row g-2"></div>
                            </div>
                            
                            <h6 class="mb-2 mt-4">Main Content Section</h6>
                            <div class="dropzone" id="content-zone" data-zone="content">
                                <div class="text-muted text-center p-3 small" id="content-placeholder">
                                    <i class="fas fa-plus-circle me-2"></i>Drag content components here
                                </div>
                                <div id="content-items" class="row g-2"></div>
                            </div>
                            
                            <h6 class="mb-2 mt-4">Bottom Section</h6>
                            <div class="dropzone" id="bottom-zone" data-zone="bottom">
                                <div class="text-muted text-center p-3 small" id="bottom-placeholder">
                                    <i class="fas fa-plus-circle me-2"></i>Drag additional components here
                                </div>
                                <div id="bottom-items" class="row g-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveLayoutBtn">Save Layout</button>
                @if(app()->environment('local', 'development'))
                <!-- Debug button for development only -->
                <button type="button" class="btn btn-outline-info ms-2" id="debugLayoutBtn" title="View debug info">
                    <i class="fas fa-bug"></i>
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Layout Success Modal -->
<div class="modal fade" id="layoutSuccessModal" tabindex="-1" aria-labelledby="layoutSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="layoutSuccessModalLabel">
                    <i class="fas fa-check-circle me-2"></i>Layout Updated
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="fas fa-columns text-success" style="font-size: 3rem;"></i>
                </div>
                <p class="text-center">Your dashboard layout has been successfully updated to <span id="newLayoutName" class="fw-bold">Standard</span>.</p>
                <p class="text-center text-muted small">The changes will be visible the next time you visit the dashboard.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Card Style Success Modal -->
<div class="modal fade" id="cardStyleSuccessModal" tabindex="-1" aria-labelledby="cardStyleSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title" id="cardStyleSuccessModalLabel">Card Style Updated</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="buksu-icon-container mb-3 mx-auto">
                    <i class="fas fa-palette fa-3x"></i>
                </div>
                <h4 class="mb-3">Success!</h4>
                <p class="mb-1">Card style updated to <span id="newCardStyleName" class="fw-bold">Rounded</span>.</p>
                <p class="text-muted">Click Save to permanently apply these changes.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Font Style Success Modal -->
<div class="modal fade" id="fontStyleSuccessModal" tabindex="-1" aria-labelledby="fontStyleSuccessModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-navy text-white">
                <h5 class="modal-title" id="fontStyleSuccessModalLabel">Font Style Updated</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <div class="buksu-icon-container mb-3 mx-auto">
                    <i class="fas fa-font fa-3x"></i>
                </div>
                <h4 class="mb-3">Success!</h4>
                <p class="mb-1">Font style updated to <span id="newFontStyleName" class="fw-bold">Default</span>.</p>
                <p class="text-muted">Click Save to permanently apply these changes.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
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
        position: relative;
        background-color: #f2f8f9;
        border-radius: 4px;
        padding: 32px 24px;
        text-decoration: none;
        z-index: 0;
        overflow: hidden;
        border: 1px solid #f2f8f9;
        transition: all 0.3s ease;
    }
    
    .card-example-glass:before {
        content: "";
        position: absolute;
        z-index: -1;
        top: -16px;
        right: -16px;
        background: #00838d;
        height: 32px;
        width: 32px;
        border-radius: 32px;
        transform: scale(1);
        transform-origin: 50% 50%;
        transition: transform 0.25s ease-out;
    }
    
    .card-example-glass:hover:before {
        transform: scale(2.15);
    }
    
    .card-example-glass:hover {
        border: 1px solid #00838d;
        box-shadow: 0px 0px 999px 999px rgba(255, 255, 255, 0.5);
        z-index: 500;
    }
    
    .card-example-glass:hover .text-center {
        color: #fff;
    }
    
    .card-example-glass .go-corner {
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        width: 32px;
        height: 32px;
        overflow: hidden;
        top: 0;
        right: 0;
        background-color: #00838d;
        border-radius: 0 4px 0 32px;
        opacity: 0.7;
        transition: opacity 0.3s linear;
    }
    
    .card-example-glass:hover .go-corner {
        opacity: 1;
    }
    
    .card-example-glass .go-arrow {
        margin-top: -4px;
        margin-right: -4px;
        color: white;
        font-family: courier, sans;
    }
    
    /* Dark mode styles */
    body.dark-mode .card-example-glass {
        background-color: #1a1a1a;
        border-color: #2d2d2d;
    }
    
    body.dark-mode .card-example-glass:hover {
        border-color: #00838d;
        box-shadow: 0px 0px 999px 999px rgba(0, 0, 0, 0.5);
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
    
    /* Dashboard Layout Options */
    .layout-option {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
        height: 100%;
    }
    
    .layout-option:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .layout-option.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 51, 102, 0.2);
    }
    
    .layout-preview {
        height: 120px;
        background-color: #f9f9f9;
        border-radius: 4px;
        overflow: hidden;
        position: relative;
    }
    
    /* Standard Layout Preview */
    .standard-layout {
        display: flex;
        flex-direction: column;
        padding: 5px;
    }
    
    .mini-cards {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .mini-card {
        width: 23%;
        height: 30px;
        background-color: #e6e6e6;
        border-radius: 3px;
    }
    
    .mini-content {
        display: flex;
        justify-content: space-between;
        height: 70px;
    }
    
    .mini-section {
        width: 49%;
        background-color: #e6e6e6;
        border-radius: 3px;
    }
    
    /* Compact Layout Preview */
    .compact-layout {
        display: flex;
    }
    
    .mini-sidebar {
        width: 15%;
        height: 100%;
        background-color: #d1d1d1;
    }
    
    .mini-content-area {
        width: 85%;
        padding: 5px;
    }
    
    .mini-cards-compact {
        display: flex;
        justify-content: flex-start;
        margin-bottom: 5px;
    }
    
    .mini-card-sm {
        width: 30%;
        height: 25px;
        background-color: #e6e6e6;
        border-radius: 3px;
        margin-right: 5px;
    }
    
    .mini-content-compact {
        height: 80px;
        background-color: #e6e6e6;
        border-radius: 3px;
    }
    
    /* Modern Layout Preview */
    .modern-layout {
        display: flex;
    }
    
    .mini-header {
        height: 15px;
        background-color: #d1d1d1;
        margin-bottom: 5px;
        border-radius: 2px;
    }
    
    .mini-modern-cards {
        display: flex;
        justify-content: space-between;
        margin-bottom: 5px;
    }
    
    .mini-card-md {
        width: 48%;
        height: 40px;
        background-color: #e6e6e6;
        border-radius: 3px;
    }
    
    .mini-modern-content {
        height: 55px;
        background-color: #e6e6e6;
        border-radius: 3px;
    }
    
    /* Dark mode specific layout preview styles */
    body.dark-mode .layout-preview {
        background-color: #2d3748;
    }
    
    body.dark-mode .mini-card,
    body.dark-mode .mini-section,
    body.dark-mode .mini-card-sm,
    body.dark-mode .mini-content-compact,
    body.dark-mode .mini-card-md,
    body.dark-mode .mini-modern-content {
        background-color: #4a5568;
    }
    
    body.dark-mode .mini-sidebar,
    body.dark-mode .mini-header {
        background-color: #1a202c;
    }
    
    /* Layout Editor Modal */
    .layout-editor-container {
        min-height: 500px;
    }
    
    .layout-editor-sidebar {
        background-color: #f8f9fa;
        border-right: 1px solid #dee2e6;
        height: 100%;
        padding: 15px;
    }
    
    .layout-editor-main {
        padding: 15px;
    }
    
    .layout-editor-item {
        background-color: #fff;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 10px;
        margin-bottom: 10px;
        cursor: move;
        transition: all 0.2s ease;
    }
    
    .layout-editor-item:hover {
        background-color: #f8f9fa;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .layout-editor-preview {
        min-height: 400px;
        border: 2px dashed #dee2e6;
        border-radius: 4px;
        padding: 15px;
    }
    
    .layout-editor-preview .dropzone {
        min-height: 80px;
        border: 1px dashed #ced4da;
        border-radius: 4px;
        margin-bottom: 15px;
        padding: 10px;
        background-color: #f8f9fa;
    }
    
    .layout-editor-preview .dropzone.active {
        background-color: rgba(0, 123, 255, 0.1);
        border-color: #007bff;
    }
    
    /* Dark mode editor */
    body.dark-mode .layout-editor-sidebar {
        background-color: #2d3748;
        border-color: #4a5568;
    }
    
    body.dark-mode .layout-editor-item {
        background-color: #3a4a5e;
        border-color: #4a5568;
        color: #e2e8f0;
    }
    
    body.dark-mode .layout-editor-item:hover {
        background-color: #4a5568;
    }
    
    body.dark-mode .layout-editor-preview {
        border-color: #4a5568;
    }
    
    body.dark-mode .layout-editor-preview .dropzone {
        background-color: #2d3748;
        border-color: #4a5568;
    }
    
    /* Layout Success Modal styling */
    #layoutSuccessModal .modal-header {
        border-bottom: 0;
    }
    
    #layoutSuccessModal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
    }
    
    #layoutSuccessModal .modal-footer {
        border-top: 0;
    }
    
    #layoutSuccessModal .fas.fa-columns {
        background-color: rgba(40, 167, 69, 0.1);
        border-radius: 50%;
        padding: 1.5rem;
        color: #28a745;
    }
    
    /* Dark mode layout success modal */
    body.dark-mode #layoutSuccessModal .modal-content {
        background-color: #1F2937;
        color: #f3f4f6;
    }
    
    body.dark-mode #layoutSuccessModal .modal-header {
        background-color: #10B981 !important; /* A more modern green for dark mode */
    }
    
    body.dark-mode #layoutSuccessModal .text-muted {
        color: #9CA3AF !important;
    }
    
    body.dark-mode #layoutSuccessModal .fas.fa-columns {
        background-color: rgba(16, 185, 129, 0.1);
        color: #10B981;
    }
    
    body.dark-mode #layoutSuccessModal .btn-secondary {
        background-color: #4B5563;
        border-color: #4B5563;
        color: #F3F4F6;
    }
    
    body.dark-mode #layoutSuccessModal .btn-primary {
        background-color: #3B82F6;
        border-color: #3B82F6;
    }
    
    /* Card Style Success Modal styling */
    #cardStyleSuccessModal .modal-header {
        border-bottom: 0;
    }

    #cardStyleSuccessModal .modal-content {
        border-radius: 1rem;
        overflow: hidden;
        border: 1px solid rgba(0, 51, 102, 0.2);
    }

    #cardStyleSuccessModal .modal-footer {
        border-top: 0;
    }

    .buksu-icon-container {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: linear-gradient(135deg, #003366, #0066cc);
        position: relative;
        overflow: hidden;
    }

    .buksu-icon-container::before {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle at top right, rgba(255, 215, 0, 0.5), transparent 60%);
    }

    .buksu-icon-container .fas {
        color: #FFD700;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
    }

    #cardStyleSuccessModal #newCardStyleName {
        color: #003366;
        border-bottom: 2px solid #FFD700;
        padding-bottom: 2px;
    }

    /* Dark mode card style success modal */
    body.dark-mode #cardStyleSuccessModal .modal-content {
        background-color: #1F2937;
        color: #f3f4f6;
        border-color: #003366;
    }

    body.dark-mode #cardStyleSuccessModal .modal-header {
        background-color: #003366 !important;
    }

    body.dark-mode #cardStyleSuccessModal .text-muted {
        color: #9CA3AF !important;
    }

    body.dark-mode #cardStyleSuccessModal #newCardStyleName {
        color: #FFD700;
        border-bottom: 2px solid #FFD700;
    }

    body.dark-mode #cardStyleSuccessModal .btn-outline-secondary {
        color: #e2e8f0;
        border-color: #4B5563;
    }

    body.dark-mode #cardStyleSuccessModal .btn-outline-secondary:hover {
        background-color: #4B5563;
        color: #e2e8f0;
    }

    /* Card examples - static previews without hover effects */
    .card-example {
        height: 120px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        cursor: pointer;
        border: 2px solid transparent;
        background-color: white;
        color: var(--text-color);
    }
    
    .card-example.active {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 2px rgba(0, 51, 102, 0.2);
    }
    
    /* Square card preview */
    .card-example-square {
        border-radius: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
    }
    
    /* Rounded card preview */
    .card-example-rounded {
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid var(--border-color);
    }
    
    /* Glass card preview */
    .card-example-glass {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(10px) !important;
        -webkit-backdrop-filter: blur(10px) !important;
        border: 1px solid rgba(255, 255, 255, 0.3) !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1) !important;
        position: relative;
        overflow: hidden;
    }
    
    /* Dark mode card example styles */
    body.dark-mode .card-example-glass {
        background: rgba(31, 41, 55, 0.7) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Debug utility for dark mode
        function logDarkModeState(label) {
            const bodyHasDarkMode = $('body').hasClass('dark-mode');
            const toggleChecked = $('#darkModeToggle').is(':checked');
            const localStorageSetting = localStorage.getItem('darkMode');
            
            console.log(`Dark Mode Debug ${label || ''}:`);
            console.log('- Body has dark-mode class:', bodyHasDarkMode);
            console.log('- Toggle is checked:', toggleChecked);
            console.log('- localStorage setting:', localStorageSetting);
        }
        
        // Log initial state
        logDarkModeState('initial');
        
        // Initialize dark mode from localStorage or server settings
        const savedDarkMode = localStorage.getItem('darkMode');
        const darkModeToggle = document.getElementById('darkModeToggle');
        
        if (savedDarkMode === 'enabled') {
            $('body').addClass('dark-mode');
            if (darkModeToggle) darkModeToggle.checked = true;
        } else if (savedDarkMode === 'disabled') {
            $('body').removeClass('dark-mode');
            if (darkModeToggle) darkModeToggle.checked = false;
        } else {
            // If no localStorage setting, use the server setting (from the checkbox state)
            if (darkModeToggle && darkModeToggle.checked) {
                $('body').addClass('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
            } else {
                $('body').removeClass('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
            }
        }
        
        // Log state after initialization
        logDarkModeState('after initialization');
        
        // Add dark mode toggle listener for immediate feedback
        $('#darkModeToggle').on('change', function() {
            if ($(this).is(':checked')) {
                $('body').addClass('dark-mode');
                localStorage.setItem('darkMode', 'enabled');
                // Dispatch event for other components
                document.dispatchEvent(new CustomEvent('darkModeToggled', {
                    detail: { isDarkMode: true }
                }));
            } else {
                $('body').removeClass('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
                // Dispatch event for other components
                document.dispatchEvent(new CustomEvent('darkModeToggled', {
                    detail: { isDarkMode: false }
                }));
            }
            
            // Log state after toggle
            logDarkModeState('after toggle change');
        });
        
        // Initialize settings form
        const settingsForm = $('#settingsForm');
        const saveStatus = $('#saveStatus');
        
        // Sync card style from localStorage on page load
        try {
            const savedCardStyle = localStorage.getItem('selectedCardStyle');
            if (savedCardStyle) {
                // Update hidden input value
                $('#cardStyleInput').val(savedCardStyle);
                
                // Update active class on card style options
                $('.card-example').removeClass('active');
                $(`.card-example-${savedCardStyle}`).addClass('active');
                
                console.log('Synced card style from localStorage:', savedCardStyle);
            } else {
                // If no localStorage value, save the server value to localStorage
                const serverCardStyle = $('#cardStyleInput').val();
                if (serverCardStyle) {
                    localStorage.setItem('selectedCardStyle', serverCardStyle);
                    console.log('Saved server card style to localStorage:', serverCardStyle);
                }
            }
        } catch (e) {
            console.error('Error syncing card style from localStorage:', e);
        }
        
        // Add tenant ID to form data before submit
        settingsForm.on('submit', function(e) {
            e.preventDefault();
            
            // Show loading state
            saveStatus.removeClass('alert-success alert-danger').addClass('alert-info').html('<i class="fas fa-spinner fa-spin"></i> Saving settings...').show();
            
            // Get form data
            const formData = new FormData(this);
            
            // Extract tenant ID from form data or URL
            let tenantId = formData.get('tenant_id');
            
            // If not in form, try to get from URL
            if (!tenantId) {
                // Try to get from URL path segment
                const pathSegments = window.location.pathname.split('/');
                if (pathSegments.length > 1 && pathSegments[1]) {
                    tenantId = pathSegments[1];
                }
                
                // Or from query parameter
                if (!tenantId) {
                    const urlParams = new URLSearchParams(window.location.search);
                    if (urlParams.has('tenant')) {
                        tenantId = urlParams.get('tenant');
                    }
                }
                
                // Add to form data if found
                if (tenantId) {
                    formData.append('tenant_id', tenantId);
                }
            }
            
            // Build the base URL correctly
            const origin = window.location.origin;
            const path = window.location.pathname;
            
            // Debug URL construction
            console.log('Current path:', path);
            console.log('Origin:', origin);
            console.log('Tenant ID:', tenantId);
            
            // Fix: Construct the URL based on the current URL pattern
            let saveUrl;
            
            // Determine if we're on a path-based tenant or subdomain tenant
            const pathPattern = new RegExp(`^\\/${tenantId}\\/`);
            const isPathBased = pathPattern.test(path);
            const isSubdomain = origin.includes(`${tenantId}.`);
            
            if (isPathBased) {
                // Path-based: /tenant-id/settings/save
                saveUrl = `${origin}/${tenantId}/settings/save`;
            } else if (isSubdomain) {
                // Subdomain-based: tenant-id.domain.com/settings/save
                saveUrl = `${origin}/settings/save`;
            } else {
                // Fallback
                saveUrl = `${origin}/${tenantId}/settings/save`;
            }
            
            console.log('Sending request to:', saveUrl, 'Path based:', isPathBased, 'Subdomain:', isSubdomain);
            
            // Convert FormData to JSON
            const jsonData = {};
            formData.forEach((value, key) => {
                // Handle special case for checkboxes
                if (key === 'dark_mode' || key === 'notification_sound' || key === 'email_notifications') {
                    jsonData[key] = value === 'on' ? true : value === 'true';
                } else {
                    jsonData[key] = value;
                }
            });
            
            console.log('Sending settings to server:', jsonData);
            
            // Use fetch with the correct URL
            fetch(saveUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Tenant-ID': tenantId || ''
                },
                body: JSON.stringify(jsonData)
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 400) {
                        throw new Error('Tenant context missing or invalid. Please refresh the page and try again.');
                    } else if (response.status === 401) {
                        throw new Error('You are not authenticated. Please log in and try again.');
                    } else if (response.status === 422) {
                        throw new Error('Form validation failed. Please check your inputs.');
                    }
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    saveStatus.removeClass('alert-info alert-danger').addClass('alert-success')
                        .html('<i class="fas fa-check"></i> ' + data.message).show();
                    
                    // Handle dark mode toggle based on the saved setting
                    const darkModeEnabled = $('#darkModeToggle').is(':checked');
                    
                    logDarkModeState('before applying from form save');
                    console.log('Dark mode from form save:', darkModeEnabled);
                    
                    // Apply dark mode immediately to current page
                    if (darkModeEnabled) {
                        $('body').addClass('dark-mode');
                        localStorage.setItem('darkMode', 'enabled');
                        
                        // Dispatch event for other components
                        document.dispatchEvent(new CustomEvent('darkModeToggled', {
                            detail: { isDarkMode: true }
                        }));
                    } else {
                        $('body').removeClass('dark-mode');
                        localStorage.setItem('darkMode', 'disabled');
                        
                        // Dispatch event for other components
                        document.dispatchEvent(new CustomEvent('darkModeToggled', {
                            detail: { isDarkMode: false }
                        }));
                    }
                    
                    // Log state after dark mode is applied
                    logDarkModeState('after applying from form save');
                    
                    // Apply card style if it was changed
                    const cardStyle = $('#cardStyleInput').val();
                    if (cardStyle) {
                        localStorage.setItem('selectedCardStyle', cardStyle);
                        // Apply styles immediately
                        applyCardStyleToPage(cardStyle);
                        
                        // Dispatch events for other tabs
                        window.dispatchEvent(new StorageEvent('storage', {
                            key: 'selectedCardStyle',
                            newValue: cardStyle
                        }));
                        
                        document.dispatchEvent(new CustomEvent('cardStyleChanged', {
                            detail: { cardStyle: cardStyle }
                        }));
                    }
                    
                    // Hide message after delay
                    setTimeout(() => {
                        saveStatus.fadeOut();
                    }, 3000);
                } else {
                    saveStatus.removeClass('alert-info alert-success').addClass('alert-danger')
                        .html('<i class="fas fa-exclamation-triangle"></i> ' + (data.message || 'An error occurred')).show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                saveStatus.removeClass('alert-info alert-success').addClass('alert-danger')
                    .html('<i class="fas fa-exclamation-triangle"></i> ' + error.message).show();
            });
        });
        
        // Card style selection
        $('.card-example').on('click', function() {
            // Check if user is premium
            @if(!$isPremium)
            // Show premium upgrade message for non-premium users
            saveStatus.removeClass('alert-success alert-danger').addClass('alert-warning')
                .html('<i class="fas fa-crown"></i> Card style customization is a premium feature. <a href="javascript:void(0)" onclick="openSubscriptionModal()" class="alert-link">Upgrade now</a>').show();
                
            setTimeout(() => {
                saveStatus.fadeOut();
            }, 3000);
                return;
            @endif
            
            const cardStyle = $(this).data('card-style');
            $('.card-example').removeClass('active');
            $(this).addClass('active');
            $('#cardStyleInput').val(cardStyle);
            
            // Save to localStorage for immediate effect on all dashboards
            localStorage.setItem('selectedCardStyle', cardStyle);
            console.log('Card style set to:', cardStyle);
            
            // Apply styles immediately to current page
            applyCardStyleToPage(cardStyle);
            
            // Dispatch both storage and custom events for immediate cross-tab effect
            window.dispatchEvent(new StorageEvent('storage', {
                key: 'selectedCardStyle',
                newValue: cardStyle
            }));
            
            // Dispatch a custom event that our dashboards can listen for
            document.dispatchEvent(new CustomEvent('cardStyleChanged', {
                detail: { cardStyle: cardStyle }
            }));

            // Show success modal with the updated card style
            updateCardStyleSuccessModal(cardStyle);
        });
        
        // Function to apply card style to current page
        function applyCardStyleToPage(style) {
            // Remove existing style classes
            $('body').removeClass('card-style-square card-style-rounded card-style-glass');
            
            // Add the selected style class
            if (style) {
                $('body').addClass('card-style-' + style);
            }
            
            // Update any preview elements
            updateCardStylePreview(style);
        }
        
        // Apply saved card style on page load
        try {
            const savedCardStyle = localStorage.getItem('selectedCardStyle');
            if (savedCardStyle) {
                applyCardStyleToPage(savedCardStyle);
            }
        } catch (e) {
            console.error('Error applying saved card style:', e);
        }
        
        // Layout option selection
        $('.layout-option').on('click', function() {
            const layoutType = $(this).data('layout');
            console.log('Layout option selected:', layoutType);
            
            $('.layout-option').removeClass('active');
            $(this).addClass('active');
            $('#dashboardLayoutInput').val(layoutType);
            
            // Save to localStorage for immediate effect on dashboard
            try {
                localStorage.setItem('selectedDashboardLayout', layoutType);
                console.log('Layout type saved to localStorage:', layoutType);
                
                // Update the layout name in the success modal and show it
                updateLayoutSuccessModal(layoutType, true);
            } catch (e) {
                console.error('Failed to save layout type to localStorage:', e);
            }
            
            // Show success message in the alert
            saveStatus.removeClass('alert-danger alert-info').addClass('alert-success')
                .html('<i class="fas fa-check"></i> Dashboard layout type updated. Click Save to apply changes.').show();
                
            // If dashboard is open in another tab, this will make the changes take effect immediately
            try {
                // Dispatch a storage event manually to update other tabs
                window.dispatchEvent(new StorageEvent('storage', {
                    key: 'selectedDashboardLayout',
                    newValue: layoutType
                }));
                console.log('Storage event dispatched for immediate update');
            } catch (e) {
                console.error('Error dispatching storage event:', e);
            }
                
            // Hide message after delay
            setTimeout(() => {
                saveStatus.fadeOut();
            }, 3000);
        });
        
        // Function to update the success modal with the correct layout name
        // showModal parameter determines whether to show the modal (default: false)
        function updateLayoutSuccessModal(layoutType, showModal = false) {
            const layoutNameElement = document.getElementById('newLayoutName');
            if (layoutNameElement) {
                let formattedName = 'Standard';
                
                switch(layoutType) {
                    case 'compact':
                        formattedName = 'Compact';
                        break;
                    case 'modern':
                        formattedName = 'Modern';
                        break;
                    default:
                        formattedName = 'Standard';
                }
                
                layoutNameElement.textContent = formattedName;
                
                // Only show the modal if explicitly requested
                if (showModal) {
                    const layoutSuccessModal = new bootstrap.Modal(document.getElementById('layoutSuccessModal'));
                    layoutSuccessModal.show();
                }
            }
        }
        
        // Initialize drag and drop when modal is shown
        $('#layoutEditorModal').on('shown.bs.modal', function() {
            initDragAndDrop();
        });
        
        // Reset button
        $('#resetBtn').click(function() {
            if (confirm('Are you sure you want to reset all settings to default?')) {
                $('#darkModeToggle').prop('checked', false);
                $('.card-example').removeClass('active');
                $('.card-example-square').addClass('active');
                $('#cardStyleInput').val('square');
                $('#fontSelect').val('Work Sans, sans-serif').trigger('change');
                $('#fontSizeSelect').val('14px').trigger('change');
                $('#fontPreview').css('font-family', 'Work Sans, sans-serif');
                $('#sizePreview').css('font-size', '14px');
                $('body').removeClass('dark-mode');
                
                // Also clear localStorage
                localStorage.removeItem('darkMode');
                
                saveStatus.removeClass('alert-danger alert-success').addClass('alert-info')
                    .html('<i class="fas fa-info-circle"></i> Settings reset to defaults. Click Save to apply changes.').show();
        }
    });

    // Sync dashboard layout with localStorage on page load
    try {
        const savedLayout = localStorage.getItem('selectedDashboardLayout');
        if (savedLayout) {
            // Update hidden input value
            $('#dashboardLayoutInput').val(savedLayout);
            
            // Update active class on layout option
            $('.layout-option').removeClass('active');
            $(`.layout-option[data-layout="${savedLayout}"]`).addClass('active');
            
            // Initialize success modal with current layout name but don't show it
            updateLayoutSuccessModal(savedLayout, false);
            
            console.log('Synced layout from localStorage:', savedLayout);
        }
    } catch (e) {
        console.error('Error syncing layout from localStorage:', e);
    }

    // Listen for dashboard layout changes from other tabs
    window.addEventListener('storage', function(e) {
        if (e.key === 'selectedDashboardLayout') {
            console.log('Detected dashboard layout change:', e.newValue);
            
            // Update hidden input value
            $('#dashboardLayoutInput').val(e.newValue);
            
            // Update active class on layout option
            $('.layout-option').removeClass('active');
            $(`.layout-option[data-layout="${e.newValue}"]`).addClass('active');
            
            // Update the layout name in the success modal but don't show it
            updateLayoutSuccessModal(e.newValue, false);
        } else if (e.key === 'selectedCardStyle') {
            console.log('Detected card style change:', e.newValue);
            
            // Update hidden input value
            $('#cardStyleInput').val(e.newValue);
            
            // Update active class on card style options
            $('.card-example').removeClass('active');
            $(`.card-example-${e.newValue}`).addClass('active');
        }
    });

    // Initialize dashboard link
    const tenantId = $('input[name="tenant_id"]').val();
    const origin = window.location.origin;
    const dashboardUrl = tenantId ? `${origin}/${tenantId}/dashboard` : `${origin}/dashboard`;
    $('#dashboardLink').attr('href', dashboardUrl);

    // Helper function to show toast notifications
    function showToast(type, message) {
        // Create toast element
        const toast = $('<div class="settings-toast ' + type + '">' +
                        '<i class="fas ' + (type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle') + '"></i> ' +
                        message +
                        '</div>');
        
        // Add to body
        $('body').append(toast);
        
        // Fade in
        setTimeout(() => {
            toast.addClass('show');
        }, 10);
        
        // Fade out and remove after delay
        setTimeout(() => {
            toast.removeClass('show');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 3000);
    }
    
    // Function to update card style preview (used by the card style selector)
    function updateCardStylePreview(cardStyle) {
        // Update any preview elements that need to show the selected card style
        $('.card-style-preview').attr('data-style', cardStyle);
    }

    // Function to update and show card style success modal
    function updateCardStyleSuccessModal(cardStyle) {
        // Format the card style name to be more presentable
        let cardStyleName;
        switch(cardStyle) {
            case 'rounded':
                cardStyleName = 'Rounded';
                break;
            case 'glass':
                cardStyleName = 'Glassy';
                break;
            default:
                cardStyleName = 'Square';
        }
        
        // Update the modal content with the correct style name
        document.getElementById('newCardStyleName').textContent = cardStyleName;
        
        // Update dashboard link
        const tenantId = document.querySelector('[name="tenant_id"]').value;
        const dashboardLink = document.getElementById('viewDashboardLink');
        dashboardLink.href = `/${tenantId}/dashboard`;
        
        // Show the modal
        const cardStyleModal = new bootstrap.Modal(document.getElementById('cardStyleSuccessModal'));
        cardStyleModal.show();
    }

    // Function to update and show font style success modal
    function updateFontStyleSuccessModal(fontStyle) {
        // Format the font style name to be more presentable
        let fontStyleName;
        switch(fontStyle) {
            case 'montserrat':
                fontStyleName = 'Montserrat';
                break;
            case 'roboto':
                fontStyleName = 'Roboto';
                break;
            case 'lato':
                fontStyleName = 'Lato';
                break;
            case 'opensans':
                fontStyleName = 'Open Sans';
                break;
            default:
                fontStyleName = 'Default';
        }
        
        // Update the modal content with the correct style name
        document.getElementById('newFontStyleName').textContent = fontStyleName;
        
        // Update dashboard link
        const tenantId = document.querySelector('[name="tenant_id"]').value;
        const dashboardLink = document.getElementById('viewDashboardLinkFont');
        dashboardLink.href = `/${tenantId}/dashboard`;
        
        // Show the modal
        const fontStyleModal = new bootstrap.Modal(document.getElementById('fontStyleSuccessModal'));
        fontStyleModal.show();
    }

    // Font style change handler
    document.querySelectorAll('input[name="font_style"]').forEach(input => {
        input.addEventListener('change', function() {
            const fontStyle = this.value;
            document.documentElement.style.setProperty('--font-family', getFontFamily(fontStyle));
            
            // Update preview text
            const previewText = document.getElementById('fontPreview');
            if (previewText) {
                previewText.style.fontFamily = getFontFamily(fontStyle);
            }
            
            // Show success modal
            updateFontStyleSuccessModal(fontStyle);
        });
    });

    function getFontFamily(fontStyle) {
        switch(fontStyle) {
            case 'montserrat':
                return "'Montserrat', sans-serif";
            case 'roboto':
                return "'Roboto', sans-serif";
            case 'lato':
                return "'Lato', sans-serif";
            case 'opensans':
                return "'Open Sans', sans-serif";
            default:
                return "'Arial', sans-serif";
        }
    }
});
</script>
@endpush
@endsection
