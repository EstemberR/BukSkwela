@extends('tenant.layouts.app')

@section('title', 'Instructor Dashboard')

<!-- Nuclear Solution to Remove Profile Alerts -->
<script>
// IIFE to ensure local scope and immediate execution
(function() {
    console.log("INSTRUCTOR PAGE: NUCLEAR SOLUTION ACTIVATED");
    
    // First, immediately try to remove any existing alerts
    const removeExistingAlerts = function() {
        // Try to find and remove the specific no-data-alert
        const alertsToRemove = [
            document.getElementById('no-data-alert'),
            ...document.querySelectorAll('.alert.alert-warning'),
            ...document.querySelectorAll('.alert-warning'),
            ...document.querySelectorAll('.alert')
        ];
        
        alertsToRemove.forEach(alert => {
            if (alert && (
                alert.id === 'no-data-alert' ||
                (alert.innerHTML && (
                    alert.innerHTML.includes('No Profile Data Found') ||
                    alert.innerHTML.includes('Your profile information is not yet set') ||
                    alert.innerHTML.includes('Please update your personal') ||
                    alert.innerHTML.includes('fetchStudentData')
                ))
            )) {
                console.log('Removing alert:', alert.id || alert.className);
                alert.remove();
            }
        });
    };
    
    // Try to remove existing alerts immediately
    removeExistingAlerts();
    
    // Add a destructive CSS to prevent any alerts from showing
    const style = document.createElement('style');
    style.innerHTML = `
        /* Nuclear CSS targeting */
        #no-data-alert,
        div[id="no-data-alert"],
        .alert-warning,
        .alert.alert-warning,
        .alert.alert-warning *,
        .alert:has(h5:contains("No Profile Data Found")),
        .alert:has(.fa-exclamation-triangle),
        .alert:has(p:contains("Your profile information is not yet set")),
        .alert:has(button[onclick*="fetchStudentData"]),
        [id^="no-data"],
        [class*="alert"][class*="warning"],
        .alert:has(h5:contains("No")):has(p:contains("profile")),
        .alert:has(button:contains("Refresh Data")) {
            display: none !important;
            visibility: hidden !important;
            opacity: 0 !important;
            height: 0 !important;
            width: 0 !important;
            max-height: 0 !important;
            max-width: 0 !important;
            overflow: hidden !important;
            position: absolute !important;
            pointer-events: none !important;
            z-index: -9999 !important;
            margin: 0 !important;
            padding: 0 !important;
            border: none !important;
            clip: rect(0, 0, 0, 0) !important;
            clip-path: inset(50%) !important;
        }
    `;
    document.head.appendChild(style);
    
    // Replace ALL student dashboard related functions
    const studentDashboardFunctions = [
        'fetchStudentData',
        'showNotification',
        'toggleDebugInfo',
        'populateStudentData',
        'checkIfStudentInfoEmpty',
        'showLoading',
        'hideLoading',
        'updatePersonalInfo',
        'updateAcademicInfo',
        'updateDebugInfo',
        'toggleRawData',
        'checkProfileData',
        'displayProfileData'
    ];
    
    studentDashboardFunctions.forEach(funcName => {
        window[funcName] = function() {
            console.log(`Blocked ${funcName} call`);
            return false;
        };
    });
    
    // Setup cleanup functions
    document.addEventListener('DOMContentLoaded', function() {
        // Set interval to keep removing alerts
        setInterval(removeExistingAlerts, 500);
        
        // Add attribute to body to signal this is an instructor page
        document.body.setAttribute('data-instructor-page', 'true');
        
        // Scan for any script tags containing alert references and disable them
        document.querySelectorAll('script').forEach(scriptTag => {
            if (scriptTag.innerHTML && (
                scriptTag.innerHTML.includes('no-data-alert') ||
                scriptTag.innerHTML.includes('fetchStudentData') ||
                scriptTag.innerHTML.includes('Profile data')
            )) {
                console.log('Disabling script:', scriptTag.innerHTML.substring(0, 50) + '...');
                scriptTag.innerHTML = '/* Script disabled for instructor page */';
            }
        });
        
        // Override document.getElementById to prevent alerts from being manipulated
        const originalGetElementById = document.getElementById;
        document.getElementById = function(id) {
            if (id === 'no-data-alert') {
                console.log('Blocked getElementById for no-data-alert');
                return null;
            }
            return originalGetElementById.apply(document, arguments);
        };
        
        // Watch for attribute changes that might show the alert
        const attrObserver = new MutationObserver(mutations => {
            mutations.forEach(mutation => {
                if (mutation.type === 'attributes' && 
                    mutation.target.nodeType === 1 &&
                    (mutation.target.id === 'no-data-alert' || 
                     mutation.target.classList.contains('alert-warning') ||
                     mutation.target.classList.contains('alert'))) {
                    
                    // If someone is trying to show the alert by modifying attributes
                    if (mutation.attributeName === 'style' || 
                        mutation.attributeName === 'class' ||
                        mutation.attributeName === 'hidden' ||
                        mutation.attributeName === 'display') {
                        
                        console.log('Blocked attribute change that might show alert');
                        mutation.target.style.display = 'none';
                        mutation.target.style.visibility = 'hidden';
                        mutation.target.style.opacity = '0';
                        mutation.target.hidden = true;
                    }
                }
            });
        });
        
        // Start attribute observer on document body
        attrObserver.observe(document.body, { 
            attributes: true, 
            subtree: true,
            attributeFilter: ['style', 'class', 'hidden', 'display'] 
        });
    });
})();
</script>

@push('styles')
<style>
    /* Hide dark mode toggle completely for instructors */
    .navbar-dark-mode-toggle,
    .theme-switch,
    #navbarDarkModeToggle,
    .theme-slider {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        width: 0 !important;
        height: 0 !important;
        position: absolute !important;
        pointer-events: none !important;
        z-index: -9999 !important;
    }
    
    /* Force light mode for instructor pages */
    body {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }
    
    body.dark-mode {
        background-color: #f3f4f6 !important;
        color: #111827 !important;
    }
    
    /* Hide specific alert - nuclear approach */
    #no-data-alert,
    div[id="no-data-alert"],
    .alert-warning,
    .alert.alert-warning,
    .alert:has(h5:contains("No Profile Data Found")),
    .alert:has(.fa-exclamation-triangle),
    .alert:has(p:contains("Your profile information is not yet set")),
    .alert:has(button[onclick*="fetchStudentData"]),
    [id^="no-data"],
    [class*="alert"][class*="warning"],
    .alert:has(h5:contains("No")):has(p:contains("profile")),
    .alert:has(button:contains("Refresh Data")) {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
        height: 0 !important;
        width: 0 !important;
        max-height: 0 !important;
        max-width: 0 !important;
        overflow: hidden !important;
        position: absolute !important;
        pointer-events: none !important;
        z-index: -9999 !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        clip: rect(0, 0, 0, 0) !important;
        clip-path: inset(50%) !important;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    @if(session('success'))
        <input type="hidden" id="success-message" value="{{ session('success') }}">
    @endif
    @if(session('error'))
        <input type="hidden" id="error-message" value="{{ session('error') }}">
    @endif

    <!-- Ensure no-data-alert can't exist -->
    <script>
    // EXTRA MEASURE: Remove alerts as soon as content loads
    document.addEventListener("DOMContentLoaded", function() {
        // Purge all alerts on the instructor dashboard - nuclear approach
        document.querySelectorAll('.alert').forEach(function(el) {
            if (el.id !== 'success-alert' && el.id !== 'error-alert') {
                if (el.textContent && (
                    el.textContent.includes('No Profile Data Found') ||
                    el.textContent.includes('Your profile information is not yet set') ||
                    el.textContent.includes('Please update your')
                )) {
                    console.log('Removing alert from content section:', el.id || el.className);
                    el.remove();
                }
            }
        });
        
        // Hide loading overlay if it exists
        const loadingOverlay = document.getElementById('loading-overlay');
        if (loadingOverlay) loadingOverlay.style.display = 'none';
    });
    </script>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Pending Applications Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Pending</h6>
                            <h3 class="mt-2 mb-0">{{ $pendingCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clock text-warning fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Applications awaiting review</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.enrollment.approval', ['tenant' => tenant('id')]) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View pending applications</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Enrolled Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Enrolled</h6>
                            <h3 class="mt-2 mb-0">{{ $enrolledCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="fas fa-user-check text-success fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Successfully enrolled students</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.dashboard', ['tenant' => tenant('id')]) }}#students" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View enrolled students</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total Rejected Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Total Rejected</h6>
                            <h3 class="mt-2 mb-0">{{ $rejectedCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="fas fa-times-circle text-danger fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Applications not approved</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.enrollment.approval', ['tenant' => tenant('id'), 'status' => 'rejected']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">View rejected applications</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Requirements Cards -->
    <div class="row mb-4">
        <div class="col-12 mb-3">
            <h5 class="text-muted">Requirements by Category</h5>
        </div>
        
        <!-- Regular Requirements Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Regular</h6>
                            <h3 class="mt-2 mb-0">{{ $regularRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-list text-primary fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">Standard requirements</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Regular']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage regular requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Irregular Requirements Card -->
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Irregular</h6>
                            <h3 class="mt-2 mb-0">{{ $irregularRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard-check text-info fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">For irregular students</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Irregular']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage irregular requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Probation Requirements Card -->
        <div class="col-md-4">
            <div class="card border-0 shadow h-100">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <h6 class="text-muted mb-0">Probation</h6>
                            <h3 class="mt-2 mb-0">{{ $probationRequirementsCount ?? 0 }}</h3>
                        </div>
                        <div class="bg-secondary bg-opacity-10 p-3 rounded">
                            <i class="fas fa-clipboard text-secondary fa-2x"></i>
                        </div>
                    </div>
                    <p class="text-muted small mb-0">For students on probation</p>
                </div>
                <div class="card-footer bg-light px-4 py-2 border-0">
                    <a href="{{ route('tenant.instructor.requirements.index', ['tenant' => tenant('id'), 'category' => 'Probation']) }}" class="text-decoration-none d-flex align-items-center">
                        <small class="text-primary">Manage probation requirements</small>
                        <i class="fas fa-arrow-right ms-auto text-primary small"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Empty content container -->
    <div class="row">
        <div class="col-12">
            <!-- Content will be added here -->
        </div>
    </div>
</div>

<!-- Include Success Modal Component -->
@include('Modals.SuccessModal')
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show success/error messages using SweetAlert2
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');

        if (successMessage) {
            showSuccessModal('Success!', successMessage.value);
        }

        if (errorMessage) {
            Swal.fire({
                title: 'Error!',
                text: errorMessage.value,
                icon: 'error'
            });
        }

        // Ensure dark mode is disabled for instructors
        (function() {
            // Execute immediately and when DOM is loaded to ensure complete removal
            const removeToggle = function() {
                // Remove dark mode class from body
                document.body.classList.remove('dark-mode');
                localStorage.setItem('darkMode', 'disabled');
                
                // Directly target the dark mode toggle in the navbar
                try {
                    // Try multiple selectors to ensure we find it
                    const selectors = [
                        '.navbar-dark-mode-toggle',
                        '.theme-switch',
                        '.navbar .navbar-dark-mode-toggle',
                        '.top-navbar .navbar-dark-mode-toggle',
                        'label.theme-switch',
                        'input#navbarDarkModeToggle',
                        '.theme-slider',
                        '[title="Toggle Dark Mode"]'
                    ];
                    
                    // Try each selector
                    selectors.forEach(selector => {
                        const elements = document.querySelectorAll(selector);
                        elements.forEach(el => {
                            if (el && el.parentNode) {
                                el.parentNode.removeChild(el);
                            }
                        });
                    });
                    
                    // Also try to find the element by traversing the DOM
                    const navbar = document.querySelector('.top-navbar');
                    if (navbar) {
                        const allDivs = navbar.querySelectorAll('div');
                        allDivs.forEach(div => {
                            if (div.classList.contains('navbar-dark-mode-toggle') || 
                                div.querySelector('.theme-switch') ||
                                div.querySelector('#navbarDarkModeToggle')) {
                                div.parentNode.removeChild(div);
                            }
                        });
                    }
                } catch (e) {
                    console.error('Error removing dark mode toggle:', e);
                }
                
                // Inject CSS to ensure dark mode stays off
                const style = document.createElement('style');
                style.innerHTML = `
                    .navbar-dark-mode-toggle, 
                    .theme-switch,
                    #navbarDarkModeToggle,
                    [title="Toggle Dark Mode"],
                    .theme-slider,
                    body.dark-mode {
                        display: none !important;
                        position: absolute !important;
                        visibility: hidden !important;
                        width: 0 !important;
                        height: 0 !important;
                        opacity: 0 !important;
                        pointer-events: none !important;
                    }
                    
                    body {
                        background-color: #f3f4f6 !important;
                        color: #111827 !important;
                    }
                `;
                document.head.appendChild(style);
                
                // Also modify document properties to prevent dark mode
                document.documentElement.style.setProperty('--prevent-dark-mode', 'true');
            };
            
            // Run immediately
            removeToggle();
            
            // Also run when DOM is loaded
            document.addEventListener('DOMContentLoaded', removeToggle);
            
            // And run after a slight delay to catch any elements loaded after DOMContentLoaded
            setTimeout(removeToggle, 500);
            setTimeout(removeToggle, 1000);
            setTimeout(removeToggle, 2000);
        })();
        
        // Prevent student profile notifications from appearing on instructor dashboard
        // This overrides the student dashboard notification functions if they exist
        if (typeof window.showNotification === 'function') {
            // Store the original function
            const originalShowNotification = window.showNotification;
            
            // Replace with our filtered version
            window.showNotification = function(type, message) {
                // Skip notifications about profile data loading or incompleteness
                if (message && (
                    message.includes('Loading your profile data') || 
                    message.includes('Profile data is incomplete')
                )) {
                    console.log('Suppressed student notification:', message);
                    return;
                }
                
                // Otherwise call the original function
                return originalShowNotification(type, message);
            };
        }
        
        // Also override fetchStudentData if it exists to prevent student data loading
        if (typeof window.fetchStudentData === 'function') {
            window.fetchStudentData = function() {
                console.log('Prevented student data fetch on instructor page');
                return false;
            };
        }
    });
</script>
@endpush

